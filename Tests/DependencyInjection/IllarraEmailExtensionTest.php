<?php

namespace Illarra\EmailBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Illarra\EmailBundle\DependencyInjection\IllarraEmailExtension;

class IllarraEmailExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    /**
     * @return ContainerBuilder
     */
    protected function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }

    public function setUp()
    {
        $this->extension = new IllarraEmailExtension();
    }

    public function testGetConfigWithDefaultValues()
    {
        $config = [];
        $this->extension->load([$config], $container = $this->getContainer());

        $this->assertTrue($container->hasParameter('illarra.email_bundle.mailer.profiles'), 'profiles parameter');
        $this->assertEquals([], $container->getParameter('illarra.email_bundle.mailer.profiles'), 'Default for profiles');

        $this->assertTrue($container->hasParameter('illarra.email_bundle.renderer.force_double_quotes'), 'force_double_quotes parameter');
        $this->assertEquals(false, $container->getParameter('illarra.email_bundle.renderer.force_double_quotes'), 'Default for force_double_quotes');

        $this->assertTrue($container->hasParameter('illarra.email_bundle.renderer.layout_var'), 'layout_var parameter');
        $this->assertEquals('layout', $container->getParameter('illarra.email_bundle.renderer.layout_var'), 'Default for layout_var');

        $this->assertTrue($container->hasParameter('illarra.email_bundle.renderer.subject_var'), 'subject_var parameter');
        $this->assertEquals('subject', $container->getParameter('illarra.email_bundle.renderer.subject_var'), 'Default for subject_var');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidTypeException
     */
    public function testProfilesString()
    {
        $config = ['profiles' => 'string'];
        $this->extension->load([$config], $container = $this->getContainer());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testProfilesFromRequired()
    {
        $config = [
            'profiles' => [
                'doup' => [
                    'reply_to' => ['doup@example.com' => 'Asier'],
                ],
            ],
        ];

        $this->extension->load([$config], $container = $this->getContainer());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testProfilesFromOneRequired()
    {
        $config = [
            'profiles' => [
                'doup' => [
                    'from' => [],
                ],
            ],
        ];

        $this->extension->load([$config], $container = $this->getContainer());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testProfilesFromInvalidEmail()
    {
        $config = [
            'profiles' => [
                'doup' => [
                    'from' => [
                        'bartolo@example.com' => 'Bartolo',
                        'notanemail' => 'Asier',
                    ],
                ],
            ],
        ];

        $this->extension->load([$config], $container = $this->getContainer());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testProfilesReplyToInvalidEmail()
    {
        $config = [
            'profiles' => [
                'doup' => [
                    'from'     => ['doup@example.com' => 'Asier'],
                    'reply_to' => [
                        'bartolo@example.com' => 'Bartolo',
                        'notanemail' => 'Asier',
                    ],
                ],
            ],
        ];

        $this->extension->load([$config], $container = $this->getContainer());
    }

    public function testProfiles()
    {
        $config = [
            'profiles' => [
                'doup' => [
                    'from'     => ['doup@example.com' => 'Asier'],
                    'reply_to' => [
                        'bartolo@example.com' => 'Bartolo',
                        'doup@example.com' => 'Asier',
                    ],
                ],
            ],
        ];

        $this->extension->load([$config], $container = $this->getContainer());

        $this->assertEquals($config['profiles'], $container->getParameter('illarra.email_bundle.mailer.profiles'), 'Profiles array is OK');
    }
}