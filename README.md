PHP Collection
==============

[![Build Status](https://travis-ci.org/urakozz/php-collection.svg?branch=master)](https://travis-ci.org/urakozz/php-collection)
[![Coverage Status](https://coveralls.io/repos/urakozz/php-collection/badge.png)](https://coveralls.io/r/urakozz/php-collection)
[![Latest Stable Version](https://poser.pugx.org/kozz/collection/v/stable.svg)](https://packagist.org/packages/kozz/collection)
[![Latest Unstable Version](https://poser.pugx.org/kozz/collection/v/unstable.svg)](https://packagist.org/packages/kozz/collection)
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

**Initializing from any Traversable or Iterator**

1. You can initiate collection as SplDoublyLinkedList-based structure with `Collection::from($traversable)`

    ```php
        $traversable = new \ArrayIterator(range(1,1000));
        $collection = Collection::from($traversable);
    ```

2. You also able use your `Iterator` as `Collection`'s data container with `new Collection($iterator)`.
Your iterator will converts to SplDoublyLinkedList once you try use any method from `ArrayAccess` or `Countable` interfaces implemented in `Collection`.
This is good solution if your iterator is cursor in big DB Data Set and you need just add some modifiers with `addModifier`

    ```php
        $mongo = new \MongoClient();
        $cursor = $mongo->selectDB('testDB')->selectCollection('testCollection')->find();
        $collection = new Collection($cursor);
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
    $element = $collection[0];
```

**Remove element**
```php
    $element = $collection->remove(0);
    //or
    $element = unset($collection[0]);
```

### Modifiers

Sometimes you should modify your data in collection

#### With Collection

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

Modifiers are quite helpful to process DB Data Sets:

```php
    $mongo = new \MongoClient();
    $cursor = $mongo->selectDB('testDB')->selectCollection('testCollection')->find();
    $collection = new Collection($cursor);
    $collection->addModifier(function(&$item){
        $item['id'] = (string)$item['_id'];
        unset($item['_id']);
    });
    foreach($collection->getFilterIterator() as $item)
    {
        //$item = ['id'=>'53df4992da722e2b550041af', 'value'=>1]
    }
```

#### Without Collection

You actually can modify your data in other ways:

Old-school approach looks like this:

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
        return true;
    });
    $it = new CallbackFilterIterator($it, function(&$item){
        if($item % 2 === 0){ $item += 1000; }
        return true;
    });
    foreach($array as $item)
    {
        //do stuff
    }
```

 
    
    
    
    
    
