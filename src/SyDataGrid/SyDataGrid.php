<?php
namespace App\SyDataGrid;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class SyDataGrid
{
    public const EMPTY_PLACEHOLDER = '---';
    /**
     * @var Column[]
     */
    public array $columns;
    public Paginated $data;
    public string $resetUrl;

    public function __construct(public QueryBuilder $dataSource, string $resetUrl)
    {
        $this->resetUrl = $resetUrl;
        $this->data = Service::transformData($dataSource);
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
