<?php
namespace Witte\Datagrid\Service;

use Doctrine\ORM\QueryBuilder;
use Witte\Datagrid\DTO\Paginated;

class PaginationService
{
    public static function paginate(QueryBuilder $dataSource, int|null $page = null, int|null $perPage = null): Paginated
    {
        $page ??= 1;
        $total = 0;
        $perPage ??= 10;
        $pageRange = 3;
        $data = [];

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

        return new Paginated($total, $page, $perPage, $pageRange, $data);
    }
}