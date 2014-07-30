PHP Collection
==============

[![Build Status](https://travis-ci.org/urakozz/php-collection.svg?branch=master)](https://travis-ci.org/urakozz/php-collection)
[![Coverage Status](https://coveralls.io/repos/urakozz/php-collection/badge.png)](https://coveralls.io/r/urakozz/php-collection)
[![Packagist](http://img.shields.io/packagist/v/kozz/collection.svg)](https://packagist.org/packages/kozz/collection)
[![License](http://img.shields.io/packagist/l/kozz/collection.svg)](https://packagist.org/packages/kozz/collection)

Data Structure based on SplDoublyLinkedList with some advantages

Installation
------------

Add the package to your `composer.json` and run `composer update`.

    {
        "require": {
            "kozz/collection": "*"
        }
    }
    
    
Examples
--------

### Basic Usage

**Initializing**

    use Kozz\Component\Collection;
    $collection = new Collection();

**Initializing from any Traversable**

    $array = range(1,1000);
    $collection = Collection::from(new \ArrayIterator($array));
    
**Adding element**

    $element = 'string';
    $collection->push($element);
    //or
    $collection[] = $element;

**Replacing element**

    $element2 = new stdClass();
    $collection->set(0, $element2);
    //or
    $collection[0] = $element2;
    // This throws Exception (offset 100 not exists)
    $collection->set(100, $element2);
    
**Check offset**

    $collection->exists(0); 
    //or
    isset($collection[0]);
    
**Retrieve element**

    $element = $collection->get(0); 
    //or
    $element = $collection[0]);


    
    
    
    
    
