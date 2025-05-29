<?php
namespace Witte\SyDatagrid\DataGrid;

use Witte\SyDatagrid\Enum\ActionTypeEnum;
class Action
{
    public ActionTypeEnum $type;
    public mixed $linkCallback = null;
    public string $classes = '';
    public string $styles = '';
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
        return $this;
    }

    public function setClasses(string $classes)
    {
        $this->classes = $classes;
        return $this;
    }

    public function setStyles(string $styles)
    {
        $this->styles = $styles;
    }

    public function link(mixed $row)
    {
        return $this->linkCallback !== null ? call_user_func($this->linkCallback, $row) : '#';
    }
}