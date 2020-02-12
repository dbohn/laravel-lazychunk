<?php


namespace Dababo\LazyChunk;


use Illuminate\Support\LazyCollection;

class LazyChunkMixin
{
    public function lazyChunk()
    {
        return function ($count, callable $callback = null) {
            return new LazyCollection(function () use ($count, $callback) {

                $this->enforceOrderBy();

                $page = 1;

                do {
                    // We'll execute the query for the given page and get the results. If there are
                    // no results we can just break and return from here. When there are results
                    // we will call the callback with the current chunk of these results here.
                    $results = $this->forPage($page, $count)->get();

                    $countResults = $results->count();

                    if ($countResults == 0) {
                        break;
                    }

                    yield $page => $results;

                    if ($callback !== null && $callback($results, $page) === false) {
                        break;
                    }

                    unset($results);

                    $page++;
                } while ($countResults == $count);
            });
        };
    }

    public function flatLazyChunk()
    {
        return function ($count, callable $callback = null) {
            return $this->lazyChunk($count, $callback)->flatten(1);
        };
    }

    public function lazyChunkById()
    {
        return function ($count, callable $callback = null, $column = null, $alias = null) {
            return new LazyCollection(function () use ($count, $callback, $column, $alias) {
                $column = $column ?? $this->defaultKeyName();

                $alias = $alias ?? $column;

                $lastId = null;

                do {
                    $clone = clone $this;

                    // We'll execute the query for the given page and get the results. If there are
                    // no results we can just break and return from here. When there are results
                    // we will call the callback with the current chunk of these results here.
                    $results = $clone->forPageAfterId($count, $lastId, $column)->get();

                    $countResults = $results->count();

                    if ($countResults == 0) {
                        break;
                    }

                    yield $results;

                    // On each chunk result set, we will pass them to the callback and then let the
                    // developer take care of everything within the callback, which allows us to
                    // keep the memory low for spinning through large result sets for working.
                    if ($callback !== null && $callback($results) === false) {
                        return false;
                    }

                    $lastId = $results->last()->{$alias};

                    unset($results);
                } while ($countResults == $count);
            });
        };
    }

    public function flatLazyChunkById()
    {
        return function ($count, callable $callable = null, $column = null, $alias = null) {
            return $this->lazyChunkById($count, $callable, $column, $alias)->flatten(1);
        };
    }
}