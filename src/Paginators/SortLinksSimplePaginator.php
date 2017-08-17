<?php

namespace Rogercbe\TableSorter\Paginators;

use Illuminate\Pagination\Paginator;

class SortLinksSimplePaginator extends Paginator
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
     * Create a SortLinksSimplePaginator instance.
     *
     * @param mixed $items
     * @param int $perPage
     * @param null $currentPage
     * @param array $options
     * @param $tableHeaders
     * @param null $sortLinksView
     */
    public function __construct($items, $perPage, $currentPage = null, array $options = [], $tableHeaders, $sortLinksView = null)
    {
        $this->tableHeaders = collect($tableHeaders);
        $this->sortLinksView = $sortLinksView ?: 'tablesorter::headers';

        parent::__construct($items, $perPage, $currentPage, $options);
    }
}