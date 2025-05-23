<?php
namespace Witte\SyDatagrid\Service;

use Doctrine\ORM\QueryBuilder;
use Witte\SyDatagrid\DTO\Paginated;

final readonly class SyDataGridService
{
    public static function transformData(QueryBuilder $dataSource): Paginated
    {
        return PaginationService::paginate($dataSource);
    }

}