<?php
namespace App\SyDataGrid;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class SyDataGridFactory
{
    private QueryBuilder $dataSource;
    private SyDataGrid $grid;
    public function __construct(private Environment $twig)
    {
    }

    public function create(QueryBuilder $dataSource, string $resetUrl): SyDataGrid
    {
        $this->dataSource = $dataSource;
        $this->grid = new SyDataGrid($dataSource, $resetUrl);
        return $this->grid;
    }

    public function update(Request $request): array
    {
        $query = $request->query;

        $this->grid->data = PaginationService::paginate(
            $this->dataSource,
            $query->get('page'),
            $query->get('perPage')
        );

        return [
            'pagination' => $this->grid->data->witnotData(),
            'html' => $this->twig->render('grid/grid.html.twig', [
                'grid' => $this->grid
            ])
        ];
    }

}