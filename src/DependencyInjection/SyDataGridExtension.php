<?php
namespace SyDataGrid\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class SyDataGridExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $config = $loader->load('twig.yaml');

        $yamlParser = new \Symfony\Component\Yaml\Parser();
        $twigConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/../Resources/config/twig.yaml'));

        $container->prependExtensionConfig('twig', $twigConfig);
    }
}
