<?php
namespace SyDataGrid\Service;

use Doctrine\ORM\QueryBuilder;
use Exception;
use SyDataGrid\DataGrid\Column;
use SyDataGrid\DataGrid\SyDataGrid;
use SyDataGrid\DTO\Paginated;
use SyDataGrid\Enum\ColumnTypeEnum;

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
                } catch (\Exception $e) {
                    $val = null;
                }
            }
        }

        if (!is_string($val)) {
            throw new Exception("Cannot resolve value: missing type setting for non-string column with key \"{$column->type->value}\".");
        }

        if ($val === null || (is_string($val) && strlen($val)) === 0) {
            $val = SyDataGrid::EMPTY_PLACEHOLDER;
        }

        return $val;
    }
}