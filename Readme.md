# Laravel Table Sorter
This package easily add sorting functionality to any of your models along with helpers to create the sorting links.

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
  - [Sort Model Attributes](#sort-model-attributes)
  - [Sort Model Relationships](#sort-model-relationships)
  - [Sort Model Count Relationships](#sort-model-count-relationships)
  - [Sort Links Helper](#sort-links-helper)
- [Contributing](#contributing)
- [License](#license)

## Installation
Pull this package through Composer.
```sh
composer require rogercbe/table-sorter
```
After the instalation add the ServiceProvider to the providers array in your `config/app.php` file 
```php
Rogercbe\TableSorter\TableSorterServiceProvider::class,
```
Finally to publish the table header view use:
```sh
php artisan vendor:publish
```
This will create a `table-sorter` folder under your `resources/views/vendor` directory with the table header view.

## Usage
To start using this package you only have to use the `Sortable` trait on the model you wish to allow sorting. This Trait allows you to use the `sortable` which will listen to the GET request parameters and execute the queries needed to sort the records.
```php
use Rogercbe\TableSorter\Sortable;

class User extends Authenticatable
{
	...
	use Sortable;
	...
}
```
On your controller scope your query to listen for the sorting using `sortable` method
```php
use App\User;

class UsersController extends Controller
{
	public function index()
    {
    	...
        $users = User::sortable()->get()
        ...
    }
}
```
This method will respond to urls following the convention below:
`your-site.dev/?sort={COLUMN-TO-SORT}&direction={asc/desc}`
To build the url you must specify the column name that has to be sorted and the direction, the direction parameter is optional, by default the direction will be ascending.
```
your-site.dev/?sort={COLUMN}&direction={asc|desc}
```
### Sort Model Attributes
In order to sort by model attributes you have to specify the column name which should be sorted on the `sort` request variable.
```
your-site.dev/?sort=name&direction=asc
```
### Sort Model Relationships
In order to sort by model relationships the `sort` variable has to be set using this convention `relation.column_name`.
```
your-site.dev/?sort=company.name&direction=asc
```
There is no limit on nested relationships levels, it will perform the necessary join queries to be able to sort by the attribute selected.
### Sort Model Count Relationships
In order to sort by count relationships you must perform a `withCount('relation')` before calling `sortable` so Laravel can eager load the query the count relationship and attach it to the model. This way you will have avaiable the `relation_count` variable on the model and we can sort by it, specially useful in case that count has to be constrained.
```
your-site.dev/?sort=posts_count&direction=asc
```
```php
$users = User::withCount('posts')->sortable()->paginate();
```
### Sort Links Helper
If you wish to generate the pagination and table header links, this package allows to define the table headers and their options on your model and render them.
```php
use Rogercbe\TableSorter\Sortable;

class User extends Authenticatable
{
    use Sortable;
    ...
    protected $tableHeaders = [
        'name' => [
        	'title' => 'User Name'
        ],
        'email' => [
        	'class' => ['my-class', 'another-class']
        ],
        'created_at' => [
			'sortable' => 'false',
			'class' => 'one-class'
        ]
    ];
    ...
}
```
By default all headers are sortable, so you don't need to specify that in the headers configuration, only specify the ones that should be disabled. The title can be ommited aswell, by default it will capitalize the column name. If you wish to add certain classes to the header selector, you can pass a string or an array of strings containing the classes that should be added as the example shows.

You'll need to call `sortPaginate()` or `sortSimplePaginate()` methods instead of laravel's `paginate()` and `simplePaginate()` in order to use the helper functions to render the paginator and the table header, to render those links you only need to call the `sortLinks()` method in your view.

In your controller:
```php
...
	public function index()
    {
    	...
        $users = User::sortable()
            ->sortPaginate();
        ...
    }
...
```
Then in your view:
```blade
<table>
    <thead>
        {{ $users->sortLinks() }}
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->company->name }}</td>
                <td>{{ $user->name }}</td>
            </tr>
        @endforeach
    </tbody>
<table>
{{ $users->pagination() }}
...
```
The method `sortLinks()` is a helper that will render the table headers that you specified with basic functionality, creating the links to sort ascending or descending aswell as inserting arrows to show the direction. The default view can be published to edit it, or you can specify your own by creating a `sortLinksView` property on your model. It accepts a path to a view as a parameter aswell.

The method `pagination()` delegates to `links()` method from laravel paginator appending the get request values needed to sort. You are free to use either, and append the values yourself, it is just a helper.

## Contributing
You are more than welcome to contribute to the package by submitting a [Pull Request](https://github.com/rogercbe/LaravelTableSorter/pulls).

## License
The MIT License (MIT). Please see [License File](https://github.com/rogercbe/LaravelTableSorter/blob/master/License) for more information.
