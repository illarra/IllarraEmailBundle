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
        $this->renderer->render('email.css', 'nonlayout.twig', 'template.twig');
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Exception\TemplateNotFoundException
     */
    public function testRenderTemplateNotFound()
    {
        $this->renderer->render('email.css', 'layout.twig', 'nontemplate.twig');
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Exception\TemplateDoesNotExtendException
     */
    public function testRenderTemplateDoesNotExtend()
    {
        $this->renderer->render('email.css', 'layout.twig', 'noextends.twig');
    }

    public function testRender()
    {
        $this->kernel
            ->expects($this->any())
            ->method('locateResource')
            ->with($this->identicalTo('email.css'))
            ->will($this->returnValue('./Tests/fixtures/email.css'));

        $data = $this->renderer->render('email.css', 'layout.twig', 'template.twig');

        $this->assertEquals('This is da subject', $data['subject'], 'Subject should not have new lines and untrimed space');
    }
}