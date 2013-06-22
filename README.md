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
    '@AcmeEmailBundle/Resources/email.css',
    'AcmeEmailBundle:Email:layout.html.twig',
    'AcmeEmailBundle:Email:signup/eu.html.twig',
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
$renderer->updateMessage($swift_message, $css, $layout, $template, $data);
```

This is the minimum a template needs:

```twig
{% extends layout %}
{% block subject %}Welcome {{ name }}!{% endblock %}
```

In `config.yml`:

```yml
illarra_email:
  layout_var:  'layout'
  subject_var: 'subject'
```

Mailer
------

In `config.yml`:

```yml
illarra_email:
  profiles:
    maritxu:
      from: { maritxu@example.com: Maritxu }
    bartolo:
      from: { no-reply@example.com: Unknown }
      reply_to: { bartolo@example.com: Bartolo }
```