# Documentation

## Background

This module extends Silverstripe's HTML email capabilities to render a message into a single template prior to email send.

It adds:

+ Standard, proven email templates based on https://github.com/ActiveCampaign/postmark-templates
+ An Injectable email decorator for setting standard CSS values via configuration
+ Structured template parts allowing you to override at the project level, as required
+ Automated text/plain content creation (as markdown)

### Planned features

+ ⚠️ 🧫 improved Schema.org support

### Stretch goals

+ Template caching and minification
+ Handlbars output for Mailer services eg. Mailgun (PR welcome)

## Customisation

Customisation is available per-email by creating a StructuredEmailProcessor and setting that as data on the Email instance:

```php
<?php
// create an email
$email = Email::create();
// create a processor instance, with the email as the argument
$processor = StructuredEmailProcessor::create($email);
// call methods on the processor
// attach this processor as data to its email
$email->setData('StructuredEmailProcessor', $processor);
```
