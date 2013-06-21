<?php

namespace Illarra\EmailBundle\Email;

class Mailer
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     *
     */
    public function send($profile, \Swift_Message $message)
    {
        // Load $profile
        
        
        // From $profile
        $message->setFrom(['test@example.com' => 'Test']);

        return $this->mailer->send($message);
    }
}