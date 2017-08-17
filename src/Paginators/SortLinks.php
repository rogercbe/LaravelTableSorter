<?php

namespace Rogercbe\TableSorter\Paginators;

use Illuminate\Support\HtmlString;

trait SortLinks
{
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
            'headers' => $this->getTableHeaders()
        ])->render());
    }

    /**
     * Creates an array of sorting options.
     *
     * @return array
     */
    private function getTableHeaders()
    {
        return $this->tableHeaders->map(function($header, $key) {
            return new SortOptions($key, $header);
        });
    }
}