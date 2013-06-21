<?php

namespace Illarra\EmailBundle\Tests\Email;

use Illarra\EmailBundle\Email;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        // Twig
        $twig = $this->getMockBuilder('\Twig_Environment')
            ->setMethods(array('loadTemplate', 'render'))
            ->disableOriginalConstructor()
            ->getMock();

        // InlineStyle
        $inliner = new \InlineStyle\InlineStyle();

        // Swift
        $swift = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();

        $swift
            ->expects($this->any())
            ->method('send')
            ->with($this->isInstanceOf('\Swift_Mime_Message'))
            ->will($this->returnValue(1));

        $mailer   = new Email\Mailer($swift);
        $renderer = new Email\Renderer($twig, $inliner);

        // Generate an Email Message
        // This message could be reused if it is generic
        $msg = $renderer->createMessage(
            'AcmeEmailBundle:Email:clean.html.twig', // Layout
            'signup',                                // Template
            'es',                                    // Locale
            [                                        // Template Data
                'name' => 'Periko de los palotes',
            ]
        );

        // Finish the message: setTo + attachments
        // Notice that this is a ordinary Swift_Message
        $msg->setTo(['asier@illarra.com' => 'Asier Illarramendi']);

        // Send the message from "maritxu" profile
        // The mailer will read the profile and set "from" field
        $count = $mailer->send('maritxu', $msg);

        $this->assertEquals(1, $count, 'One email sent.');
    }

    public function testSendWithAttachment() 
    {
        $this->assertTrue(false, 'Sends and email with attachment.');
    }
}