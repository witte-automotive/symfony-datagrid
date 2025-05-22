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

        if ($container->hasDefinition('twig.loader')) {
            $container->getDefinition('twig.loader')
                ->addMethodCall('addPath', [__DIR__ . '/../Resources/views', 'SyDataGrid']);
        }

        $container->getParameterBag()->add([
            'my_alert.asset_paths' => [
                __DIR__ . '/../Resources/assets',
            ]
        ]);
    }
}
