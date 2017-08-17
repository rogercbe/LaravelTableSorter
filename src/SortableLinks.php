<?php

namespace Rogercbe\TableSorter;

use Rogercbe\TableSorter\Paginators\SortLinksLengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Rogercbe\TableSorter\Paginators\SortLinksSimplePaginator;

trait SortableLinks
{
    /**
     * Paginates the given query with length awareness.
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

    /**
     * Paginates the query with a simple paginator.
     *
     * @param $query
     * @param int|null $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return SortLinksSimplePaginator
     */
    public function scopeSortSimplePaginate($query, $perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->getPerPage();

        $query->skip(($page - 1) * $perPage)->take($perPage + 1);

        $results = $query->get($columns);

        return new SortLinksSimplePaginator($results, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ], $this->tableHeaders, $this->sortLinksView);
    }
}