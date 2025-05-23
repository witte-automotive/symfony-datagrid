<?php
namespace Witte\SyDatagrid\Enum;

enum SearchableColumnEnum: int
{
    case QUERY = 1;
    case OPTIONS_SELECT = 2;

    public function enabledOptions(): array
    {
        return match ($this) {
            self::QUERY => ['__NO_OPTIONS__'],
            self::OPTIONS_SELECT => ['options' => ['required'], 'placeholder', 'default_key'],
        };
    }
}

