<?php

namespace Witte\SyDatagrid\DTO;

use Witte\SyDatagrid\Enum\SearchableColumnEnum;

class SeachableColumnOptions
{
    public function __construct(
        public SearchableColumnEnum $type = SearchableColumnEnum::QUERY,
        public array $options = []
    ) {
    }
}