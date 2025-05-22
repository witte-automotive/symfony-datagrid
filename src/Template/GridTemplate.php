<?php

namespace SyDataGrid\Template;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'SyDataGrid', template: 'grid/grid.html.twig')]
class GridTemplate
{
    public mixed $grid;
}
