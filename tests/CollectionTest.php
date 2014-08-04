<?php
/**
 * @author Ura Kozyrev <yk@multiship.ru>
 */

namespace Kozz\Tests;


use Kozz\Components\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{

  public function testInit()
  {
    $collection = new Collection();
    $this->assertInstanceOf('Kozz\Components\Collection', $collection);
  }

  public function testArrayAccessCountable()
  {
    $data = new Mock();
    $collection = new Collection();
    $collection->push($data);

    $this->assertEquals(1, $collection->count());
    $this->assertEquals(1, count($collection));
    $this->assertTrue($collection->get(0) === $data);
    $this->assertTrue($collection->offsetGet(0) === $data);
    $this->assertTrue($collection[0] === $data);
    $this->assertTrue(isset($collection[0]));
    $this->assertTrue($collection->offsetExists(0));
    $this->assertFalse($collection->offsetExists(1));

    $data1 = clone $data;
    $data1->setData(['key'=>100]);

    $collection->push($data1);

    $this->assertEquals(2, $collection->count());
    $this->assertTrue($collection[1] === $data1);
    $this->assertTrue(isset($collection[1]));

    unset($collection[1]);

    $this->assertEquals(1, $collection->count());
    $this->assertTrue($collection->get(0) === $data);
    $this->assertTrue($collection->offsetExists(0));
    $this->assertFalse($collection->offsetExists(1));

    /** @var $collection Collection */
    $collection[] = $data1;

    $this->assertEquals(2, $collection->count());
    $this->assertTrue($collection[1] === $data1);
    $this->assertTrue(isset($collection[1]));

    $collection[1] = $data;

    $this->assertEquals(2, $collection->count());
    $this->assertTrue($collection[1] === $data);
    $this->assertTrue(isset($collection[1]));

    $collection->remove(1);

    $this->assertFalse($collection->exists(1));

    $collection->push($data1);
    $this->assertTrue($collection->get(1) === $data1);

    $collection->set(1, $data);
    $this->assertTrue($collection->get(1) === $data);

  }

  public function testFrom()
  {
    $data = new Mock();
    $array = [];
    $array[] = $data;
    $array[] = $data;

    $collection = Collection::from(new \ArrayIterator($array));
    $this->assertEquals(2, $collection->count());
    $this->assertTrue($data === $collection[0]);
    $this->assertTrue($data === $collection[1]);
  }

  public function testIterator()
  {
    $data = new Mock();

    $collection = new Collection();
    $collection->push($data);
    $collection->push($data);

    $it = $collection->getIterator();

    $this->assertInstanceOf('Iterator', $it);
    foreach($it as $item)
    {
      $this->assertInstanceOf('Kozz\Tests\Mock', $item);
      $this->assertTrue($item === $data);
    }

    $filteredIterator = $collection->getFilterIterator();

    $this->assertInstanceOf('Iterator', $it);
    foreach($filteredIterator as $item)
    {
      $this->assertInternalType('array', $item);
      $this->assertTrue($item === $data->toArray());
    }
  }

  public function testToArray()
  {
    $data = new Mock();

    $collection = new Collection();
    $collection->push($data);
    $collection->push($data);

    $array = $collection->toArray();

    $this->assertInternalType('array', $array);
    $this->assertEquals(2, count($array));

    foreach($array as $item)
    {
      $this->assertInternalType('array', $item);
      $this->assertTrue(isset($item['key']));
      $this->assertFalse(isset($item['id']));
    }
  }

  public function testModifier()
  {
    $data = new Mock();

    $collection = new Collection();
    $collection->push($data);
    $collection->push($data);

    $collection->addModifier(function(&$item){
      $item['__id'] = $item['key'];
    });

    $array = $collection->toArray();
    $this->assertInternalType('array', $array);
    $this->assertEquals(2, count($array));
    $this->assertTrue(array_key_exists('__id', $array[0]));
  }

  public function testMongoCursor()
  {
    $mongo = new \MongoClient();
    $c = $mongo->selectDB('testDB')->selectCollection('testCollection');

    for($i = 0; $i<=20; $i++)
    {
      $c->insert(['value'=>$i]);
    }

    $cursor = $c->find();

    $collection = new Collection($cursor);
    $collection->addModifier(function(&$item){
      $item['id'] = (string)$item['_id'];
      unset($item['_id']);
    });

    foreach($collection as $item)
    {
      $this->assertTrue(isset($item['_id']));
      $this->assertNotEmpty($item['_id']);
      $this->assertFalse(isset($item['id']));
    }

    foreach($collection->getFilterIterator() as $item)
    {
      $this->assertTrue(isset($item['id']));
      $this->assertNotEmpty($item['id']);
      $this->assertFalse(isset($item['_id']));
    }

    foreach($collection->toArray() as $item)
    {
      $this->assertTrue(isset($item['id']));
      $this->assertNotEmpty($item['id']);
      $this->assertFalse(isset($item['_id']));
    }

    $c->drop();

  }

  public function testIteratorToList()
  {
    $mongo = new \MongoClient();
    $c = $mongo->selectDB('testDB')->selectCollection('testCollection');

    for($i = 0; $i<=10; $i++)
    {
      $c->insert(['value'=>$i]);
    }

    $collection = new Collection($c->find());
    $this->assertInstanceOf('MongoCursor', $collection->getIterator());

    $collection->iteratorToList();
    $this->assertInstanceOf('SplDoublyLinkedList', $collection->getIterator());

    $c->drop();
  }

  public function testIteratorConvert()
  {
    $mongo = new \MongoClient();
    $c = $mongo->selectDB('testDB')->selectCollection('testCollection');

    for($i = 0; $i<=10; $i++)
    {
      $c->insert(['value'=>$i]);
    }

    $cursor = $c->find();

    $collection = new Collection($cursor);
    $this->assertInstanceOf('MongoCursor', $collection->getIterator());

    $res = $collection->get(10);
    $this->assertInstanceOf('SplDoublyLinkedList', $collection->getIterator());
    $this->assertEquals(10, $res['value']);

    $collection = new Collection($cursor);
    $this->assertInstanceOf('MongoCursor', $collection->getIterator());

    $collection->set(0, 100);
    $this->assertInstanceOf('SplDoublyLinkedList', $collection->getIterator());
    $this->assertEquals(100, $collection->get(0));

    $collection = new Collection($cursor);
    $this->assertInstanceOf('MongoCursor', $collection->getIterator());

    $exists = $collection->exists(0);
    $this->assertInstanceOf('SplDoublyLinkedList', $collection->getIterator());
    $this->assertTrue($exists);

    $collection = new Collection($cursor);
    $elem1 = $collection->get(0);
    $collection = new Collection($cursor);
    $this->assertInstanceOf('MongoCursor', $collection->getIterator());
    $collection->remove(0);

    $this->assertInstanceOf('SplDoublyLinkedList', $collection->getIterator());
    $elem2 = $collection->get(1);

    $this->assertFalse($elem1 === $elem2);

    $collection = new Collection($cursor);
    $this->assertInstanceOf('MongoCursor', $collection->getIterator());
    $count = $collection->count();
    $this->assertInstanceOf('SplDoublyLinkedList', $collection->getIterator());
    $this->assertEquals(11, $count);

    $c->drop();
  }

} 