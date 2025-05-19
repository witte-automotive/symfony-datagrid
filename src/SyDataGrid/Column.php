<?php
namespace App\SyDataGrid;

class Column
{
    public ColumnTypeEnum $type = ColumnTypeEnum::TEXT;
    public bool $searchable = false;
    public bool $sortable = false;
    public function __construct(private string $label, private string $key)
    {
    }

    public function value(): string
    {
        $val = SyDataGrid::EMPTY_PLACEHOLDER;

        return $val;
    }
}