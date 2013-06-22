<?php

namespace Illarra\EmailBundle\Tests\Email;

use Illarra\EmailBundle\Email;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    protected $renderer;

    protected function setUp()
    {
        $twig = new \Twig_Environment();
        $twig->setLoader(new \Twig_Loader_Filesystem('./Tests/fixtures'));

        $twigStrLoader = new \Twig_Loader_String();
        $inliner       = new \InlineStyle\InlineStyle();

        $this->renderer = new Email\Renderer($twig, $twigStrLoader, $inliner);
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Error\LayoutNotFound
     */
    public function testRenderLayoutNotFound()
    {
        $this->renderer->render('nonlayout.twig', 'template.twig');
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Error\TemplateNotFound
     */
    public function testRenderTemplateNotFound()
    {
        $this->renderer->render('layout.twig', 'nontemplate.twig');
    }

    /**
     * @expectedException \Illarra\EmailBundle\Email\Error\TemplateDoesNotExtend
     */
    public function testRenderTemplateDoesNotExtend()
    {
        $this->renderer->render('layout.twig', 'noextends.twig');
    }

    public function testRender()
    {
        $data = $this->renderer->render('layout.twig', 'template.twig');

        $this->assertEquals('This is da subject', $data['subject'], 'Subject should not have new lines and untrimed space');
    }
}