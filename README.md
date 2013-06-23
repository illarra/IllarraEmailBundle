Illarra Email Bundle
====================

[![Build Status](https://secure.travis-ci.org/illarra/IllarraEmailBundle.png)](http://travis-ci.org/illarra/IllarraEmailBundle) [![Total Downloads](https://poser.pugx.org/illarra/email-bundle/d/total.png)](https://packagist.org/packages/illarra/email-bundle) [![Latest Stable Version](https://poser.pugx.org/illarra/email-bundle/version.png)](https://packagist.org/packages/illarra/email-bundle) [![Latest Unstable Version](https://poser.pugx.org/illarra/email-bundle/v/unstable.png)](https://packagist.org/packages/illarra/email-bundle)

This bundle let's you create HTML emails with inline styles using Twig as the template language. It's made of two services:

  - Renderer: Updates the given Swift_Message using a Twig layout/template and css.
  - Mailer: It's a wrapper for the default "@mailer" service which let's you use profiles to tell who is sending the email.

```php
$message = new \Swift_Message::newInstance();

$this->get('illarra.email.renderer')->updateMessage(
    $message,
    'AcmeEmailBundle:Email:layout.html.twig',
    'AcmeEmailBundle:Email:signup/eu.html.twig',
    '@AcmeEmailBundle/Resources/assets/css/email.css',
    [
        'name' => 'doup',
    ]
);

$message->setTo(['bartolo@example.com' => 'Bartolo']);

$this->get('illarra.email.mailer')->send('maritxu', $message);
```

Renderer
--------

```php
$renderer->updateMessage($swift_message, $layout, $template, $css, $data);
```

### Layout & template

Both layout & template need a Twig path. Layout and templates are separated, so that is easier to maintain lot's of 
templates. 


This is the minimum a `$template` needs:

```twig
{% extends layout %}
{% block subject %}Welcome {{ name }}!{% endblock %}
```

Note that layout in the `extends` tag is a variable which corresponds to the 
`$layout` given in the updateMessage() method. The `subject` block is used to 
generate the email subject. **Both are required**.

The names of the layout variable and the subject block can be changed in
`config.yml`:

```yml
illarra_email:
  layout_var:  'layout'
  subject_var: 'subject'
```

### CSS

You can use the `@AcmeBundle/path/my.css` to locate your css. This will be used
to add inline styles to the generated HTML.

Mailer
------

```php
$mailer->send($profile, $swift_message);
```

Profiles are defined in `config.yml`:

```yml
illarra_email:
  profiles:
    maritxu:
      from: { maritxu@example.com: Maritxu }
    bartolo:
      from: { no-reply@example.com: Unknown }
      reply_to: { bartolo@example.com: Bartolo }
```

Define the From and ReplyTo options like in a Swift Message: 
`{'email': 'name'}`. You can define multiple emails in the From parameter, all
of them will be visible to the addressee, but only the first one will be the 
actual Sender.