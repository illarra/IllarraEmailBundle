<?php

namespace Illarra\EmailBundle\Email;

use Symfony\Component\HttpKernel\Kernel;

/**
 *
 */
class Renderer
{
    protected $inliner;
    protected $kernel;
    protected $layout;
    protected $subject;
    protected $twig;
    protected $twigStrLoader;

    /**
     *
     */
    public function __construct(Kernel $kernel, \Twig_Environment $twig, \Twig_Loader_String $twigStrLoader, \InlineStyle\InlineStyle $inliner)
    {
        $this->inliner       = $inliner;
        $this->kernel        = $kernel;
        $this->twig          = $twig;
        $this->twigStrLoader = $twigStrLoader;

        $this->layout  = "layout";
        $this->subject = "subject";
    }

    /**
     *
     */
    public function getLayoutVar()
    {
        return $this->layout;
    }

    /**
     * Create a Layout to render ONLY the "subject" block, this will be used
     * as the email subject
     */
    protected function getSubjectLayout()
    {
        $current = $this->twig->getLoader();
        $this->twig->setLoader($this->twigStrLoader);

        try {
            $layout = $this->twig->loadTemplate('{% block '. $this->subject .' %}{% endblock %}');
        } catch (\Exception $e) {
            $this->twig->setLoader($current);

            throw $e;
        }

        $this->twig->setLoader($current);

        return $layout;
    }

    /**
     *
     */
    public function getSubjectVar()
    {
        return $this->subject;
    }

    /**
     *
     */
    public function htmlToPlain($html)
    {
        return $html;
    }

    /**
     *
     */
    public function render($css, $layout, $template, array $data = array())
    {
        $loader    = $this->twig->getLoader();
        $hasExists = $loader instanceof \Twig_ExistsLoaderInterface;

        // LOAD LAYOUT
        if ($hasExists && !$loader->exists($layout)) {
            throw new Exception\LayoutNotFoundException("Layout '$layout' not found.");
        }

        $layout = $this->twig->loadTemplate($layout);

        // LOAD TEMPLATE
        if ($hasExists && !$loader->exists($template)) {
            throw new Exception\TemplateNotFoundException("Template '$template' not found.");
        }

        $template = $this->twig->loadTemplate($template);

        // Twig says this method should not be used
        // https://github.com/fabpot/Twig/blob/v1.13.1/lib/Twig/Template.php#L58
        if ($template->getParent(array_merge($data, [$this->layout => $layout])) === false) {
            throw new Exception\TemplateDoesNotExtendException("Template doesn't extend. Please add: {% extends {$this->layout} %}");   
        }

        // -------
        // SUBJECT
        // -------
        // Render & clean subject
        $subjectLayout = $this->getSubjectLayout();
        $subject       = $template->render(array_merge($data, [$this->layout => $subjectLayout]));
        $subject       = preg_replace('/\n/',' ', $subject);
        $subject       = preg_replace('!\s+!', ' ', $subject);
        $subject       = trim($subject);

        // ----
        // BODY
        // ----
        // Render the "layout", add the layout given by the user
        // and the subject generated before to the data
        $body = $template->render(array_merge($data, [
            $this->layout  => $layout,
            $this->subject => $subject,
        ]));

        // Clean comments <!-- -->
        // "/s" makes "."  match new lines
        $body = preg_replace('/\<!--.*?--\>/s', '', $body);

        // -----------------
        // ADD INLINE STYLES
        // -----------------
        $css = file_get_contents($this->kernel->locateResource($css));

        $this->inliner->loadHTML($body);
        @$this->inliner->applyStylesheet($css);

        $body = $this->inliner->getHtml();

        // Return rendered values
        return [
            $this->subject => $subject, 
            'body_html'    => $body,
            'body_plain'   => $this->htmlToPlain($body),
        ];
    }

    /**
     *
     */
    public function setLayoutVar($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     *
     */
    public function setSubjectVar($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     *
     */
    public function updateMessage(\Swift_Message $message, $css, $layout, $template, array $data = array())
    {
        $render = $this->render($css, $layout, $template, $data);

        $message
            ->setCharset('utf-8')
            ->setSubject($render[$this->subject])
            ->setBody($render['body_plain'], 'text/plain')
            ->addPart($render['body_html'], 'text/html');

        return $message;
    }
}
