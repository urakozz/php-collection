<?php
/**
 * @author Ura Kozyrev <yk@multiship.ru>
 */

namespace Kozz\Tests;


use Kozz\Components\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
  protected $codeGuy;
  protected function _before() {}
  protected function _after()  {}

  public function testInit()
  {
    $collection = new Collection();
    $this->assertInstanceOf('Kozz\Components\Collection', $collection);
  }

  public function testCollection()
  {
    $data = new Mock();

    $collection = new Collection();
    $collection->push($data);
    $collection->push($data);

    $array = $collection->toArray();
    $this->assertInternalType('array', $array);
    $this->assertEquals(2, count($array));
  }

  public function testModifier()
  {
    $data = new Mock();

    $collection = new Collection();
    $collection->push($data);
    $collection->push($data);
    $collection->addModifier(function(&$item){
        $item['id'] = isset($item['id']) ? $item['id'] : true;
        $item['__id'] = $item['id'];
      });

    $array = $collection->toArray();
    $this->assertInternalType('array', $array);
    $this->assertEquals(2, count($array));
    $this->assertTrue(array_key_exists('__id', $array[0]));
  }

} 