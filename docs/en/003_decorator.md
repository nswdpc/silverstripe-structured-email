### Decorating emails

A decorator allows you to set CSS values in the template via configuration API.

```yml
NSWDPC\StructuredEmail\Decorator:
  decorations:
    BackgroundColor: '#eeeeee'
```

You can also provide your own decorator that extends the default decorator:

```php
<?php
namespace Some\Project;

use NSWDPC\StructuredEmail\Decorator;

class MyDecorator extends Decorator {

    /**
     * @var string
     */
    private static $type = 'basic';

    /**
     * @var array
     */
    private static $decorations = [
        'FontFamily' => "'Font',Arial,sans-serif",
        'FontSize' => '1em',
        'BodyBackgroundColor' => '#eeeeee',
        'BackgroundColor' => '#ffffff',
        'Color' => '#222555',
        'SubColor' => '#555555',
        'HeadingColor' => '#010101',
        'PrimaryColor' => '#5500ff',
        'PrimaryTextColor' => '#ffffff',
        'HighlightColor' => '#aa55ff',
        'SecondaryColor' => '#e0b9ff'
    ];
}
```

Then inject your own decorator in place of `NSWDPC\StructuredEmail\Decorator` using Injector configuration.

```yaml
---
Name: 'project-emails'
---
SilverStripe\Core\Injector\Injector:
  NSWDPC\StructuredEmail\Decorator:
    class: Some\Project\MyDecorator
```

In the default decorator, the values used are:

| Name      | Description | Default |
| ----------- | ----------- | ----- |
| FontFamily      | The default font family | "-apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Ubuntu, roboto, noto, arial, sans-serif" |
| FontSize   | The default font size        | '16px' |
| BodyBackgroundColor   | The `bgcolor` attribute value applied to the `<body>` tag and as the `background-color` of the `email-wrapper` element        | '#F2F4F6' |
| BackgroundColor   | The `background-color` applied to the `email-body_inner` element       | '#FFFFFF' |
| Color   | The text colour of `<p>` elements       | '#51545E'|
| SubColor   | The text colour of `<p class="sub">` elements       | '#6B6E76' |
| HeadingColor   | The colour of h1-h3 elements       | '#333333' |
| PrimaryColor   | The text colour of `.email-body a` link elements, the `background-color` of `.email-masthead`      | '#002664'|
| PrimaryTextColor   | The text colour of `.email-masthead` text and links | '#ffffff'
| PrimaryButtonColor | The `background-color` of `.email-body .button` elements  |  '#002664'|
| PrimaryButtonTextColor | The text colour of `.email-body .button` elements  |  '#ffffff'|
| HighlightColor   | (unused)       | '#d7153a'|
| SecondaryColor   | (unused)       | '#2e5299'|
| DarkModeBackgroundColor   | Background colour of email when email is rendered in darkmode     | '#121212'|
| DarkModeBackgroundSubColor   | Background colour of `.email-masthead` when email is rendered in darkmode      | '#333333'|
| DarkModeColor   | Text colour when email is rendered in darkmode     | '#ffffff'|
| DarkModeButtonColor   | Background colour of `.email-body .button` when email is rendered in darkmode     | '#002664'|

### Set the layout type

A layout type allows you some control over the HTML width ([more](https://github.com/ActiveCampaign/mailmason/wiki/Project-Structure#layouts))

+ basic-full = full width
+ basic = single centred column
+ plain = similar to basic but less decorations

Set a layout type via Injector:

```yaml
---
Name: 'project-emails-layout'
---
SilverStripe\Core\Injector\Injector:
  Some\Project\MyDecorator:
    properties:
      LayoutType: 'basic'
```

These changes will affect all emails. To set a Decorator per email:

```php
<?php
// create an email
$email = Email::create();
// create a processor instance, with the email as the argument
$processor = StructuredEmailProcessor::create($email);
// $decorator created previously for this email
$processor->setDecorator($decorator);
// attach this processor as data to its email
$email->setData('StructuredEmailProcessor', $processor);
```
