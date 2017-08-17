<?php

namespace Rogercbe\TableSorter\Paginators;

class SortOptions
{
    private $key;
    private $options;

    function __construct($key, $options = [])
    {
        $this->key = $key;
        $this->options = $options;
    }

    public function title()
    {
        if (! $this->getProperty('title')) {
            return ucfirst(
                str_replace(['.', '_'], ' ', $this->key)
            );
        }

        return $this->getProperty('title');
    }

    public function key()
    {
        return $this->key;
    }

    public function sortable()
    {
        if (! $this->getProperty('sortable')) {
            return true;
        }

        return $this->getProperty('sortable') !== 'false';
    }

    public function classes()
    {
        if (is_array($this->getProperty('class'))) {
            return implode(' ', $this->getProperty('class'));
        }

        return $this->getProperty('class');
    }

    public function path($direction = 'asc')
    {
        $search = request('search') ?: '';

        $wildcard = request()->has('search') ? "?search={$search}&" : '?';

        return "{$wildcard}sort={$this->key}&direction={$direction}";
    }

    private function getProperty($property)
    {
        if (array_key_exists($property, $this->options)) {
            return $this->options[$property];
        }

        return false;
    }

    public function __get($name)
    {
        if (! method_exists($this, $name)) {
            throw new \Exception("Method $name does not exists");
        }

        return $this->$name();
    }
}