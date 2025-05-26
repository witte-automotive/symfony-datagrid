<?php
namespace Witte\SyDatagrid\DataGrid;

use App\Service\SyDataGridColumnService;
use Witte\SyDatagrid\DTO\SeachableColumnOptions;
use Witte\SyDatagrid\Enum\ColumnTypeEnum;
use Witte\SyDatagrid\Enum\SearchableColumnEnum;

class Column
{
    private ColumnTypeEnum $type = ColumnTypeEnum::TEXT;
    private SeachableColumnOptions|null $searchable = null;
    private mixed $callback = null;
    private array $classes = [];
    private bool $sortable = true;

    public function __construct(public string $key, public string $label)
    {
    }

    public function setSortable(bool $value = false)
    {
        $this->sortable = $value;
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function setType(ColumnTypeEnum $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): ColumnTypeEnum
    {
        return $this->type;
    }

    public function isSearchable(): bool
    {
        return $this->searchable !== null;
    }

    public function getSearchable(): SeachableColumnOptions
    {
        return $this->searchable;
    }


    /**
     * @param callable(mixed): string $callback
     */
    public function setCallback(mixed $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function setClasses(array $classes)
    {
        $this->classes = $classes;
        return $this;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getCallback(): mixed
    {
        return $this->callback;
    }

    public function setSearchable(SearchableColumnEnum|null $type = SearchableColumnEnum::QUERY, array $options = []): self
    {
        SyDataGridColumnService::validateSearchableOptions($type, $options);

        $this->searchable = new SeachableColumnOptions($type, $options);
        return $this;
    }

    public function value($row): string
    {
        $val = SyDataGridColumnService::resolveValue($this, $row);
        return $val;
    }
}