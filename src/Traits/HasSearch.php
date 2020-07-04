<?php

namespace ctf0\EasySearchable\Traits;

use Illuminate\Support\Facades\Schema;

/**
 * under each model add any/both of.
 *
 * public $searchableAttributes = [];
 * public $searchableRelations  = [];
 */
trait HasSearch
{
    /**
     * getSearchableRelations.
     */
    protected function getSearchableRelations()
    {
        return $this->searchableRelations ?? [];
    }

    /**
     * getSearchableAttributes.
     */
    public function getSearchableAttributes()
    {
        return $this->searchableAttributes ?? $this->filterSelfSearchableAttributes();
    }

    /**
     * filterSelfSearchableAttributes.
     */
    protected function filterSelfSearchableAttributes()
    {
        $attrs  = Schema::getColumnListing($this->getTable());
        $remove = array_merge([$this->getKeyName()], $this->getDates(), $this->getHidden());

        return array_diff($attrs, $remove);
    }

    /**
     * getRelationsAttributes.
     */
    protected function getRelationsAttributes()
    {
        $relations  = [];

        foreach ($this->getSearchableRelations() as $relation) {
            $relate  = get_class($this->$relation()->getRelated());

            $resolve = array_map(function ($i) use ($relation) {
                return "$relation.$i";
            }, app($relate)->getSearchableAttributes());

            array_push($relations, ...$resolve);
        }

        return $relations;
    }

    protected function getSearchableFields()
    {
        return array_merge(
            $this->getSearchableAttributes(),
            $this->getRelationsAttributes()
        );
    }

    /*
     * search() scope.
     *
     * @param [type] $query
     * @param [type] $searchTerm
     * @param [type] $customFields
     */
    public function scopeSearch($query, $searchTerm, $customFields = null)
    {
        return $query->queryFilter(
            $customFields ?: $this->getSearchableFields(),
            $searchTerm,
            false
        );
    }
}
