### Decorating emails

A decorator allows you to set CSS values in the template

By default the `NSWDPC\StructuredEmail\Decorator` class is used but you can provide your own decorator

```php
<?php
namespace Some\Project;

use NSWDPC\StructuredEmail\AbstractDecorator;

class MyDecorator extends AbstractDecorator {
    
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

Then create it:

```php
<?php
$decorator = new MyDecorator();
$email->setDecorator($decorator)->send();
```

You can also inject your own decorator over `NSWDPC\StructuredEmail\Decorator`

```yaml
---
Name: 'project-emails'
---
SilverStripe\Core\Injector\Injector:
  NSWDPC\StructuredEmail\Decorator:
    class: Some\Project\MyDecorator
```

```php
<?php
use NSWDPC\StructuredEmail\Decorator;
use SilverStripe\Core\Injector\Injector;

$decorator = Injector::inst()->create(Decorator::class);
print get_class($decorator);
// Some\Project\MyDecorator
```

### Set the layout type

A layout type allows you some control over the HTML width ([more](https://github.com/ActiveCampaign/mailmason/wiki/Project-Structure#layouts))

+ basic-full = full width
+ basic = single centred column
+ plain = similar to basic but less decorations 

```php
<?php
$decorator->setLayoutType('basic-full');
$email->setDecorator($decorator)->send();
```
