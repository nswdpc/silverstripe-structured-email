<?php

namespace NSWDPC\StructuredEmail;

use League\HTMLToMarkdown\HtmlConverter;
use League\HTMLToMarkdown\Converter\TableConverter;
use SilverStripe\Control\Email\Email;
use SilverStripe\Control\Email\Mailer;
use SilverStripe\Control\HTTP;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;
use SilverStripe\View\Requirements;

/**
 * Convert standard email template requests to structured email
 */
class StructuredEmail extends Email {

    use Configurable;

    /**
     * @var bool
     */
    private static $is_structured = true;

    /**
     * @var NSWDPC\StructuredEmail\AbstractDecorator
     **/
    private $decorator = null;

    /**
     * @var string
     */
    private $email_template = "NSWDPC/StructuredEmail/StructuredEmail";

    /**
     * @var string
     */
    protected $pre_header = '';

    /**
     * @inheritdoc
     */
    public function __construct(
        $from = null,
        $to = null,
        $subject = null,
        $body = null,
        $cc = null,
        $bcc = null,
        $returnPath = null
    ) {
        parent::__construct($from, $to, $subject, $body, $cc, $bcc, $returnPath);
        // by default set this template
        parent::setHTMLTemplate( $this->email_template );
    }

    /**
     * @return self
     */
    public function setDecorator(AbstractDecorator $decorator) : self {
        $this->decorator = $decorator;
        return $this;
    }

    /**
     * @return AbstractDecorator
     */
    public function getDecorator() : AbstractDecorator {
        return $this->decorator ? $this->decorator : Injector::inst()->get(Decorator::class);
    }

    /**
     * Rendered the data provided into a into the structured email template
     */
    protected function renderIntoStructured(string $template) {

        // check if a body is set, if so use that
        $body = $this->getBody();
        if(!$body) {
            // email called with data and a template
            // render data into that template
            $body = ViewableData::create()->renderWith($template, $this->getData());
        }

        // TODO check if $body is a complete HTML document and strip chrome out

        // clear all data on this email
        $this->setData([]);
        // override this email's data with the rendered template
        $this->addData('Body', $body);
        // ensure a preheader is set, even if an empty string but if not already set
        if(!isset($this->data['Preheader'])) {
            $this->addData('Preheader', $this->getPreheader());
        }

        // clear the body
        $body = '';
        $this->setBody($body);

        // ensure the email uses the
        $this->setHTMLTemplate($this->email_template);

        return $this;
    }

    /**
     * @inheritdoc
     * Override sending handling to render email into structured email template
     */
    public function send()
    {
        // Allow opt-out via configuration
        if(!$this->config()->get('is_structured')) {
            return parent::send();
        }

        // Check template in use
        $template = $this->getHTMLTemplate();
        // if using the structured email template, don't re-render
        if($template != $this->email_template) {
            // render into the structured email template
            $this->renderIntoStructured($template);
        }

        // render document
        $this->render();

        // send
        return Injector::inst()->get(Mailer::class)->send($this);
    }

    /**
     * @inheritdoc
     */
    public function sendPlain()
    {
        // Allow opt-out via configuration
        if(!$this->config()->get('is_structured')) {
            return parent::sendPlain();
        }

        // render the email into the structured email template
        $this->renderIntoStructured();

        // only render the plain part
        $this->render(true);

        // send
        return Injector::inst()->get(Mailer::class)->send($this);
    }

    /**
     * @inheritdoc
     * Apply custom fields and data plus email decorator for templates
     */
    public function renderWith($template, $customFields = null)
    {

        // Allow opt-out via configuration
        if(!$this->config()->get('is_structured')) {
            return parent::renderWith($template, $customFields);
        }

        // Base configurable data for all emails
        $data = [];
        if(!$customFields instanceof ViewableData) {
            if(is_array($customFields)) {
                // merge custom fields on top of base data to allow override
                $data = array_merge($data, $customFields);
            }
            // ensure it is a viewable data
            $customFields = ViewableData::create();
        }

        foreach($data as $field => $value) {
            $customFields->setField($field, $value);
        }

        $customFields->setField( 'EmailDecorator', $this->getDecorator());

        return parent::renderWith($template, $customFields);

    }

    /**
     * @return bool
     * Plain part is always created in render()
     */
    public function hasPlainPart()
    {
        // Allow opt-out via configuration
        if(!$this->config()->get('is_structured')) {
            return parent::hasPlainPart();
        }
        return true;
    }

    /**
     * @inheritdoc
     * Override core rendering to ONLY render the HTML part and allow the plain part
     * to be added just prior to send
     */
    public function render($plainOnly = false) {

        // Allow opt-out via configuration
        if(!$this->config()->get('is_structured')) {
            return parent::render($plainOnly);
        }

        // Do not interfere with emails styles
        Requirements::clear();

        // the email body, pre-rendered
        $bodyPart = isset($this->data['Body']) ? strval($this->data['Body']) : '';

        // Create the HTML document
        $htmlTemplate = $this->getHTMLTemplate();
        // Render into the structured email template
        $htmlPart = $this->renderWith($htmlTemplate, $this->getData());

        // Rendering is finished
        Requirements::restore();

        // handle specific common template sections
        $htmlTemplate = str_replace('\\', '/', $htmlTemplate);
        $preHeader = $this->getPreheader();
        if(!$preHeader) {
            switch($htmlTemplate) {
                case 'SilverStripe/Control/Email/ForgotPasswordEmail':
                    $this->setPreheader(
                        _t(
                            'StructuredEmail.FORGOT_PASSWORD_PREHEADER',
                            'Your password reset link'
                        )
                    );
                    break;
                case 'SilverStripe/Control/Email/ChangePasswordEmail':
                    $this->setPreheader(
                        _t(
                            'StructuredEmail.CHANGED_PASSWORD_PREHEADER',
                            'Your password was changed'
                        )
                    );
                    break;
            }
        }

        if(!$plainOnly) {
            // Build HTML / Plain components
            $this->setBody($htmlPart);
            $this->getSwiftMessage()->setContentType('text/html');
            $this->getSwiftMessage()->setCharset('utf-8');
        }

        try {
            // create the converter
            $converter = new HtmlConverter([
                'strip_tags' => true,
                'remove_nodes' => 'head style script'
            ]);
            $converter->getEnvironment()->addConverter(new TableConverter());
            $bodyPart = HTTP::absoluteURLs($bodyPart);
            $markdown = $converter->convert($bodyPart);
            // create the text/plain part
            $this->getSwiftMessage()->addPart(
                $markdown,
                'text/plain',
                'utf-8'
            );
        } catch (\Exception $e) {
            // failed to convert!
        }
        return $this;
    }

    /**
     * Set the preheader for this specific email
     * @see https://postmarkapp.com/support/article/1220-adding-preheader-text-to-your-messages
     * @param string
     */
    public function setPreHeader(string $value) {
        $this->pre_header = $value;
    }

    /**
     * Return the preheader
     * @return string
     */
     public function getPreheader() : string {
        if($this->pre_header) {
            return _t('StructuredEmail.PREHEADER', $this->pre_header);
        } else {
            return '';
        }
    }
}
