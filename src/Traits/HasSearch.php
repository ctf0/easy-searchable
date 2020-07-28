<?php

namespace ctf0\EasySearchable\Traits;

use Illuminate\Support\Facades\Schema;

/**
 * under each model add any/both of.
 *
 * public $searchableAttributes = [];
 * public $searchableAttributesIgnore = [];
 * public $searchableRelations = [];
 */
trait HasSearch
{
    /**
     * self.
     */
    public function getSearchableAttributes()
    {
        return $this->searchableAttributes ?? $this->filterSelfSearchableAttributes();
    }

    /**
     * self ignore.
     */
    protected function getSearchableAttributesIgnore()
    {
        return $this->searchableAttributesIgnore ?? [];
    }

    /**
     * relations.
     */
    protected function getSearchableRelations()
    {
        return $this->searchableRelations ?? [];
    }

    /**
     * filter self attributes.
     */
    protected function filterSelfSearchableAttributes()
    {
        $attrs  = Schema::getColumnListing($this->getTable());
        $remove = array_merge(
            [$this->getKeyName()],
            $this->getDates(),
            $this->getHidden(),
            $this->getSearchableAttributesIgnore()
        );

        return array_diff($attrs, $remove);
    }

    /**
     * get relation attributes.
     */
    protected function getRelationsAttributes()
    {
        $relations = [];

        foreach ($this->getSearchableRelations() as $relation) {
            $relate = get_class($this->$relation()->getRelated());

            $resolve = array_map(function ($i) use ($relation) {
                return "$relation.$i";
            }, app($relate)->getSearchableAttributes());

            array_push($relations, ...$resolve);
        }

        return $relations;
    }

    /**
     * final.
     */
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
     * @param DB|Eloquent $query
     * @param string $searchTerm
     * @param string|array $customFields
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
