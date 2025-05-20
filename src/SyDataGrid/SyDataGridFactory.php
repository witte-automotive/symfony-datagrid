<?php
namespace App\SyDataGrid;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class SyDataGridFactory
{
    public function __construct(private Environment $twig)
    {
    }

    public function create(QueryBuilder $dataSource, string $resetUrl): SyDataGrid
    {
        return new SyDataGrid($dataSource, $resetUrl);
    }

    public function refresh(SyDataGrid $grid, Request $request): array
    {
        $query = $request->query;

        $grid->data = PaginationService::paginate(
            $grid->dataSource,
            $query->get('page'),
            $query->get('perPage')
        );

        return [
            'pagination' => $grid->data->witnotData(),
            'html' => $this->twig->render('grid/grid.html.twig', [
                'grid' => $grid
            ])
        ];
    }

}