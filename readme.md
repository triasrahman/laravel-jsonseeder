# Quick and efficient seeding database from JSON files for Laravel
[![Build Status](https://travis-ci.org/triasrahman/laravel-jsonseeder.svg?branch=master)](https://travis-ci.org/triasrahman/laravel-json-seeder)
[![Total Downloads](https://poser.pugx.org/triasrahman/json-seeder/d/total.svg)](https://packagist.org/packages/triasrahman/json-seeder)
[![Latest Stable Version](https://poser.pugx.org/triasrahman/json-seeder/v/stable.svg)](https://packagist.org/packages/triasrahman/json-seeder)
[![License](https://poser.pugx.org/triasrahman/json-seeder/license.svg)](https://packagist.org/packages/triasrahman/json-seeder)

> **This is a package to make your development workflow efficient by seeding the database from JSON files quickly.**

###[SEE DEMO](http://laravel-json-seeder.triasrahman.com)

## Installation

Require this package with composer:

```
composer require triasrahman/json-seeder
```

## Usage

On your database seeder, put `use JSONSeeder` trait.

```

use Triasrahman\JSONSeeder\JSONSeeder;

class YourTableSeeder extends Seeder
{
	use JSONSeeder;

	...

```

And then on `run()` method, just call `$this->JSONSeed($tableName, $className)`

```php
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// For simple JSON
        $this->JSONSeed('regions', '\App\Region');

		// For more complex JSON
        $this->JSONSeed('stats', '\App\Stat', [
            // has many
            'sources' => [
                'table' => 'sources',
                'class' => '\App\Source',
                'foreign_key' => 'stat_id',
                'flush' => true,
            ],
            // belongs to many
            'tags' => [
                'table' => 'tags',
                'class' => '\App\Tag',
                'flush' => true,
                'many_to_many' => true,
            ],
            // belongs to
            'author' => [
                'table' => 'users',
                'class' => '\App\User',
                'local_key' => 'user_id',
                'flush' => true,
            ],
            'region' => [
                'table' => 'regions',
                'class' => '\App\Region',
                'local_key' => 'region_id',
            ],
        ]);
    }
}

```

## Examples

Coming Soon

## License

Laravel JSON Seeder is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2015 [Trias Nur Rahman](http://triasrahman.com/)
