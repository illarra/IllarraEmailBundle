Illarra Email Bundle
====================

[![Build Status](https://secure.travis-ci.org/illarra/IllarraEmailBundle.png)](http://travis-ci.org/illarra/IllarraEmailBundle) [![Total Downloads](https://poser.pugx.org/illarra/email-bundle/d/total.png)](https://packagist.org/packages/illarra/email-bundle) [![Latest Stable Version](https://poser.pugx.org/illarra/email-bundle/version.png)](https://packagist.org/packages/illarra/email-bundle) [![Latest Unstable Version](https://poser.pugx.org/illarra/email-bundle/v/unstable.png)](https://packagist.org/packages/illarra/email-bundle)

This bundle let's you create HTML emails with inline styles using Twig as the template language. It's made of two services:

  - Renderer: Updates the given Swift_Message using the given Twig layout/template and css.
  - Mailer: It's a wrapper for the default "@mailer" service which let's you use profiles to tell who is sending the email.

```php
$message = new Swift_Message::newInstance();

$this->get('illarra.email.renderer')->updateMessage(
    $message,
    'AcmeEmailBundle:Email:layout.html.twig',
    'AcmeEmailBundle:Email:signup/eu.html.twig',
    [
        'username' => 'doup',
    ]
);

$message->setTo(['bartolo@example.com' => 'Bartolo']);

$this->get('illarra.email.mailer')->send('maritxu', $message);
```

Renderer
--------

```php
$renderer->updateMessage($swift_message, $layout, $template, $data);
```

Template requirements:

```twig
{% extends layout %}

{% block subject %}

{% endblock %}

{% block another_block %}

{% endblock %}
```

Mailer
------