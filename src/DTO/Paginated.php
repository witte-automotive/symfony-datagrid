<?php
namespace SyDataGrid\DTO;

class Paginated
{
    public array $visiblePages = [];
    public int $totalPages;
    public array $filters = [];
    public function __construct(
        public int $total,
        public int $page,
        public int $perPage,
        public int $pageRange,
        public array $data = [],
    ) {
        $this->visiblePages = $this->getVisiblePages();
        $this->totalPages = $this->getTotalPages();
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    public function getVisiblePages(): array
    {
        $totalPages = $this->getTotalPages();
        $range = $this->pageRange;
        $current = $this->page;

        $half = (int) floor($range / 2);
        $start = max(1, $current - $half);
        $end = min($totalPages, $start + $range - 1);

        if ($end - $start + 1 < $range) {
            $start = max(1, $end - $range + 1);
        }

        return range($start, $end);
    }

    public function witnotData()
    {
        $self = new self(
            $this->total,
            $this->page,
            $this->perPage,
            $this->pageRange
        );
        $self->setFilters($this->filters);

        return $self;
    }
}