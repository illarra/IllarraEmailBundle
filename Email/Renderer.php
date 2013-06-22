<?php

namespace Illarra\EmailBundle\Email;

class Renderer
{
    protected $inliner;
    protected $twig;
    protected $twigStrLoader;

    public function __construct(\Twig_Environment $twig, \Twig_Loader_String $twigStrLoader, \InlineStyle\InlineStyle $inliner)
    {
        $this->inliner       = $inliner;
        $this->twig          = $twig;
        $this->twigStrLoader = $twigStrLoader;
        $this->subject       = "subject";
        $this->layout        = "layout";
    }

    public function updateMessage(\Swift_Message $message, $layout, $template, array $data = array())
    {
        $render = $this->render($layout, $template, $data);

        $message
            ->setCharset('utf-8')
            ->setSubject($render[$this->subject])
            ->setBody($render['body_plain'], 'text/plain')
            ->addPart($render['body_html'], 'text/html');

        return $message;
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

    public function render($layout, $template, array $data = array())
    {
        $loader    = $this->twig->getLoader();
        $hasExists = $loader instanceof \Twig_ExistsLoaderInterface;

        // LOAD LAYOUT
        if ($hasExists && !$loader->exists($layout)) {
            throw new Error\LayoutNotFound("Layout '$layout' not found.");
        }

        $layout = $this->twig->loadTemplate($layout);

        // LOAD TEMPLATE
        if ($hasExists && !$loader->exists($template)) {
            throw new Error\TemplateNotFound("Template '$template' not found.");
        }

        $template = $this->twig->loadTemplate($template);

        // Twig says this method should not be used
        // https://github.com/fabpot/Twig/blob/v1.13.1/lib/Twig/Template.php#L58
        if ($template->getParent(array_merge($data, [$this->layout => $layout])) === false) {
            throw new Error\TemplateDoesNotExtend("Template doesn't extend. Please add: {% extends {$this->layout} %}");   
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
        //$css = file_get_contents($this->container->getParameter('kernel.root_dir') . '/../src/App/CoreBundle/Resources/assets/css/email.css');
        $css = "html { background-color: red; }";

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

    public function htmlToPlain($html)
    {
        return $html;
    }
}
