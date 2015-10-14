<?php

namespace Knp\Bundle\TranslatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class KnpTranslatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        if (!$config['enabled']) {
            return;
        }

        $container->setParameter('knp_translator.templating.helper.spanning_element', $config['spanning_element']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('translation.xml');
        $loader->load('controller.xml');

        if (empty($config['include_vendor_assets'])) {
            $container->removeDefinition('knp_translator.response.listener.assets_injecter');
        }

        $container->setAlias('templating.helper.translator', 'knp_translator.templating.helper.translator');
    }
}
