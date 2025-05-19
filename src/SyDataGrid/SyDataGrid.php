<?php
namespace App\SyDataGrid;

class SyDataGrid
{
    public const EMPTY_PLACEHOLDER = '---';

    /**
     * @var Column[]
     */
    public array $columns;

    public function addColumn(string $label, string $key): Column
    {
        $column = new Column($label, $key);
        $this->columns = $column;
        return $column;
    }
}