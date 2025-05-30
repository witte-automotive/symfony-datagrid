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
    public array $perPageOptions = [
        10,
        25,
        50
    ];
    public string|null $title = null;
    public function __construct(public QueryBuilder $dataSource, string $resetUrl)
    {
        $this->resetUrl = $resetUrl;
        $this->data = SyDataGridService::transformData($dataSource);
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
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

    public function getOrderedColumn(): array
    {
        $order = $this->data->filters['order'] ?? null;

        if ($order === null) {
            return [
                'col' => null,
                'dir' => null
            ];
        } else {
            $col = array_key_first($order);
            return [
                'col' => $col,
                'dir' => $order[$col]
            ];
        }
    }

    public function getSearchingColumn(string $key): string
    {
        return ($this->data->filters['search'] ?? [])[$key] ?? '';
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
