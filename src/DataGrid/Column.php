<?php
namespace Witte\SyDatagrid\DataGrid;

use Witte\SyDatagrid\Enum\ColumnTypeEnum;
use Witte\SyDatagrid\Service\SyDataGridService;

class Column
{
    public ColumnTypeEnum $type = ColumnTypeEnum::TEXT;
    public bool $searchable = false;
    public mixed $callback = null;
    public function __construct(public string $key, public string $label)
    {
    }
    public function setType(ColumnTypeEnum $type)
    {
        $this->type = $type;
        return $this;
    }
    public function setCallback(mixed $callback)
    {
        $this->callback = $callback;
        return $this;
    }
    public function setSearchable(bool $value = true): self
    {
        $this->searchable = $value;
        return $this;
    }

    public function value($row): string
    {
        $val = SyDataGridService::resolveValue($this, $row);
        return $val;
    }
}