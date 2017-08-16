<?php

namespace Rogercbe\TableSorter;

use Illuminate\Database\Eloquent\Relations\HasOne;

trait Sortable
{
    use SortableLinks;

    /**
     * Sorts the query by the given column and direction.
     *
     * @param $query
     * @param string|null $defaultSortColumn
     * @param string $direction
     * @return mixed
     */
    public function scopeSortable($query, $defaultSortColumn = null, $direction = 'asc')
    {
        if ($this->sortingIsActive()) {
            return $this->buildQuery(
                $query,
                collect(request()->only(['sort', 'direction']))
            );
        }

        if (! is_null($defaultSortColumn)) {
            return $query->orderBy($defaultSortColumn, $direction);
        }

        return $query;
    }

    /**
     * Creates the queries needed to apply the sorting.
     *
     * @param $query
     * @param $sortParams
     * @return mixed
     */
    private function buildQuery($query, $sortParams)
    {
        $column = $sortParams->get('sort');
        $direction = $sortParams->get('direction');

        if ($this->columnIsRelated($column)) {
            $relatedModel = $this->getRelatedModel($column);
            $parameters = collect([
                'column' => $this->getRelatedSortColumn($column),
                'direction' => $direction
            ]);
            $relation = $query->getRelation($relatedModel);
            return $query->join(
                $this->relatedTable($relation),
                $this->parentPrimaryKey($relation),
                '=',
                $this->relatedPrimaryKey($relation)
            )
                ->select(
                    $this->parentTable($relation) . '.*',
                    $this->getRelatedSelectAttribute($relation, $column)
                )
                ->orderBy(
                    $parameters->get('column'),
                    $parameters->get('direction')
                );
        }

        return $query->orderBy($column, $direction);
    }

    private function getRelatedSelectAttribute($relation, $column)
    {
        return implode('.', [
                $this->relatedTable($relation),
                $this->getSortColumn($column)
            ]) . ' as ' . $this->getRelatedSortColumn($column);
    }

    private function parentTable($relation)
    {
        return $relation->getParent()->getTable();
    }

    private function relatedTable($relation)
    {
        return $relation->getRelated()->getTable();
    }

    private function relatedPrimaryKey($relation)
    {
        if ($relation instanceof HasOne) {
            return $relation->getQualifiedForeignKeyName();
        }
        return $relation->getQualifiedOwnerKeyName();
    }

    private function parentPrimaryKey($relation)
    {
        if ($relation instanceof HasOne) {
            return $relation->getQualifiedParentKeyName();
        }
        return $relation->getQualifiedForeignKey();
    }

    private function sortingIsActive()
    {
        return request()->has('sort') && request()->has('direction');
    }

    private function getSortColumn($column)
    {
        return $this->parseRelation($column)->last();
    }

    private function getRelatedSortColumn($column)
    {
        return implode('_', [
            $this->parseRelation($column)->first(),
            $this->parseRelation($column)->last()
        ]);
    }

    private function getRelatedModel($column)
    {
        return $this->parseRelation($column)
            ->first();
    }

    /**
     * Parses the column to an array of relations.
     *
     * @param $column
     * @return \Illuminate\Support\Collection
     */
    private function parseRelation($column)
    {
        return collect(
            explode('.', $column)
        );
    }

    /**
     * Check if the given column belongs to a relation.
     *
     * @param $column
     * @return bool|int
     */
    private function columnIsRelated($column)
    {
        return strpos($column, '.');
    }
}