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

    // if empty then use all model fields except "dates, primary_key, hidden"
    public $searchableAttributes = [];

    // the relations you want to be searched
    // under each relation add the '$searchableAttributes' and we will pick them up automatically
    public $searchableRelations  = [];
}
```

# Usage

```php
// auto search in model & relations attributes
Post::search('search for something')->get();

// search in specific fields
Post::search('search for something', ['columnName','relation.columnName'])->get();
```
