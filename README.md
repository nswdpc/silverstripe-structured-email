## Structured email handling for Silverstripe

The goal of this module is to produce simple and easy-to-digest emails ([more](./docs/en/001_index.md)). The templates used are based on the [Postmark Transactional Email Templates](https://github.com/ActiveCampaign/postmark-templates).

> :warning: This module is under active development and shouldn't be used in production just yet as there are probably bugs. Test results, pull requests and feedback are welcome.

The `StructuredEmail` extends the `Email` class. You can use it standalone or you may wish to inject StructuredEmail as the Email class for your project using `Injector`.

For specific core emails, it will attempt to detect the purpose of the email based on the template name:

+ `SilverStripe/Control/Email/ForgotPasswordEmail` - the forgot password email
+ `SilverStripe/Control/Email/ChangePasswordEmail` - the changed password email

TODO:

+ `SilverStripe/MFA/Email/*` - MFA emails
+ `SilverStripe/ContentReview/*` - Content review emails
+ `SubmittedFormEmail` - user defined form generic email
+ `SubmittedFormEmailPlain` - user defined form generic email (plain text)

### Existing templates

If the class encounters a complete HTML document in the email, it will use HTML contained within the `<body>` tag as the email content.

## Schema.org

[Reference](./docs/en/004_schemaorg.md)

## Resources

+ E-mail support across clients: https://www.caniemail.com
+ Postmark templates: https://github.com/ActiveCampaign/postmark-templates

## Quick example

For finer grain control, use `StructuredEmail` directly.

See [further documentation](./docs/en/001_index.md)
```php
<?php
// Your custom HTML body
$html = ArrayData::create([
    'Name' => $name,
    'CallToAction' => $link
])->renderWith('My/Template');

$data = [
    'Body' => $html// Your email HTML
];

$email = StructuredEmail::create();
$email->setTo(["to@example.com", "To name"]);
$email->setFrom(["from@example.com" => "From name"]);
$email->setData($data);
// will automatically pick up StructuredEmail.ss as the template
$email->send();
```

Emails are decorated using a standard, basic colour palette from the NSW Design System. [You can provide your own decorator](./docs/en/003_decorator.md).

## Installation

The only supported way of installing this module is via [composer](https://getcomposer.org/download/)

```shell
composer require nswdpc/silverstripe-structured-email
```

## License

[BSD-3-Clause](./LICENSE.md)


## Maintainers

+ [dpcdigital@NSWDPC:~$](https://dpc.nsw.gov.au)

The source of the HTML email templates is the [Postmark templates project](https://github.com/ActiveCampaign/postmark-templates)

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
