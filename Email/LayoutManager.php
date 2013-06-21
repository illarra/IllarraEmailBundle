<?php

namespace Illarra\EmailBundle\Email;

class LayoutManager
{
    protected $loaders = array();

    public function __construct($inliner)
    {
        $this->inliner = $inliner;
    }

    public function getTemplate($template, $locale)
    {
        foreach ($loaders as $loader) {
            $template = $loader->load($template, $locale);

            if ($template !== false) {
                return $template;
            }
        }

        throw new \Exception("Template '{$template}' with locale '{$locale}' not found.");
    }

    public function render($layout, $template, array $data = array())
    {
        $html = $layout->render($template, $data);
        $css  = "html { background-color: red; }";

        // Create the email HTML with inline styles
        $inliner = new \InlineStyle\InlineStyle($html);
        @$inliner->applyStylesheet($css);

        $content = $inliner->getHtml();
    }
}
        // layout folder OR "@AppCoreBundle/Email/xxx"
        // 
        /*
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppCoreBundle:EmailTemplate')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmailTemplate entity.');
        }

        $twig     = $this->get('twig');
        $markdown = $this->get('markdown.parser');
        $css      = file_get_contents($this->container->getParameter('kernel.root_dir') . '/../src/App/CoreBundle/Resources/assets/css/email.css');
        $body     = $twig->render($entity->getBody(), [
            'name' => 'Periko de los palotes',
        ]);

        // Add link with button class, format: [[Text](http://example.com)]
        $buttonClass   = 'btn';
        $buttonTextTpl = function ($text) {
            return $text . ' »';
        };

        $body = preg_replace_callback('/\[\[(.*?)\]\((.*?)\)\]/', function ($m) use ($buttonClass, $buttonTextTpl) {
            return "<a class='{$buttonClass}' href='{$m[2]}'>" . $buttonTextTpl($m[1]) . "</a>";
        }, $body);

        // Render template
        $html = $twig->render("AppCoreBundle:Email:{$layout}.html.twig", array(
            'title' => $entity->getSubject(),
            'body'  => $markdown->transformMarkdown($body),
        ));

        // Clean comments <!-- -->
        // /s makes . to match new lines
        $html = preg_replace('/\<!--.*?--\>/s', '', $html);

        // Create the email HTML with inline styles
        $inliner = new \InlineStyle\InlineStyle($html);
        @$inliner->applyStylesheet($css);

        return new \Symfony\Component\HttpFoundation\Response($inliner->getHtml());
        */