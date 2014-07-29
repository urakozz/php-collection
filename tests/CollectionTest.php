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

    $collection[] = $data1;

    $this->assertEquals(2, $collection->count());
    $this->assertTrue($collection[1] === $data1);
    $this->assertTrue(isset($collection[1]));

    $collection[1] = $data;

    $this->assertEquals(2, $collection->count());
    $this->assertTrue($collection[1] === $data);
    $this->assertTrue(isset($collection[1]));

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

    $this->assertInstanceOf('IteratorIterator', $it);
    foreach($it as $item)
    {
      $this->assertInstanceOf('Kozz\Tests\Mock', $item);
      $this->assertTrue($item === $data);
    }

    $filteredIterator = $collection->getFilterIterator();

    $this->assertInstanceOf('IteratorIterator', $it);
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

} 