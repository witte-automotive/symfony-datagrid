<?php

namespace SyDataGrid\DependencyInjection;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SyDataGridTwigExtension extends AbstractExtension
{
    public function __construct(private \Twig\Environment $twig)
    {
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sydatagrid', [$this, 'renderSydatagrid'], ['is_safe' => ['html']]),
        ];
    }

    public function renderSydatagrid($grid): string
    {
        return $this->twig->render(__DIR__ . '/../Resources/view/grid/grid.html.twig', ['grid' => $grid]);
    }
}
