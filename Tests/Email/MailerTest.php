<?php

namespace Illarra\EmailBundle\Tests\Email;

use Illarra\EmailBundle\Email;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $swift = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();

        $swift
            ->expects($this->any())
            ->method('send')
            ->with($this->isInstanceOf('\Swift_Mime_Message'))
            ->will($this->returnValue(1));

        $mailer = new Email\Mailer($swift);
        $layout = new Email\Layout();

        $count = $mailer->send('default', $layout, [
            'to' => ['asier@illarra.com' => 'Asier Illarramendi'],
        ]);

        $this->assertEquals(1, $count, 'One email sent.');
    }
}