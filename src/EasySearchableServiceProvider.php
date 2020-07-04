<?php

namespace ctf0\EasySearchable;

use Illuminate\Support\Arr;
use Laravie\QueryFilter\Searchable;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class EasySearchableServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     * https://github.com/laravie/query-filter
     * https://freek.dev/1182-searching-models-using-a-where-like-query-in-laravel.
     *
     * @return void
     */
    public function boot()
    {
        QueryBuilder::macro('queryFilter', function ($attributes, string $searchTerm, $replaceSpace = false) {
            $searchTerm = $replaceSpace ? str_replace(' ', '%', $searchTerm) : $searchTerm;

            return (new Searchable($searchTerm, Arr::wrap($attributes)))->apply($this);
        });

        EloquentBuilder::macro('queryFilter', function ($attributes, string $searchTerm, $replaceSpace = false) {
            $searchTerm = $replaceSpace ? str_replace(' ', '%', $searchTerm) : $searchTerm;

            return (new Searchable($searchTerm, Arr::wrap($attributes)))->apply($this);
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
    }
}
