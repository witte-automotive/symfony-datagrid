<?php
namespace App\SyDataGrid;

use Doctrine\ORM\QueryBuilder;

class SyDataGrid
{
    public const EMPTY_PLACEHOLDER = '---';
    /**
     * @var Column[]
     */
    public array $columns;
    public Paginated $data;
    public string|null $dataSourceClass = null;

    public function __construct(QueryBuilder $dataSource)
    {
        $this->data = Service::transformData($dataSource, $this);
    }

    public function addColumn(string $key, string $label): Column
    {
        $column = new Column($key, $label);
        $this->columns[] = $column;
        return $column;
    }

    public function jsonPaginationData(): string
    {
        $array = json_decode(json_encode($this->data), true);

        unset($array['data']);

        return json_encode($array);
    }
}
