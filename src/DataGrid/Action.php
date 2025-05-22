<?php
namespace SyDataGrid\DataGrid;

use SyDataGrid\Enum\ActionTypeEnum;
class Action
{
    public ActionTypeEnum $type;
    public mixed $linkCallback = null;
    public string $classes = '';
    public function __construct(ActionTypeEnum $type)
    {
        $this->type = $type;
    }

    /**
     * @param callable $callback
     */
    public function setLinkCallback($callback)
    {
        $this->linkCallback = $callback;
    }

    public function setClasses(string $classes)
    {
        $this->classes = $classes;
    }

    public function link(mixed $row)
    {
        return $this->linkCallback !== null ? call_user_func($this->linkCallback, $row) : '#';
    }
}