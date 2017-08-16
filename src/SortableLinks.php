<?php

namespace Rogercbe\TableSorter;

use Rogercbe\TableSorter\Paginators\SortLinksLengthAwarePaginator;
use Illuminate\Pagination\Paginator;

trait SortableLinks
{
    /**
     * Paginates the given query.
     *
     * @param $query
     * @param int|null $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return SortLinksLengthAwarePaginator
     */
    public function scopeSortPaginate($query, $perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->getPerPage();

        $queryBase = $query->toBase();

        $total = $queryBase->getCountForPagination();

        $results = $total
            ? $query->forPage($page, $perPage)->get($columns)
            : $this->newCollection();

        return new SortLinksLengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ], $this->tableHeaders, $this->sortLinksView);
    }
}