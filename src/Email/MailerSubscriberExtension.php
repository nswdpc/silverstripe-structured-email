<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Extension;
use Symfony\Component\Mailer\Event\MessageEvent;

/**
 * Extension for \SilverStripe\Control\Email\MailerSubscriber to handle onMessage events
 * For emails sent via StructuredEmail, the HTML and Text parts are re-rendered into
 * The Structured email templates via renderIntoStructuredEmail
 */
class MailerSubscriberExtension extends Extension
{

    public function updateOnMessage(Email $email, MessageEvent $event)
    {
        if($email instanceof StructuredEmail) {
            $email->renderIntoStructuredEmail();
        } else {
            Logger::log("Cannot use structured email handling, as provide email is not a StructuredEmail instance", "INFO");
        }
    }

}
