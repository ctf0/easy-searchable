<?php

namespace ctf0\EasySearchable\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

/**
 * under each model add any/both of.
 *
 * public $replaceSpace = true;
 * public $searchableDates = true;
 * public $searchableAttributes = [];
 * public $searchableAttributesIgnore = [];
 * public $searchableRelations = [];
 */
trait HasSearch
{
    protected $noWildCard = false;

    protected function getSearchableDates()
    {
        return $this->searchableDates ?? false;
    }

    /**
     * self.
     */
    public function getSearchableAttributes()
    {
        return $this->searchableAttributes ?? $this->filterSelfSearchableAttributes();
    }

    /**
     * ignore.
     */
    public function getSearchableAttributesIgnore()
    {
        return $this->searchableAttributesIgnore ?? [];
    }

    /**
     * relations.
     */
    public function getSearchableRelations()
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
            $this->getSearchableDates() ? [] : $this->getDates(),
            $this->getHidden(),
            $this->getSearchableAttributesIgnore()
        );

        return array_diff($attrs, array_filter($remove));
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

    /* -------------------------------------------------------------------------- */

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
            $this->resolveSearchTerm($searchTerm),
            $this->noWildCard
        );
    }

    /* -------------------------------------------------------------------------- */
    /*                                  FUZZINESS                                 */
    /* -------------------------------------------------------------------------- */

    protected function resolveSearchTerm($searchTerm)
    {
        $searchTerm = $this->searchStrict($searchTerm);
        $searchTerm = $this->replaceSpaces($searchTerm);

        return $searchTerm;
    }

    protected function searchStrict($searchTerm)
    {
        if (Str::startsWith($searchTerm, ['"', '\''])) {
            $searchTerm = preg_replace('/^[\'"]|[\'"]$/', '', $searchTerm);

            $this->noWildCard = true;
        }

        return $searchTerm;
    }

    protected function replaceSpaces($searchTerm)
    {
        $replace = $this->replaceSpace ?? false;

        return $replace
                    ? str_replace(' ', '%', $searchTerm)
                    : $searchTerm;
    }
}
