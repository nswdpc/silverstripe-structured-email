# Schema.org support

The StructuredEmail class supports [Schema.org EmailMessage](https://schema.org/EmailMessage) by default, along with zero or one `potentialAction` values.

## Actions

Some actions require you to be registered with an email provider. Using actions may result in no changes to how your email is rendered at the recipient's end.

Reference (gmail): https://developers.google.com/gmail/markup/reference/go-to-action

By default, no Actions is set in the EmailMessage schema added to an HTML email.

## Add an action

Add a ViewAction to an email:

```php
<?php
// create an email
$email = Email::create();
// create a processor instance, with the email as the argument
$processor = StructuredEmailProcessor::create($email);
$processor->setViewAction('Confirm your identify', 'https://confirm.example.com?token=some-token-for-the-recipient');
// attach this processor as data to its email
$email->setData('StructuredEmailProcessor', $processor);
// other email actions
// ...
$email->send();
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

// similar to the view action, but call setAction
$processor->setAction($action);
```

Using your own Actions is possible eg. SaveAction. It must implement the [ActionContract](https://github.com/spatie/schema-org/blob/master/src/Contracts/ActionContract.php)

Further reading: https://developers.google.com/gmail/markup/reference/go-to-action
