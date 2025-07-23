<?php

namespace NSWDPC\StructuredEmail;

use League\HTMLToMarkdown\HtmlConverter;
use League\HTMLToMarkdown\Converter\TableConverter;
use SilverStripe\Control\Email\Email;
use SilverStripe\Control\HTTP;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use SilverStripe\View\ViewableData;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Action;
use Spatie\SchemaOrg\Contracts\ActionContract;

/**
 * Process all emails via a single template
 *
 * @author James
 *
 */
class StructuredEmailProcessor extends \SilverStripe\Model\ModelData
{
    use Injectable;

    use Configurable;

    /**
     * Allow projects to opt out of structured email processing
     */
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
     * The email decorator, which holds style rules
     */
    protected ?\NSWDPC\StructuredEmail\AbstractDecorator $decorator = null;

    /**
     * The default structured email template
     */
    private static string $email_template = "NSWDPC/StructuredEmail/StructuredEmail";

    /**
     * Used to retrieve the contents of a <body> from provided templates
     * The default HTML document cleaner is tidy
     * if not found or installed, strip_tags will be used
     */
    private static string $html_cleaner = self::HTML_CLEANER_TIDY;

    protected string $preHeader = '';

    protected ?ActionContract $emailMessageAction = null;

    public function __construct(protected ?\SilverStripe\Control\Email\Email $email)
    {
    }

    /**
     * This class representation in a template is an empty string
     */
    #[\Override]
    public function forTemplate(): string
    {
        return '';
    }

    /**
     * return the configured email template, or the default if not set
     */
    public static function getEmailTemplate(): string
    {
        return static::config()->get('email_template') ?? "NSWDPC/StructuredEmail/StructuredEmail";
    }

    /**
     * Return the Email instance
     */
    public function getEmail(): \SilverStripe\Control\Email\Email
    {
        return $this->email;
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
            if (!static::config()->get('is_structured')) {
                return $this;
            }

            // The original HTML template for the email
            $htmlTemplate = $this->email->getHTMLTemplate();

            // Apply the preheaderer
            $this->applyPreheader($htmlTemplate);

            /** @var resource|string|null $html an existing body of the email */
            $html = $this->email->getHtmlBody();
            /**
             * clean the HTML, removing everything that cannot go in a body tag
             * the structured email template renders the cleaned html into
             * a complete template
             */
            $cleanedHtml = $this->cleanHTMLDocument($html);

            // email data
            $data = $this->email->getData();

            // override this email's data with the rendered template
            $this->email->addData('Body', $cleanedHtml);

            // add the Email decorator
            $this->email->addData('EmailDecorator', $this->getDecorator());

            // add the EmailSchema
            $this->email->addData('EmailSchema', $this->getEmailSchema());

            // ensure a preheader is set, even if an empty string but if not already set
            if (!$data->hasField('Preheader')) {
                $this->email->addData('Preheader', $this->getPreheader());
            }

            // update HTML of email by rendering it into the StructuredEmail template
            $this->email->setHTMLTemplate(static::getEmailTemplate());
            $renderedHtml = HTTP::absoluteURLs($this->email->getData()->renderWith(static::getEmailTemplate())->RAW());
            // print "<pre>";print htmlspecialchars($renderedHtml);print "</pre>";exit;
            $this->email->html($renderedHtml);

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
                $this->email->text($markdown);
            } catch (\Exception) {
                // failed to convert!
            }

            return $this;

        } catch (\Exception) {

        } finally {
            Requirements::restore();
        }

        return $this;

    }

    /**
     * Use the configured html_cleaner to get the body contents of the provided HTML
     * @param resource|string|null $html
     */
    private function cleanHTMLDocument($html): string
    {
        try {

            if (is_null($html)) {
                return "";
            }

            if (is_resource($html)) {
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
                    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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
                            'input-encoding' => 'utf-8',
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
        $this->preHeader = $value;
        return $this;
    }

    /**
     * Return the preheader
     */
    public function getPreheader(): string
    {
        return $this->preHeader;
    }

    /**
     * In your email template, use $EmailSchema.RAW within a script ld+json tag
     * Return the Schema.org script for this message
     * @return DBHTMLText|false
     */
    public function getEmailSchema(): \SilverStripe\ORM\FieldType\DBField|false
    {
        try {

            // create base schema
            $emailMessage = Schema::emailMessage();

            // about
            $subject = $this->email->getSubject();
            if (is_string($subject) && $subject !== '') {
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
        return $this->emailMessageAction;
    }

    /**
     * Set a potentialAction from schema.org actions
     * @see https://schema.org/potentialAction
     */
    public function setAction(?ActionContract $action): static
    {
        $this->emailMessageAction = $action;
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
        $this->emailMessageAction = $action;
        return $this;
    }
}
