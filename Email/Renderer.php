<?php

namespace Illarra\EmailBundle\Email;

class Renderer
{
    protected $inliner;
    protected $twig;

    public function __construct(\Twig_Environment $twig, \InlineStyle\InlineStyle $inliner)
    {
        $this->inliner = $inliner;
        $this->twig    = $twig;
    }

    public function createMessage($layout, $template, $locale, array $data = array())
    {
        $render  = $this->render($layout, $template, $locale, $data);
        $message = \Swift_Message::newInstance();

        $message
            ->setCharset('utf-8')
            ->setSubject($render['subject'])
            ->setBody($render['body_plain'], 'text/plain')
            ->addPart($render['body_html'], 'text/html');

        return $message;
    }

    public function loadTemplate($template, $locale)
    {

    }

    public function render($layout, $template, $locale, array $data = array())
    {
        // CHECK IF LAYOUT EXISTS
        $layout = $this->twig->loadTemplate($layout);

        // CHECK IF TEMPLATE EXISTS
        $template = $this->loadTemplate($template, $locale);

        // -------
        // SUBJECT
        // -------
        // Create a Layout to render ONLY the "subject" block, this will be used
        // as the email subject
        $subjectLayout = $this->twig->loadTemplate('{% block subject %}{% endblock %}');
        $subject       = $this->twig->render($template, array_merge($data, ['layout' => $subjectLayout]));
        $subject       = preg_replace('/\n/',' ', $subject);
        $subject       = preg_replace('!\s+!', ' ', $subject);
        $subject       = trim($subject);

        // ----
        // BODY
        // ----
        // Render the "layout", add the layout given by the user
        // and the subject generated before to the data
        $body = $this->twig->render($template, array_merge($data, [
            'layout'  => $layout,
            'subject' => $subject,
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
            'subject'    => $subject, 
            'body_html'  => $body,
            'body_plain' => $this->htmlToPlain($body),
        ];
    }

    public function htmlToPlain($html)
    {
        return $html;
    }
}
