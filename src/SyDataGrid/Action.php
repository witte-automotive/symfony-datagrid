<?php
namespace App\SyDataGrid;
class Action
{
    public ActionTypeEnum $type;
    public mixed $callback = null;
    public string $classes = '';
    public function __construct(ActionTypeEnum $type)
    {
        $this->type = $type;
    }

    /**
     * @param callable(mixed) $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    public function setClasses(string $classes)
    {
        $this->classes = $classes;
    }

    public function link(mixed $row)
    {
        return $this->callback !== null ? call_user_func($this->callback, $row) : '';
    }
}