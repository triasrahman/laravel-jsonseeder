# Quick and efficient seeding database from JSON files for Laravel
[![Build Status](https://travis-ci.org/triasrahman/laravel-jsonseeder.svg?branch=master)](https://travis-ci.org/triasrahman/laravel-json-seeder)
[![Total Downloads](https://poser.pugx.org/triasrahman/json-seeder/d/total.svg)](https://packagist.org/packages/triasrahman/json-seeder)
[![Latest Stable Version](https://poser.pugx.org/triasrahman/json-seeder/v/stable.svg)](https://packagist.org/packages/triasrahman/json-seeder)
[![License](https://poser.pugx.org/triasrahman/json-seeder/license.svg)](https://packagist.org/packages/triasrahman/json-seeder)

> **This is a package to make your development workflow efficient by seeding the database from JSON files quickly.**

## Installation

Require this package with composer:

```
composer require triasrahman/json-seeder
```

## Usage

Prepare your JSON files for every table you want to seed using format `{table-name}.json`, for example:

*countries.json*

	[ 
		{name: 'Afghanistan', code: 'AF'}, 
		{name: 'Åland Islands', code: 'AX'}, 
		{name: 'Albania', code: 'AL'}, 
		{name: 'Algeria', code: 'DZ'}, 
		{name: 'American Samoa', code: 'AS'}, 
		{name: 'AndorrA', code: 'AD'}, 
		{name: 'Angola', code: 'AO'}, 
		{name: 'Anguilla', code: 'AI'}, 
		{name: 'Antarctica', code: 'AQ'}, 
		{name: 'Antigua and Barbuda', code: 'AG'}, 
		{name: 'Argentina', code: 'AR'}, 
		{name: 'Armenia', code: 'AM'}, 
		{name: 'Aruba', code: 'AW'}, 
		{name: 'Australia', code: 'AU'}, 
		{name: 'Austria', code: 'AT'}, 
		{name: 'Azerbaijan', code: 'AZ'}, 
		{name: 'Bahamas', code: 'BS'},
		...
	]

Save them into `storage/database`, for example:

	/storage
		/database
			- users.json
			- cities.json
			- countries.json
			- products.json
			- posts.json




On your Seeder class (located at `database/seeds/`), append `use Triasrahman\JSONSeeder\JSONSeeder` namespace and `use JSONSeeder` trait inside the class.


```php
<?php

use Illuminate\Database\Seeder;
use Triasrahman\JSONSeeder\JSONSeeder;

class YourTableSeeder extends Seeder
{
	use JSONSeeder;

	...

```

And then on `run()` method, just call `$this->JSONSeed($tableName, $className, [$realtions])`. For example:

*CountriesTableSeeder.php*
```php
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->JSONSeed('countries', '\App\Country');
    }
}

```
NOTE: You can run this on `DatabaseSeeder.php` too!

Just run on console `php artisan db:seed`, and viola!
See the [Official Laravel Documentation](http://laravel.com/docs/master/seeding) for more database seeding usage.

## Complex JSON Structure

We really understand that Laravel has powerful ORM named Eloquent. We want it make easier for you on this package. Yes, you can import your relational model here! It currently supports:
- One to One
- One to Many
- Many to Many
- Polymorphic (Coming Soon)

For example when we have a blog website, we wanna seed the posts table. You can easily set the JSON file like this.
*posts.json*

	[{
		"title": "Hello World!",
		"content": "This is my new blog.",
		"year": 2014,
		"categories": [
			{
				"name": "Hello",
				"description": "This is a category"
			},
			{
				"name": "World",
				"description": "This is a category"
			}
		],
		"author": {
			"name": "John Doe",
			"email": "johndoe@mail.com"
		},
		"pictures": [
			{
				"title": "Picture 1",
				"path": "uploads/picture-1.jpg"
			},
			{
				"title": "Picture 2",
				"path": "uploads/picture-2.jpg"
			}
		],
		"status": "published"
	},
	
	...
	
	]
	
After that you can run using following options.	

```php
$this->JSONSeed('posts', '\App\Post', [

	// has many
	'picture' => [
	'table' => 'pictures',          // Table name (optional). If blank, it will pluralize from the name
	'class' => '\App\Picture',      // Define the related class
	'foreign_key' => 'post_id',     // Define the foreign key in related class
	'flush' => true,                // If true, it will flush the related table (optional, default: false)
	],
	
	// many to many
	'categories' => 
	'table' => 'categories',        // Table name (optional). If blank, it will pluralize from the name
	'class' => '\App\Category',     // Define the related class
	'flush' => true,                // If true, it will flush the related table (optional, default: false)
	'many_to_many' => true,         // If this relation is many to many, set it true
	],
	
	// belongs to
	'author' => [
	'table' => 'users',             // Table name (optional). If blank, it will pluralize from the name
	'class' => '\App\User',         // Define the related class
	'local_key' => 'user_id',       // Define the local key in related class
	'flush' => true,                // If true, it will flush the related table (optional, default: false)
	],
]);
```

So easy right? Just try it now!

## License

Laravel JSON Seeder is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2015 [Trias Nur Rahman](http://triasrahman.com/)
