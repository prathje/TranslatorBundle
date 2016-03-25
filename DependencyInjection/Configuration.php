<?php

namespace Knp\Bundle\TranslatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for the bundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('knplabs_translator');

        $rootNode
            ->children()
                ->booleanNode('include_vendor_assets')->defaultTrue()->end()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->variableNode('spanning_element')->defaultValue(array(
                'attr' => array(),
                'keys' => array(
                    'id' => 'data-translation-id',
                    'locale' => 'data-translation-locale',
                    'domain' => 'data-translation-domain',
                    'type' => 'data-translation-type'
                ),
                'tag' => 'span'))->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

