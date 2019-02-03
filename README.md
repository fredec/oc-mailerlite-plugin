# Plugin to integrate OctoberCMS website with MailerLite email service

[OctoberCMS](https://www.octobercms.com)

Service: [Mailerlite](http://www.mailerlite.com)

Instructions (after installation):

1. Insert the API key in configuration area.
1. Insert the component in the page to create a form. In the component, setup the Group ID.
1. If the form has a field called "group" (name="group"), the component will create a Group in the MailerLite with the field value (if there is no group with the name yet) + include the email+name in this new group.

## Require:

Required: [Margic Forms plugin](https://octobercms.com/plugin/martin-forms)

## Use a field named "group" to create a new group in MailerLite:

```php
<input type="text" name="group" />
```
