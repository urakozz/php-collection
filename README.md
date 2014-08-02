PHP Collection
==============

[![Build Status](https://travis-ci.org/urakozz/php-collection.svg?branch=master)](https://travis-ci.org/urakozz/php-collection)
[![Coverage Status](https://coveralls.io/repos/urakozz/php-collection/badge.png)](https://coveralls.io/r/urakozz/php-collection)
[![Packagist](http://img.shields.io/packagist/v/kozz/collection.svg)](https://packagist.org/packages/kozz/collection)
[![License](http://img.shields.io/packagist/l/kozz/collection.svg)](https://packagist.org/packages/kozz/collection)

Data Structure based on SplDoublyLinkedList with some advantages:

- Regular array compatiable (`ArrayAccess` interface implemented)
- Using Lamda Modifiers (see `addModifier` method)

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

```php
    use Kozz\Components\Collection;
    $collection = new Collection();
```

**Initializing from any Traversable**

```php
    $array = range(1,1000);
    $collection = Collection::from(new \ArrayIterator($array));
```
    
**Adding element**

```php
    $element = 'string';
    $collection->push($element);
    //or
    $collection[] = $element;
```

**Replacing element**

```php
    $element2 = new stdClass();
    $collection->set(0, $element2);
    //or
    $collection[0] = $element2;
    // This throws Exception (offset 100 not exists)
    $collection->set(100, $element2);
```
    
**Check offset**

```php
    $collection->exists(0); 
    //or
    isset($collection[0]);
```
    
**Retrieve element**

```php
    $element = $collection->get(0); 
    //or
    $element = $collection[0]);
```

### Modifiers

Sometimes you should modify your data in collection

Old-school way looks like this:

```php
    $array = range(1,100);
    foreach($array as &$item)
    {
        ++$item;
        unset($item);
    }
    foreach($array as &$item)
    {
        if($item % 2 === 0)
        {
            $item += 1000;
        }
        unset($item);
    }
    
    foreach($array as $item)
    {
        //do stuff
    }
```

Modern approach might looks something like this:

```php
    $array = range(1,100);
    $it = new ArrayIterator($array);
    $it = new CallbackFilterIterator($it, function(&$item){
        ++$item;
    });
    $it = new CallbackFilterIterator($it, function(&$item){
        if($item % 2 === 0){ $item += 1000; }
    });
    foreach($array as $item)
    {
        //do stuff
    }
```

And with this `Collection` you are able simply add modifier in just one line:

```php
    use Kozz\Components\Collection;
    
    $array = range(1,100);
    $collection = Collection::from(new ArrayIterator($array));
    $collection->addModifier(function(&item){
        ++$item;
    });
    $collection->addModifier(function(&item){
        if($item % 2 === 0){ $item += 1000; }
    });
```

So now Modifiers are stored in `Collection` and you have two ways to apply it:

1. use `getFilterIterator()` method to get an Iterator with all applied modifiers:

```php
    foreach($collection->getFilterIterator() as $item)
    {
        //do stuff
    }
```

2. Call `->toArray()` that calls `getFilterIterator()` :

```php
    $array = $collection->toArray();
    foreach($array as $item)
    {
        //do stuff
    }
```

 
    
    
    
    
    
