# Laravel Lazy Chunk

This library adds the ability to work with chunked query results using lazy collections.

## Motivation

Since Laravel 6, there are Lazy Collections, which are a nice way to use the Collection interface on generated data.

One major use case for these were the database query cursors. This allows to work with a query result, as if all results were fetched,
but with one query and small memory footprint.

The issue with these cursors is, that eager loading relationships does not work.
As there is always just one result at hand, eager loads are resolved with one query each.

So if you are working with large data sets that are using relationships, you are still stuck with `chunk()` and `chunkById()`.

Sadly these are still closure methods, which this library changes.

## Usage

Using this library, you gain two new query builder methods:

```php
Builder::lazyChunk($count, callable $callback = null): LazyCollection;
```

and

```php
Builder::lazyChunkById($count, callable $callback = null, $column = null, $alias = null): LazyCollection;
```

The main difference is, that both methods are now returning lazy collections that resolve the chunks. The callback is now optional.
You can do this now:

```php
Article::with('author')->lazyChunk($chunkSize)->flatten(1)->map([$this, 'handleEachArticle']);
```

You can work with the articles there, as if you would have fetched those all in one query with the benefit of the eager loading.
But using the chunking, we are not loading all results at once and thus holding memory usage low.
In fact, the use case of flattening the chunks again to get the elements themselves, is seen as the main use case. Because of this,
the library offers the `flatLazyChunk` alias:

```php
Article::with('author')->flatLazyChunk($chunkSize)->map([$this, 'handleEachArticle']);
```