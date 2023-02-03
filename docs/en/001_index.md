# Documentation

## Background

We send quite a few emails and we'd prefer not to spend our time testing email templates in all devices. Our goal is to deliver easy-to-digest information for citizens without the bells and whistles.

This module extends Silverstripe's HTML email capabilities to provide a structured template rendering method prior to email send.

This module handles the templating of the email, leaving you to figure out what to put in the email.

We add:

+ Standard, proven email templates based on https://github.com/ActiveCampaign/postmark-templates
+ A Structured Email class
+ An Injectable email decorator containing standard CSS values
+ Structured template parts allowing you to override as required
+ 🧫 Automated text/plain content creation (as markdown)

### Planned features

+ ⚠️ 🧫 improved Schema.org support

### Stretch goals

+ Template caching and minification
+ Handlbars output for Mailer services eg. Mailgun (PR welcome)
