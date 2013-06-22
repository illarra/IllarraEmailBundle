<?php

namespace Illarra\EmailBundle\Email;

/**
 *
 */
class Mailer
{
    protected $mailer;
    protected $profiles;

    /**
     *
     */
    public function __construct(\Swift_Mailer $mailer, array $profiles = [])
    {
        $this->mailer   = $mailer;
        $this->profiles = $profiles;
    }

    /**
     *
     */
    public function getProfile($profile)
    {
        if (!$this->hasProfile($profile)) {
            throw new Error\ProfileNotFoundException("Profile '$profile' not found.");
        }

        return $this->profiles[$profile];
    }

    /**
     *
     */
    public function hasProfile($profile)
    {
        return isset($this->profiles[$profile]);
    }

    /**
     *
     */
    public function send($profile, \Swift_Message $message)
    {
        $profile = $this->getProfile($profile);

        // From
        $message->setFrom($profile['from']);

        // If there are multiple From emails we must set the Sender
        if (count($profile['from']) > 1) {
            // Reset the pointer and get the email
            reset($profile['from']);
            $email = key($profile['from']);

            // Set the Sender
            $message->setSender(array($email => $profile['from'][$email]));
        }

        // ReplyTo
        if (isset($profile['reply_to']) && !empty($profile['reply_to'])) {
            $message->setReplyTo($profile['reply_to']);
        }

        return $this->mailer->send($message);
    }
}