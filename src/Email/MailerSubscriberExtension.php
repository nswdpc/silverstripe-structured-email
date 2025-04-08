<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use Symfony\Component\Mailer\Event\MessageEvent;

/**
 * Extension for \SilverStripe\Control\Email\MailerSubscriber to handle onMessage events
 * When configured (the default) HTML and Text parts are re-rendered into
 * the Structured email templates via renderIntoStructuredEmail
 *
 * This extension is automatically enabled when the module is installed
 */
class MailerSubscriberExtension extends Extension
{
    public function updateOnMessage(Email $email, MessageEvent $event)
    {
        if (Config::inst()->get(StructuredEmailProcessor::class, 'is_structured')) {
            $data = $email->getData();
            $processor = $data->StructuredEmailProcessor ?? null;
            if (!$processor) {
                // use the default if not set
                $processor = StructuredEmailProcessor::create($email);
            }
            $processor->renderIntoStructuredEmail();
        }
    }

}
