<?php
namespace Witte\SyDatagrid\DataGrid;

use Witte\SyDatagrid\Enum\ColumnAttributedTagsEnum;
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
    private array $attributes = [];
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

    public function setSearchable(SearchableColumnEnum|null $type = SearchableColumnEnum::QUERY, array $options = []): self
    {
        SyDataGridColumnService::validateSearchableOptions($type, $options);

        $this->searchable = new SeachableColumnOptions($type, $options);
        return $this;
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

    public function getCallback(): mixed
    {
        return $this->callback;
    }

    public function value($row): string
    {
        $val = SyDataGridColumnService::resolveValue($this, $row);
        return $val;
    }

    public function setAttributes(ColumnAttributedTagsEnum $tag, array $attributes)
    {
        $this->attributes[$tag->value] = $attributes;
        return $this;
    }

    public function getAttribute(int $val): array
    {
        return $this->attributes[$val] ?? [];
    }
}