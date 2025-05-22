<?php
namespace SyDataGrid;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
class SyDataGridBundle extends AbstractBundle
{
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('twig_component', [
            'defaults' => [
                'SyDataGrid\Twig\Component\\' => [
                    'template_directory' => '@SyDataGrid/components/',
                    'name_prefix' => 'SyDataGrid',
                ],
            ],
        ]);
    }
}