<?php

namespace Illarra\EmailBundle\Email;

class Mailer
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function htmlToPlain($html)
    {
        return $html;
    }

    /**
     *
     */
    public function send($profile, Layout $layout, array $data = array())
    {
        $html  = $layout->render();
        $plain = $this->htmlToPlain($html);

        // Send From Profile
        $message = \Swift_Message::newInstance()
            ->setCharset('utf-8')
            ->setSubject('Subject')
            ->setFrom(['test@example.com' => 'Test'])
            ->setTo($data['to'])
            ->setBody($plain, 'text/plain')
            ->addPart($html, 'text/html');

        // TODO: check for attachments

        return $this->mailer->send($message);
    }
}