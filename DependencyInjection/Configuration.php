<?php

namespace Illarra\EmailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('illarra_email');

        $rootNode
            ->children()
                ->booleanNode('force_double_quotes')
                    ->defaultFalse()
                    ->info('Force double quotes for HTML tag attributes')
                ->end()
                ->booleanNode('generate_plain')
                    ->defaultFalse()
                    ->info('Generate plain message from HTML')
                ->end()
                ->scalarNode('layout_var')
                    ->defaultValue('layout')
                    ->info('Name of the variable used in the twig extends tag')
                ->end()
                ->scalarNode('subject_var')
                    ->defaultValue('subject')
                    ->info('Name of the twig block used to extract the email subject')
                ->end()
                ->arrayNode('profiles')
                    ->info('Profiles to be used with Mailer service')
                    ->defaultValue([])
                    ->prototype('array')
                    ->children()
                        ->arrayNode('from')
                            ->isRequired()
                            ->info('Associative array of From emails, key must be email and value the name')
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->validate()
                                ->ifTrue($this->validateEmail())
                                    ->thenInvalid("Array key must be an email")
                                ->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('reply_to')
                            ->info('Associative array of Reply-To emails, key must be email and value the name')
                            ->useAttributeAsKey('name')
                            ->validate()
                                ->ifTrue($this->validateEmail())
                                    ->thenInvalid("Array key must be an email")
                                ->end()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    public function validateEmail()
    {
        return function ($v) {
            foreach (array_keys($v) as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return true;
                }
            }

            return false;
        };
    }
}
