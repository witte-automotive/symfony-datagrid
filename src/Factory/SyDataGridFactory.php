<?php
namespace Witte\Datagrid\Factory;

use Doctrine\ORM\QueryBuilder;
use Witte\Datagrid\DataGrid\SyDataGrid;
use Witte\Datagrid\Service\PaginationService;
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
        $alias = current($grid->dataSource->getAllAliases()) ?? 'f';

        $page = $query->getInt('page', 1);
        $perPage = $query->getInt('perPage', 10);

        $filters = $query->all('filters');
        $sort = $query->all('sorted');

        if (!empty($sort) && is_array($sort)) {
            $em = $grid->dataSource->getEntityManager();
            $entity = $grid->dataSource->getRootEntities()[0];

            foreach ($sort as $position => $id) {
                $em->createQueryBuilder()
                    ->update($entity, $alias)
                    ->set("$alias.{$grid->getSortableColumn()}", ':pos')
                    ->where("$alias.id = :id")
                    ->setParameter('pos', ++$position)
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->execute();
            }

            if (!empty($filters['order']) && is_array($filters['order'])) {
                $filters['order'] = [$grid->getSortableColumn(), 'asc'];
            }

            return [
                'html' => '',
                'pagination' => ''
            ];
        }

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
            $column = array_key_first($filters['order']);
            $dir = $filters['order'][$column];

            if (in_array($dir, ['asc', 'desc'], true)) {
                $grid->dataSource
                    ->orderBy("$alias.$column", $dir);
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
            'html' => $this->twig->render('@SyDataGrid/grid/grid.html.twig', [
                'grid' => $grid
            ])
        ];
    }

}