<?php
namespace Witte\SyDatagrid\DataGrid;

use Doctrine\ORM\QueryBuilder;
use Witte\SyDatagrid\DataGrid\Action;
use Witte\SyDatagrid\DTO\Paginated;
use Witte\SyDatagrid\Enum\ActionTypeEnum;
use Witte\SyDatagrid\Service\SyDataGridService;
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
    private null|string $sortableColumn = null;
    private null|string $primaryKey = null;
    private array $defaultOrder = [];
    public array $perPageOptions = [
        10,
        25,
        50
    ];

    public function __construct(public QueryBuilder $dataSource, string $resetUrl)
    {
        $this->resetUrl = $resetUrl;
        $this->data = SyDataGridService::transformData($dataSource);
    }

    public function addColumn(string $key, string $label): Column
    {
        $column = new Column($key, $label);
        $this->columns[] = $column;
        return $column;
    }
    public function setDefaultOrder(string $column, string $dir)
    {
        $this->data->filters['order'] = [$column => $dir];
    }

    public function addAction(ActionTypeEnum $type): Action
    {
        $action = new Action($type);
        $this->actions[] = $action;
        return $action;
    }

    public function hasActions(): bool
    {
        return count($this->actions) !== 0;
    }

    public function setSortable(string $column)
    {
        if (!$this->primaryKey) {
            throw new \Exception('To use sortable, you must define a primary key.');
        }
        $this->sortableColumn = $column;
    }

    public function setPrimaryKey(string $key)
    {
        $this->primaryKey = $key;
    }

    public function getPrimaryKey(): string|null
    {
        return $this->primaryKey;
    }

    public function getSortableColumnName(): string|null
    {
        return $this->sortableColumn;
    }

    public function hasSearchableColumn(): bool
    {
        return count(array_filter($this->columns, fn(Column $it): bool => $it->isSearchable())) > 0;
    }

    public function jsonPaginatedData(): string
    {
        $array = json_decode(json_encode($this->data), true);

        unset($array['data']);

        return json_encode($array);
    }
}
