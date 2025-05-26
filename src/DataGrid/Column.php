<?php
namespace Witte\SyDatagrid\DataGrid;

use InvalidArgumentException;
use Witte\SyDatagrid\Service\SyDataGridColumnService;
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
    private array $typeOptions = [];
    public function __construct(public string $key, public string $label)
    {
    }

    public function setSortable(bool $value = true)
    {
        $this->sortable = $value;
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function setType(ColumnTypeEnum $type, array $options = [])
    {
        if ($type->value === ColumnTypeEnum::BOOL->value && array_diff(['true', 'false'], array_keys($options)) !== []) {
            throw new InvalidArgumentException('Column type BOOL must define options with true and false, like array("true" => "Yes", "false" => "No")');
        }
        $this->typeOptions = $options;
        $this->type = $type;
        return $this;
    }

    public function getType(): ColumnTypeEnum
    {
        return $this->type;
    }

      public function getTypeOptions(): array
    {
        return $this->typeOptions;
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