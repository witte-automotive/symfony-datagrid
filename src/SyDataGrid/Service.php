<?php
namespace App\SyDataGrid;

use Doctrine\ORM\QueryBuilder;

final readonly class Service
{
    public static function transformData(array|QueryBuilder $dataSource, SyDataGrid $grid, bool $isPaginated = true): Paginated|array
    {
        if ($isPaginated === false) {
            if (is_array($dataSource)) {
                return $dataSource;
            } else {
                return $dataSource->getQuery()->getResult();
            }
        } else {
            $p = PaginationService::paginate($dataSource);
            $firstItem = current($p->data);
            $grid->dataSourceClass = $firstItem ? get_class($firstItem) : null;

            return PaginationService::paginate($dataSource);
        }
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

        if (is_object($row)) {
            if (method_exists($row, $method)) {
                $val = $row->$method();
            } else {
                throw new \InvalidArgumentException("Method '{$method}' does not exist on the given object for column '{$column->label}'.");
            }
        } else if (is_array($row)) {
            if (array_key_exists($column->key, $row)) {
                $val = $row[$column->key];
            } else {
                throw new \InvalidArgumentException("Key '{$column->key}' does not exist in the given array for column '{$column->label}'.");
            }
        }

        if ($column->type && $val) {
            if ($column->type === ColumnTypeEnum::DATE) {
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

        if ($val === null || strlen($val) === 0) {
            $val = SyDataGrid::EMPTY_PLACEHOLDER;
        }

        return $val;
    }
}