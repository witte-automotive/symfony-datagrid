<?php
namespace App\SyDataGrid;

class Column
{
    public ColumnTypeEnum $type = ColumnTypeEnum::TEXT;
    public bool $searchable = false;
    public bool $sortable = false;
    public mixed $callback = null;
    public function __construct(public string $key, public string $label)
    {
    }
    public function setSearchable(bool $value = true): self
    {
        $this->searchable = $value;
        return $this;
    }

    public function value($row): string
    {
        $val = Service::resolveValue($this, $row);
        return $val;
    }
}