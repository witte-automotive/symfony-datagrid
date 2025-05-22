<?php
namespace SyDataGrid\SyDataGrid;

use SyDataGrid\Enum\ColumnTypeEnum;
use SyDataGrid\Service\SyDataGridService;

class Column
{
    public ColumnTypeEnum $type = ColumnTypeEnum::TEXT;
    public bool $searchable = false;
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
        $val = SyDataGridService::resolveValue($this, $row);
        return $val;
    }
}