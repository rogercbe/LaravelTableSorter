<?php

namespace Rogercbe\TableSorter\Paginators;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\HtmlString;

class SortLinksLengthAwarePaginator extends LengthAwarePaginator
{
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

    /**
     * Renders paginator with the given view.
     *
     * @param string|null $view
     * @return string
     */
    public function pagination($view = null)
    {
        return $this->appends([
            'search' => request('search'),
            'sort' => request('sort'),
            'direction' => request('direction')
        ])->links($view);
    }

    /**
     * Renders sorting links with the given view.
     *
     * @param string|null $view
     * @return HtmlString
     */
    public function sortLinks($view = null)
    {
        return new HtmlString(view($this->sortLinksView ?: $view, [
            'headers' => $this->parseTableHeaders()
        ])->render());
    }

    /**
     * Creates an array of sorting options.
     *
     * @return array
     */
    private function parseTableHeaders()
    {
        return $this->tableHeaders->map(function($header, $key) {
            if (is_array($header)) {
                return new SortOptions($key, $header);
            }

            return new SortOptions($header);
        });
    }
}