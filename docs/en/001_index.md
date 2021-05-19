# Documentation

## Background

We send a lot of emails and we'd prefer not to spend our time testing email templates in all devices. Our goal is to deliver easy-to-digest information for citizens without the bells and whistles.

This module extends Silverstripe's HTML email capabilities to provide a structured template rendering method prior to email send.

This module handles the templating of the email, leaving you to figure out what to put in the email.

We add:

+ Standard, proven email templates based on https://github.com/wildbit/postmark-templates
+ A Structured Email class
+ An Injectable email decorator containing standard CSS values
+ Structured template parts allowing you to override as required


## Example usage

```php

// Your custom HTML body
$html = ArrayData::create([
    'Name' => $name,
    'CallToAction' => $link
])->renderWith('My/Template');

// Provide some data
$data = [
    'Body' => $html,// your email HTML
    'EmailPreHeader' => 'Please follow the instructions in this email',
    'EmailMasthead' => 'Welcome to Service',
    'EmailMastheadLink' => 'https://service.example.com/welcome',
    'EmailPhysical' => $html_physical_address
];

// Create a structured Email
$email = StructuredEmail::create();
$email->setTo("to@example.com");
$email->setCc("cc@example.com");
$email->setBcc("bcc@example.com");
$email->setFrom("from@example.com");
$email->setData($data);
$email->setHTMLTemplate("NSWDPC/StructuredEmail/Email");

// StructuredEmail extends Email, so you get standard Silverstripe email methods
// $email->render();
// $body = $email->getBody();

// Send with your configured mailer
$result = $email->send();
```

### Set your own decorator

> A decorator allows you to set CSS values in the template

```php
$email->setDecorator($decorator)->send();
```

### Set the layout type

> A layout type allows you control over the HTML width
> basic-full, basic, plain

```php
$decorator->setLayoutType('basic-full');
$email->setDecorator($decorator)->send();
```

## Template override

You can override any Include (or the main Email.ss template) in your theme, per the standard Silverstripe template priority handling

Provide your own e-mail footer:
```shell
themes/mytheme/templates/NSWDPC/StructuredEmail/Includes/Footer.ss
```


## Planned features

+ Schema.org support
+ Minification
+ Automated text/plain content creation
