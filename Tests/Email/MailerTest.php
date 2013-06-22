<?php

namespace Illarra\EmailBundle\Tests\Email;

use Illarra\EmailBundle\Email;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    protected $swift;

    public function getMailer(array $profiles = array())
    {
        // Swift
        $this->swift = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();

        return new Email\Mailer($this->swift, $profiles);
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Exception\ProfileNotFoundException
     */
    public function testSendProfileNotFoundException()
    {
        $mailer = $this->getMailer();
        $mailer->send('unknown', \Swift_Message::newInstance());
    }

    public function testSend()
    {
        $profiles = array(
            'doup' => array(
                'from' => array(
                    'doup@example.com'  => 'Asier',
                ),
            ),
        );

        $mailer = $this->getMailer($profiles);

        $this->swift
            ->expects($this->any())
            ->method('send')
            ->with($this->isInstanceOf('\Swift_Mime_Message'))
            ->will($this->returnValue(1));

        $msg   = \Swift_Message::newInstance();
        $count = $mailer->send('doup', $msg);
        $this->assertEquals(1, $count, 'One email sent.');

        $this->assertEquals(null, $msg->getSender());
        $this->assertEquals($profiles['doup']['from'], $msg->getFrom());
        $this->assertEquals(null, $msg->getReplyTo());
    }

    public function testSendFullTest()
    {
        $profiles = array(
            'doup' => array(
                'from' => array(
                    'doup@example.com'  => 'Asier',
                    'eneko@example.com' => 'Eneko',
                ),
                'reply_to' => array(
                    'joxepo@example.com'  => 'Joxepo',
                    'maritxu@example.com' => 'Maritxu',
                ),
            ),
        );

        $mailer = $this->getMailer($profiles);

        $this->swift
            ->expects($this->any())
            ->method('send')
            ->with($this->isInstanceOf('\Swift_Mime_Message'))
            ->will($this->returnValue(1));

        $msg   = \Swift_Message::newInstance();
        $count = $mailer->send('doup', $msg);
        $this->assertEquals(1, $count, 'One email sent.');

        $this->assertEquals(array('doup@example.com' => 'Asier'), $msg->getSender());
        $this->assertEquals($profiles['doup']['from'], $msg->getFrom());
        $this->assertEquals($profiles['doup']['reply_to'], $msg->getReplyTo());
    }
}