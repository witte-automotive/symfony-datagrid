<?php
namespace App\Service;

use Exception;
use InvalidArgumentException;
use Witte\SyDatagrid\DataGrid\Column;
use Witte\SyDatagrid\DataGrid\SyDataGrid;
use Witte\SyDatagrid\Enum\ColumnTypeEnum;
use Witte\SyDatagrid\Enum\SearchableColumnEnum;

class SyDataGridColumnService
{
    public static function resolveValue(Column $column, mixed $row): string
    {
        $val = null;

        if ($column->getCallback()) {
            $value = call_user_func($column->getCallback(), $row);
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

        if ($column->getType() && $val) {
            if ($column->getType() === ColumnTypeEnum::DATE || $column->getType() === ColumnTypeEnum::DATETIME) {
                try {
                    if (is_string($val)) {
                        $date = new \DateTimeImmutable($val);
                    } elseif ($val instanceof \DateTimeInterface) {
                        $date = $val;
                    } else {
                        throw new InvalidArgumentException('Invalid date value');
                    }

                    if ($column->getType() === ColumnTypeEnum::DATE) {
                        $val = $date->format('d.m.Y');
                    }

                    if ($column->getType() === ColumnTypeEnum::DATETIME) {
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

    public static function validateSearchableOptions(SearchableColumnEnum $type, array $options): void
    {
        $enabledOptions = $type->enabledOptions();

        $requiredKeys = array_keys(array_filter(
            $enabledOptions,
            fn($value) => is_array($value) && in_array('required', $value, true)
        ));

        $missingKeys = array_diff($requiredKeys, array_keys($options));

        if (!empty($missingKeys)) {
            $keysString = implode(', ', $missingKeys);
            throw new InvalidArgumentException("Missing required searchable options: $keysString");
        }
    }

}