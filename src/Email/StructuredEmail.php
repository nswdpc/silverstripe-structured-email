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
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Action;
use Spatie\SchemaOrg\Contracts\ActionContract;

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
     * Process HTML with DOMDocument
     */
    const HTML_CLEANER_DOMDOCUMENT = 'DOMDocument';
    /**
     * Process HTML with strip_tags
     */
    const HTML_CLEANER_STRIPTAGS = 'strip_tags';
    /**
     * Process HTML with tidy
     */
    const HTML_CLEANER_TIDY = 'tidy';

    /**
     * Used to retrieve the contents of a <body> from provided templates
     * The default HTML document cleaner is tidy
     * if not found or installed, strip_tags will be used
     * @var string
     */
    private static $html_cleaner = self::HTML_CLEANER_TIDY;

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
     * @var Action|null
     */
    protected $email_message_action = null;

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
    protected function renderIntoStructuredEmail(string $template) {

        Requirements::clear();

        // check if a body is set, if so use that
        $body = $this->getBody();

        if(!$body) {
            // email called with data and a template
            // render data into that template
            $body = ViewableData::create()->renderWith($template, $this->getData());
        }

        // clean the HTML, removing everything that cannot go in a body tag
        $body = $this->cleanHTMLDocument($body);

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

        Requirements::restore();

        return $this;
    }

    /**
     * Use the configured html_cleaner to get the body contents of the provided HTML
     */
    private function cleanHTMLDocument(string $html) : string {

        try {

            // it's possible the HTML is entitised, or partially so
            $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
            // clear out all newlines to avoid nl2br fun
            $html = str_replace(["\n","\r"], "", $html);

            // Sneaky check if no <body occurs in the document - bail if so
            if(strpos($html, "<body") === false) {
                return $html;
            }

            $cleaner = $this->config()->get('html_cleaner');

            if($cleaner == self::HTML_CLEANER_DOMDOCUMENT) {

                if(class_exists('DOMDocument')) {
                    // Use DOMDocument to strip out everything but the contents of <body>
                    libxml_use_internal_errors(true);
                    $dom = new \DOMDocument();
                    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
                    /* @var \DOMNode */
                    $body = $dom->getElementsByTagName('body')->item(0);
                    if(!$body instanceof \DOMNode) {
                        throw new \Exception("Failed to find body in document");
                    }
                    // create an element to hold body child nodes
                    $element = $dom->createElement('div');
                    foreach($body->childNodes as $node) {
                        if($node instanceof \DOMElement) {
                            $element->appendChild($node);
                        }
                    }
                    $result = $dom->saveHTML($element);
                    $result .= "<!-- dd -->";
                    libxml_clear_errors();
                    return $result;
                }

            } else if($cleaner == self::HTML_CLEANER_TIDY) {

                if(class_exists('tidy')) {
                    // Use tidy to strip out everything but the contents of <body>
                    $tidy = new \tidy();
                    $html = $tidy->repairString($html,
                        [
                            'indent' =>  true,
                            'indent-spaces' => 4,
                            'output-html' => true,
                            'merge-divs' => false,
                            'merge-spans' => false,
                            'tab-size' => 4,
                            'show-body-only' => true,
                            'output-encoding' => 'utf-8',
                            'input-encoding'=> 'utf-8',
                            'output-bom' => false
                        ],
                        'utf8'
                    );
                    $html .= "<!-- tidy -->";
                    return $html;
                }

            }
        } catch (\Exception $e) {
            // NOOP on error - but failover to the default
        }

        $html = strip_tags(
            $html,
            '<div><p><pre><blockquote><img>'
            . '<br>'
            . '<h1><h2><h3><h4><h5><h6>'
            . '<ul><li><ol><dl><dt><dd>'
            . '<strong><em><a><span><i><b><code><cite>'
            . '<table><th><td><tr><caption><thead><tbody><tfoot>'
        );
        $html .= "<!-- st -->";
        return $html;
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
            $this->renderIntoStructuredEmail($template);
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
        $this->renderIntoStructuredEmail();

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
        // apply preheader if not set, based on template name
        $this->applyPreheader($htmlTemplate);

        // handle adding HTML part
        if(!$plainOnly) {
            // Build HTML / Plain components
            $this->setBody($htmlPart);
            $this->getSwiftMessage()->setContentType('text/html');
            $this->getSwiftMessage()->setCharset('utf-8');
        }

        // convert the html to plain text (markdown)
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
     * Apply pre header based on template name
     * We attempt to set a pre header if not already set for common templates
     * @return void
     */
    protected function applyPreheader(string $template) {
        $preHeader = $this->getPreheader();
        if(!$preHeader) {
            switch($template) {
                case 'SilverStripe/Control/Email/ForgotPasswordEmail':
                    $this->setPreHeader(
                        _t(
                            'StructuredEmail.FORGOT_PASSWORD_PREHEADER',
                            'Your password reset link'
                        )
                    );
                    break;
                case 'SilverStripe/Control/Email/ChangePasswordEmail':
                    $this->setPreHeader(
                        _t(
                            'StructuredEmail.CHANGED_PASSWORD_PREHEADER',
                            'Your password was changed'
                        )
                    );
                    break;
            }
        }
    }

    /**
     * Set the preheader for this specific email
     * @see https://postmarkapp.com/support/article/1220-adding-preheader-text-to-your-messages
     * @param string
     */
    public function setPreHeader(string $value) : self {
        $this->pre_header = $value;
        return $this;
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

    /**
     * In your email template, use $Schema.RAW within a script ld+json tag
     * Return the Schema.org script for this message
     */
    public function getSchema() : string {

        // create base schema
        $emailMessage = Schema::emailMessage();

        // about
        if($subject = $this->getSubject() ) {
            $emailMessage->about( Schema::thing()->name( $subject ) );
        }

        // abstract
        if($abstract = $this->getPreheader()) {
            $emailMessage->abstract( $abstract );
        }

        // add potentialAction on
        if($action = $this->getAction()) {
            $emailMessage->action( $action );
        }

        return $emailMessage->toScript();

    }

    /**
     * @return ActionContract|null
     * The action must implement {@link https://github.com/spatie/schema-org/blob/master/src/Contracts/ActionContract.php}
     */
    public function getAction() {
        return $this->email_message_action;
    }

    /**
     * Set a potentialAction from schema.org actions
     * @see https://schema.org/potentialAction
     */
    public function setAction(ActionContract $action) {
        $this->email_message_action = $action;
        return $this;
    }

    /**
     * A common type of action is a ViewAction
     */
    public function setViewAction($name, $url) : self {
        $action = Schema::viewAction()
            ->name($name)
            ->url($url);
        $this->email_message_action = $action;
        return $this;
    }
}
