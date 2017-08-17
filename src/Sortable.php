<?php

namespace Rogercbe\TableSorter;

use Illuminate\Database\Eloquent\Relations\HasOne;

trait Sortable
{
    /**
     * Sorts the query by the given column and direction.
     *
     * @param $query
     * @param string|null $defaultSortColumn
     * @param string $direction
     * @return \Illuminate\Database\Query\Builder
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
     * @return \Illuminate\Database\Query\Builder
     */
    private function buildQuery($query, $sortParams)
    {
        $column = $sortParams->get('sort');
        $direction = $sortParams->get('direction');

        if ($this->columnIsRelated($column)) {
            $relations = $this->getRelatedModel($column);

            $parameters = collect([
                'column' => $this->getSelectAsName($column),
                'direction' => $direction
            ]);

            return $this->performJoins($query, $relations)
                ->select(
                    $this->getTable() . '.*',
                    $this->getRelatedSelectAttribute($query, $relations, $column)
                )
                ->orderBy(
                    $parameters->get('column'),
                    $parameters->get('direction')
                );
        }

        return $query->orderBy($column, $direction);
    }

    /**
     * Perform the necessary joins to construct the query.
     *
     * @param $query
     * @param $relations
     * @return \Illuminate\Database\Query\Builder
     */
    private function performJoins($query, $relations)
    {
        $relation = $query->getRelation($relations->first());

        foreach ($relations as $key => $relationName) {
            $query = $query->join(
                $this->relatedTable($relation),
                $this->parentPrimaryKey($relation),
                '=',
                $this->relatedPrimaryKey($relation)
            );

            if ($key < $relations->count() - 1) {
                $relation = $relation->getQuery()
                    ->getRelation($relations[$key + 1]);
            }
        }

        return $query;
    }

    /**
     * Creates the string for the related attribute being sorted.
     *
     * @param $query
     * @param $relations
     * @param $column
     * @return string
     */
    private function getRelatedSelectAttribute($query, $relations, $column)
    {
        $relation = $query->getRelation($relations->first());

        foreach ($relations as $key => $relationName) {
            if ($key < $relations->count() - 1) {
                $relation = $relation->getQuery()
                    ->getRelation($relations[$key + 1]);
            }
        }

        $sortColumn = implode('.', [
            $relation->getRelated()->getTable(),
            $this->getRelatedSortAttribute($column)
        ]);

        return $sortColumn . ' as ' . $this->getSelectAsName($column);
    }

    /**
     * Get related table name.
     *
     * @param $relation
     * @return string
     */
    private function relatedTable($relation)
    {
        return $relation->getRelated()->getTable();
    }

    /**
     * Get related attribute primary key.
     *
     * @param $relation
     * @return string
     */
    private function relatedPrimaryKey($relation)
    {
        if ($relation instanceof HasOne) {
            return $relation->getQualifiedForeignKeyName();
        }

        return $relation->getQualifiedOwnerKeyName();
    }

    /**
     * Get primary key from the parent table.
     *
     * @param $relation
     * @return string
     */
    private function parentPrimaryKey($relation)
    {
        if ($relation instanceof HasOne) {
            return $relation->getQualifiedParentKeyName();
        }

        return $relation->getQualifiedForeignKey();
    }

    /**
     * Determine if sorting is active.
     *
     * @return bool
     */
    private function sortingIsActive()
    {
        return request()->has('sort') && request()->has('direction');
    }

    private function getRelatedModel($column)
    {
        $relations = $this->parseRelation($column);
        $relations->pop();

        return $relations;
    }

    /**
     * Created the select as name of the column,
     * merging the table and field.
     *
     * @param $column
     * @return string
     */
    private function getSelectAsName($column)
    {
        return $this->parseRelation($column)->slice(
            $this->parseRelation($column)->count() - 2
        )->implode('_');
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

    /**
     * Get the related column attribute to be sorted.
     *
     * @param $column
     * @return string
     */
    private function getRelatedSortAttribute($column)
    {
        return $this->parseRelation($column)->last();
    }
}