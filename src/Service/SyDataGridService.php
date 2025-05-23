<?php
namespace Witte\SyDatagrid\Service;

use Doctrine\ORM\QueryBuilder;
use Exception;
use Witte\SyDatagrid\DataGrid\Column;
use Witte\SyDatagrid\DataGrid\SyDataGrid;
use Witte\SyDatagrid\DTO\Paginated;
use Witte\SyDatagrid\Enum\ColumnTypeEnum;

final readonly class SyDataGridService
{
    public static function transformData(QueryBuilder $dataSource): Paginated
    {
        return PaginationService::paginate($dataSource);
    }

    public static function resolveValue(Column $column, mixed $row): string
    {
        $val = null;

        if ($column->callback) {
            $value = call_user_func($column->callback, $row);
            if (empty($value)) {
                $value = '---';
            }
            return $value;
        }

        $method = 'get' . ucfirst($column->key);

        if (method_exists($row, $method)) {
            $val = $row->$method();
        } else {
            throw new \InvalidArgumentException("Method '{$method}' does not exist on the given object for column '{$column->label}'.");
        }

        if ($column->type && $val) {
            if ($column->type === ColumnTypeEnum::DATE || $column->type === ColumnTypeEnum::DATETIME) {
                try {
                    if (is_string($val)) {
                        $date = new \DateTimeImmutable($val);
                    } elseif ($val instanceof \DateTimeInterface) {
                        $date = $val;
                    } else {
                        throw new \InvalidArgumentException('Invalid date value');
                    }

                    if ($column->type === ColumnTypeEnum::DATE) {
                        $val = $date->format('d.m.Y');
                    }

                    if ($column->type === ColumnTypeEnum::DATETIME) {
                        $val = $date->format('d.m.Y H:i:s');
                    }
                } catch (Exception $e) {
                    $val = null;
                }
            }
        }

        if ((!is_string($val) && !is_numeric($val)) && $val !== null && !($val instanceof \Stringable)) {
            throw new Exception("Cannot resolve value: missing type setting for non-string column with key \"{$column->key}\".");
        }

        if ($val === null || (is_string($val) && strlen($val) === 0)) {
            $val = SyDataGrid::EMPTY_PLACEHOLDER;
        }

        return $val;
    }
}