<h1 align="center">
    EasySearchable
    <br>
    <a href="https://packagist.org/packages/ctf0/easy-searchable"><img src="https://img.shields.io/packagist/v/ctf0/easy-searchable.svg" alt="Latest Stable Version"/></a>
    <a href="https://packagist.org/packages/ctf0/easy-searchable"><img src="https://img.shields.io/packagist/dt/ctf0/easy-searchable.svg" alt="Total Downloads"/></a>
</h1>

## Installation

``` bash
composer require ctf0/easy-searchable
```

# Setup

> also check https://github.com/laravie/query-filter#search-queries

- in ur model add

```php
use ctf0\EasySearchable\Traits\HasSearch;

class Post extends Model
{
    use HasSearch;

    // searched attributes
    //
    // when empty, all model fields will be searched
    // except "dates, primary_key, hidden"
    public $searchableAttributes = [];

    // or have a complete freedom and directly use
    public function getSearchableAttributes()
    {
        return [
            'name->' . app()->getLocale(),
            // ...
        ];
    }

    // ignore attributes
    //
    // so instead of listing all the needed columns in `$searchableAttributes`
    // you can ignore the ones you don't need here
    public $searchableAttributesIgnore = [];

    // searched relations
    //
    // when empty, no relation will be searched
    // * under each relation add the '$searchableAttributes' and we will pick them up automatically
    // * doesn't support nested relations
    public $searchableRelations  = [];

    // we search using the full sentence, 
    // however if you prefer to search by words, then use
    public $replaceSpace = true;
}
```

# Usage

```php
// auto search in model & relations attributes
Post::search('search for something')->get();

// to strict search even further, wrap the text with either `'` or `"`
Post::search('"search for something"')->get();

// search in specific fields
Post::search('search for something', ['columnName','relation.columnName'])->get();
```
