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

        $page = $query->getInt('page', 1);
        $perPage = $query->getInt('perPage', 10);

        $filters = $query->all('filters');

        $alias = current($grid->dataSource->getAllAliases()) ?? 'f';
        if (!empty($filters['search']) && is_array($filters['search'])) {

            foreach ($filters['search'] as $search) {
                if (!is_array($search))
                    continue;

                $column = array_key_first($search);
                $value = $search[$column];

                if ($column && $value !== '') {
                    $grid->dataSource
                        ->andWhere("$alias.$column LIKE :search_$column")
                        ->setParameter("search_$column", "%$value%");
                }
            }
        }

        if (!empty($filters['order']) && is_array($filters['order'])) {
            $orderColumn = array_key_first($filters['order']);
            $direction = strtolower($filters['order'][$orderColumn]);

            if (in_array($direction, ['asc', 'desc'], true)) {
                $grid->dataSource
                    ->orderBy("$alias.$orderColumn", $direction);
            }
        }

        $grid->data = PaginationService::paginate(
            $grid->dataSource,
            $page,
            $perPage
        );

        $grid->data->setFilters($filters);

        return [
            'pagination' => $grid->data->witnotData(),
            'html' => $this->twig->render('grid/grid.html.twig', [
                'grid' => $grid
            ])
        ];
    }
}