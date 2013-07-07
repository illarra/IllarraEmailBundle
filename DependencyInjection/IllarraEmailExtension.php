<?php

namespace Illarra\EmailBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IllarraEmailExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('illarra.email_bundle.mailer.profiles', $config['profiles']);
        $container->setParameter('illarra.email_bundle.renderer.force_double_quotes', $config['force_double_quotes']);
        $container->setParameter('illarra.email_bundle.renderer.generate_plain', $config['generate_plain']);
        $container->setParameter('illarra.email_bundle.renderer.layout_var', $config['layout_var']);
        $container->setParameter('illarra.email_bundle.renderer.subject_var', $config['subject_var']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
