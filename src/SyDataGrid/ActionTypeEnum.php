<?php
namespace SyDataGrid\SyDataGrid;

enum ActionTypeEnum: string
{
    case CREATE = 'create';
    case EDIT = 'edit';
    case SHOW = 'show';
    case DELETE = 'delete';
}