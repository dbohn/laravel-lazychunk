<?php


namespace Dababo\LazyChunk;


use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class LazyChunkServiceProvider extends ServiceProvider
{
    public function register()
    {
        Builder::mixin(new LazyChunkMixin());

        EloquentBuilder::mixin(new LazyChunkMixin());
    }
}