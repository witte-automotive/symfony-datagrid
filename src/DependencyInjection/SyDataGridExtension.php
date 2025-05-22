<?php
namespace SyDataGrid\DependencyInjection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class SyDataGridExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('twig.yaml');
        
        $container->prependExtensionConfig('twig', [
            'paths' => [
                __DIR__ . '/../Resources/view' => 'SyDataGrid',
            ],
        ]);
    }
}