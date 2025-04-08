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
