<?php

namespace NSWDPC\StructuredEmail;

use League\HTMLToMarkdown\HtmlConverter;
use League\HTMLToMarkdown\Converter\TableConverter;
use NSWDPC\Messaging\Taggable\TaggableEmail;
use SilverStripe\Control\Email\Email;
use SilverStripe\Control\Email\Mailer;
use SilverStripe\Control\HTTP;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;
use SilverStripe\View\Requirements;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Action;
use Spatie\SchemaOrg\Contracts\ActionContract;

/**
 * Provides a more structured/restricted way to send emails out of a Silverstripe instance
 * Email content will be pre-processed and rendered into the StructuredEmail template
 *
 * Custom parameters:
 * StructuredEmail supports custom parameters
 * but it is up to your configured transport to handle the custom parameters
 * e.g if custom parameters are added as mail headers, these can be added at the
 * onMessage event of a EventSubscriberInterface
 *
 * EmailSchema
 * StructuredEmail has basic EmailMessage schema.org support.
 * See getEmailSchema. setAction/getAction provide the ability to set a potentialAction
 *
 * PreHeader
 * Use setPreHeader to apply preheader text to your emails
 * See: https://postmarkapp.com/support/article/1220-adding-preheader-text-to-your-messages
 *
 * @author James
 *
 */
class StructuredEmail extends TaggableEmail implements EmailWithCustomParameters
{
    /**
     * Allow configuration via API
     */
    use Configurable;

    /**
     * Custom parameters for the mailer, if it is supported
     */
    use CustomParameters;

    private static bool $is_structured = true;

    /**
     * Process HTML with DOMDocument
     */
    public const HTML_CLEANER_DOMDOCUMENT = 'DOMDocument';

    /**
     * Process HTML with strip_tags
     */
    public const HTML_CLEANER_STRIPTAGS = 'strip_tags';

    /**
     * Process HTML with tidy
     */
    public const HTML_CLEANER_TIDY = 'tidy';

    /**
     * Used to retrieve the contents of a <body> from provided templates
     * The default HTML document cleaner is tidy
     * if not found or installed, strip_tags will be used
     */
    private static string $html_cleaner = self::HTML_CLEANER_TIDY;

    private ?\NSWDPC\StructuredEmail\AbstractDecorator $decorator = null;

    private string $email_template = "NSWDPC/StructuredEmail/StructuredEmail";

    /**
     * @var string
     */
    protected $pre_header = '';

    /**
     * @var ActionContract|null
     */
    protected $email_message_action;

    /**
     * @inheritdoc
     */
    public function __construct(
        string|array $from = '',
        string|array $to = '',
        string $subject = '',
        string $body = '',
        string|array $cc = '',
        string|array $bcc = '',
        string $returnPath = ''
    ) {
        parent::__construct($from, $to, $subject, $body, $cc, $bcc, $returnPath);
        // by default set this template
        parent::setHTMLTemplate($this->email_template);
    }

    /**
     * Set a decorator class for this email
     */
    public function setDecorator(AbstractDecorator $decorator): self
    {
        $this->decorator = $decorator;
        return $this;
    }

    /**
     * The decorator in place for this email
     */
    public function getDecorator(): AbstractDecorator
    {
        return $this->decorator instanceof \NSWDPC\StructuredEmail\AbstractDecorator ? $this->decorator : Injector::inst()->get(Decorator::class);
    }

    /**
     * Rendered the HTML of the email into the StructuredEmail template
     * This is called from the MailerSubscriberExtension
     */
    public function renderIntoStructuredEmail(): static
    {

        try {

            Requirements::clear();

            // Allow opt-out via configuration
            if(!$this->config()->get('is_structured')) {
                return $this;
            }

            // The original HTML template for the email
            $htmlTemplate = $this->getHTMLTemplate();

            // Apply the preheaderer
            $this->applyPreheader($htmlTemplate);

            /** @var resource|string|null $html an existing body of the email */
            $html = $this->getHtmlBody();
            /**
             * clean the HTML, removing everything that cannot go in a body tag
             * the structured email template renders the cleaned html into
             * a complete template
             */
            $cleanedHtml = $this->cleanHTMLDocument($html);

            // email data
            $data = $this->getData();

            // override this email's data with the rendered template
            $this->addData('Body', $cleanedHtml);

            // add the Email decorator
            $this->addData('EmailDecorator', $this->getDecorator());

            // add the EmailSchema
            $this->addData('EmailSchema', $this->getEmailSchema());

            // ensure a preheader is set, even if an empty string but if not already set
            if ((is_array($data) && !isset($data['Preheader']))
                || (($data instanceof ViewableData) && !$data->hasField('Preheader'))) {
                $this->addData('Preheader', $this->getPreheader());
            }

            // update HTML of email by rendering it into the StructuredEmail template
            $this->setHTMLTemplate($this->email_template);
            $renderedHtml = HTTP::absoluteURLs($this->getData()->renderWith($this->email_template)->RAW());
            // print "<pre>";print htmlspecialchars($renderedHtml);print "</pre>";exit;
            $this->html($renderedHtml);

            try {
                // update text body of email, the HTML is converted to markdown
                // create the converter
                $converter = new HtmlConverter([
                    'strip_tags' => true,
                    'remove_nodes' => 'head style script link'
                ]);
                $converter->getEnvironment()->addConverter(new TableConverter());
                // $bodyPart = HTTP::absoluteURLs($bodyPart);
                $markdown = $converter->convert($cleanedHtml);
                // set the text part of the email
                $this->text($markdown);
            } catch (\Exception) {
                // failed to convert!
            }

            return $this;

        } catch (\Exception $e) {

        } finally {
            Requirements::restore();
        }

    }

    /**
     * Use the configured html_cleaner to get the body contents of the provided HTML
     * @param resource|string|null $html
     */
    private function cleanHTMLDocument($html): string
    {
        try {

            if(is_null($html)) {
                return "";
            }

            if(is_resource($html)) {
                // at the moment, not handling resource
                return "";
            }

            // clear out all newlines to avoid nl2br fun
            $html = str_replace(["\n","\r"], "", $html);

            // Sneaky check if no <body occurs in the document - bail if so
            if (!str_contains($html, "<body")) {
                return $html;
            }

            $cleaner = $this->config()->get('html_cleaner');

            if ($cleaner == self::HTML_CLEANER_DOMDOCUMENT) {
                if (class_exists('DOMDocument')) {
                    // Use DOMDocument to strip out everything but the contents of <body>
                    libxml_use_internal_errors(true);
                    $dom = new \DOMDocument();
                    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
                    /* @var \DOMNode */
                    $body = $dom->getElementsByTagName('body')->item(0);
                    if (!$body instanceof \DOMNode) {
                        throw new \Exception("Failed to find body in document");
                    }

                    // create an element to hold body child nodes
                    $element = $dom->createElement('div');
                    foreach ($body->childNodes as $node) {
                        if ($node instanceof \DOMElement) {
                            $element->appendChild($node);
                        }
                    }

                    $result = $dom->saveHTML($element);
                    $result .= "<!-- dd -->";
                    libxml_clear_errors();
                    return $result;
                }
            } elseif ($cleaner == self::HTML_CLEANER_TIDY) {
                if (class_exists('tidy')) {
                    // Use tidy to strip out everything but the contents of <body>
                    $tidy = new \tidy();
                    $html = $tidy->repairString(
                        $html,
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
                    return $html . "<!-- tidy -->";
                }
            }
        } catch (\Exception) {
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
        return $html . "<!-- st -->";
    }

    /**
     * Apply pre header based on template name
     * We attempt to set a pre header if not already set for common templates
     * @see https://postmarkapp.com/support/article/1220-adding-preheader-text-to-your-messages
     * @return void
     */
    protected function applyPreheader(string $template)
    {
        $preHeader = $this->getPreheader();
        if ($preHeader !== '') {
            switch ($template) {
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
     */
    public function setPreHeader(string $value): self
    {
        $this->pre_header = $value;
        return $this;
    }

    /**
     * Return the preheader
     */
    public function getPreheader(): string
    {
        if ($this->pre_header) {
            return _t('StructuredEmail.PREHEADER', $this->pre_header);
        } else {
            return '';
        }
    }

    /**
     * In your email template, use $EmailSchema.RAW within a script ld+json tag
     * Return the Schema.org script for this message
     * @return DBHTMLText|false
     */
    public function getEmailSchema()
    {
        try {

            // create base schema
            $emailMessage = Schema::emailMessage();

            // about
            if ($subject = $this->getSubject()) {
                $emailMessage->about(Schema::thing()->name($subject));
            }

            // abstract
            if (($abstract = $this->getPreheader()) !== '') {
                $emailMessage->abstract($abstract);
            }

            // add potentialAction on
            if (($action = $this->getAction()) instanceof \Spatie\SchemaOrg\Contracts\ActionContract) {
                $emailMessage->potentialAction($action);
            }

            $script = $emailMessage->toScript();

            $html = DBField::create_field(
                DBHTMLText::class,
                $script
            );
        } catch (\Exception) {
            // on error, do not return a schema.org snippet
            $html = false;
        }

        return $html;
    }

    /**
     * The action must implement {@link https://github.com/spatie/schema-org/blob/master/src/Contracts/ActionContract.php}
     */
    public function getAction(): ?ActionContract
    {
        return $this->email_message_action;
    }

    /**
     * Set a potentialAction from schema.org actions
     * @see https://schema.org/potentialAction
     */
    public function setAction(?ActionContract $action): static
    {
        $this->email_message_action = $action;
        return $this;
    }

    /**
     * A common type of action is a ViewAction
     */
    public function setViewAction($name, $url): self
    {
        $action = Schema::viewAction()
            ->name($name)
            ->url($url);
        $this->email_message_action = $action;
        return $this;
    }
}
