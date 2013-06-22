<?php

namespace Illarra\EmailBundle\Tests\Email;

use Illarra\EmailBundle\Email;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        // Swift
        $swift = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();

        $swift
            ->expects($this->any())
            ->method('send')
            ->with($this->isInstanceOf('\Swift_Mime_Message'))
            ->will($this->returnValue(1));

        $mailer = new Email\Mailer($swift);

        $msg = \Swift_Message::newInstance();

        // Send the message from "maritxu" profile
        // The mailer will read the profile and set "from" field
        $count = $mailer->send('maritxu', $msg);

        $this->assertEquals(1, $count, 'One email sent.');
    }

    public function testSendWithAttachment() 
    {
        //$this->assertTrue(false, 'Sends and email with attachment.');
    }
}