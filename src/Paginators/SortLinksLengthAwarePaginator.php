<?php

namespace Rogercbe\TableSorter\Paginators;

use Illuminate\Pagination\LengthAwarePaginator;

class SortLinksLengthAwarePaginator extends LengthAwarePaginator
{
    use SortLinks;

    /**
     * Table headers and sort options.
     *
     * @var array
     */
    protected $tableHeaders;

    /**
     * View that will render the sort links.
     *
     * @var string
     */
    protected $sortLinksView;

    /**
     * Create a new paginator instance.
     *
     * @param mixed $items
     * @param int $total
     * @param int $perPage
     * @param int|null $currentPage
     * @param array $options
     * @param $tableHeaders
     * @param string|null $sortLinksView
     */
    public function __construct($items, $total, $perPage, $currentPage = null, array $options = [], $tableHeaders, $sortLinksView = null)
    {
        $this->tableHeaders = collect($tableHeaders);
        $this->sortLinksView = $sortLinksView ?: 'tablesorter::headers';

        parent::__construct($items, $total, $perPage, $currentPage, $options);
    }
}