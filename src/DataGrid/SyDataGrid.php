<?php
namespace SyDataGrid\DataGrid;

use Doctrine\ORM\QueryBuilder;
use SyDataGrid\DataGrid\Action;
use SyDataGrid\DTO\Paginated;
use SyDataGrid\Enum\ActionTypeEnum;
use SyDataGrid\Service\SyDataGridService;
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
    private array $defaultSort = [];
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

    public function setDefaultDataSource(string $column, string $dir)
    {
        $this->data->filters['order'] = [$column => $dir];
    }

    public function getDefaultSort(): array
    {
        return $this->defaultSort;
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

    public function getSortableColumn(): string|null
    {
        return $this->sortableColumn;
    }

    public function jsonPaginationData(): string
    {
        $array = json_decode(json_encode($this->data), true);

        unset($array['data']);

        return json_encode($array);
    }

}
