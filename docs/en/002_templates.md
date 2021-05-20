# Templates

You can override any template provided in this module per the standard Silverstripe template priority handling.

If you do so, it's up to you to ensure the HTML renders correctly in an email client.

## Example

> Provide your own Footer template

```yaml
---
Name: project-theme
---
SilverStripe\View\SSViewer:
  themes:
    - 'mytheme'
    - 'a-vendor/the-module:their-theme'
    - '$default'
```

Given the above, provide your own e-mail footer in `mytheme`:

```shell
themes/mytheme/templates/NSWDPC/StructuredEmail/Includes/Footer.ss
```

The `a-vendor/the-module` theme might provide the same template, but your theme takes precedence due to your project configuration:

```shell
vendor/a-vendor/the-module/themes/their-theme/templates/NSWDPC/StructuredEmail/Includes/Footer.ss
```
