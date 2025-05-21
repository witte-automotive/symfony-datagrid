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

    /**
     * @var Action[]
     */
    public array $actions = [];
    public Paginated $data;
    public string $resetUrl;

    public array $perPageOptions = [
        10,
        25,
        50
    ];

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

    public function addAction(ActionTypeEnum $type): Action
    {
        $action = new Action($type);
        $this->actions[] = $action;
        return $action;
    }

    public function hasSearchable(): bool
    {
        return array_any($this->columns, fn($it) => $it->searchable);
    }

    public function hasActions(): bool
    {
        return count($this->actions) !== 0;
    }

    public function jsonPaginationData(): string
    {
        $array = json_decode(json_encode($this->data), true);

        unset($array['data']);

        return json_encode($array);
    }

}
