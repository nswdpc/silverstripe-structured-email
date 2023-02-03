# Schema.org support

The StructuredEmail class supports [Schema.org EmailMessage](https://schema.org/EmailMessage) by default, along with zero or one `potentialAction` values.

## Actions

Schema.org supports actions in EmailMessage.

Some actions require you to be registered with an email provider. Using actions may result in no changes to how your email is rendered at the recipient's end.

Reference (gmail): https://developers.google.com/gmail/markup/reference/go-to-action

### ViewAction

A common type of action is the `view action`:

```php
<?php
/* @var StructuredEmail */
$email->setViewAction(
    'Confirm your identify',
    'https://confirm.example.com?token=suitably-long-token'
);
```
Internally, a `\Spatie\SchemaOrg\ViewAction` will be created using the name and URL provided.

This will result in the following HTML snippet in the template:

```html
<script type="application/ld+json">
{
    "@context":"https:\/\/schema.org",
    "@type":"EmailMessage",
    "about":{
        "@type":"Thing",
        "name":"Confirm your identity"
    },
    "abstract":"We need to confirm your registration on our website",
    "action":{
        "@type":"ViewAction",
        "name":"Confirm identify",
        "url":"https:\/\/confirm.example.com?token=suitably-long-token"
    }
}
</script>
```

## Action

You can also define a generic action:

```php
<?php
/* @var Spatie\SchemaOrg\Action */
$action = Schema::action()
    ->name('Carry out this action')
    ->handler(
        [
            '@type' => "HttpActionHandler",
            "url" => "https://action.example.com?action=1234"
        ]
    );
/* @var StructuredEmail */
$email->setAction($action);
```

Using your own Actions is possible eg. SaveAction. It must implement the [ActionContract](https://github.com/spatie/schema-org/blob/master/src/Contracts/ActionContract.php)

Further reading: https://developers.google.com/gmail/markup/reference/go-to-action
