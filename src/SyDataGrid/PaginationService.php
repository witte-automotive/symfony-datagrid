<?php
namespace App\SyDataGrid;

use Doctrine\ORM\QueryBuilder;

class PaginationService
{
    public static function paginate(array|QueryBuilder $dataSource, Paginated|null $current = null): Paginated
    {
        $page = $current?->page ?? 1;
        $total = 0;
        $perPage = $current?->perPage ?? 10;
        $pageRange = 3;
        $data = [];

        if (is_array($dataSource)) {
            $total = count($dataSource);
            $data = $dataSource;
        } else {
            $qbClone = clone $dataSource;
            $total = (int) $qbClone
                ->select('COUNT(1)')
                ->resetDQLPart('orderBy')
                ->getQuery()
                ->getSingleScalarResult();

            $data = $dataSource
                ->setFirstResult(($page - 1) * $perPage)
                ->setMaxResults($perPage)
                ->getQuery()
                ->getResult();
        }

        return new Paginated($total, $page, $perPage, $pageRange, $data);
    }
}