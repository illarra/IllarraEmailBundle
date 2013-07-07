<?php

namespace Illarra\EmailBundle\Tests\Email;

use Illarra\EmailBundle\Email;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    protected $kernel;
    protected $renderer;

    protected function setUp()
    {
        $this->kernel = $this->getMockBuilder('\\Symfony\Component\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $twig = new \Twig_Environment();
        $twig->setLoader(new \Twig_Loader_Filesystem('./Tests/fixtures'));

        $twigStrLoader = new \Twig_Loader_String();
        $inliner       = new \InlineStyle\InlineStyle();

        $this->renderer = new Email\Renderer($this->kernel, $twig, $twigStrLoader, $inliner);
    }

    public function testCleanup()
    {
        // Default Quotes
        $this->renderer->setForceDoubleQuotes(false);

        $html = $this->renderer->cleanup(file_get_contents('./Tests/fixtures/cleanup_quotes_before.txt'));
        $this->assertEquals(file_get_contents('./Tests/fixtures/cleanup_quotes_before.txt'), $html, "Don't cleanup anything");

        // Force Double Quotes
        $this->renderer->setForceDoubleQuotes(true);

        $html = $this->renderer->cleanup(file_get_contents('./Tests/fixtures/cleanup_quotes_before.txt'));
        $this->assertEquals(file_get_contents('./Tests/fixtures/cleanup_quotes_after.txt'), $html, "Cleanup single quoted html attributes");

    }

    public function testSetterGetters()
    {
        // Default values
        $this->assertEquals('layout', $this->renderer->getLayoutVar());
        $this->assertEquals('subject', $this->renderer->getSubjectVar());

        // Change default values
        $this->renderer->setLayoutVar('da_layout');
        $this->renderer->setSubjectVar('da_subject');

        $this->assertEquals('da_layout', $this->renderer->getLayoutVar());
        $this->assertEquals('da_subject', $this->renderer->getSubjectVar());
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Exception\LayoutNotFoundException
     */
    public function testRenderLayoutNotFound()
    {
        $this->renderer->render('nonlayout.twig', 'template.twig', 'email.css');
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Exception\TemplateNotFoundException
     */
    public function testRenderTemplateNotFound()
    {
        $this->renderer->render('layout.twig', 'nontemplate.twig', 'email.css');
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Exception\TemplateDoesNotExtendException
     */
    public function testRenderTemplateDoesNotExtend()
    {
        $this->renderer->render('layout.twig', 'noextends.twig', 'email.css');
    }

    public function testRender()
    {
        $this->kernel
            ->expects($this->any())
            ->method('locateResource')
            ->with($this->identicalTo('email.css'))
            ->will($this->returnValue('./Tests/fixtures/email.css'));

        $data = $this->renderer->render('layout.twig', 'template.twig', 'email.css');

        $this->assertEquals('This is da subject', $data['subject'], 'Subject should not have new lines and untrimed space');
    }
}