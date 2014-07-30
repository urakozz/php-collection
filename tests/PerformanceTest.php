<?php
/**
 * @author Ura Kozyrev <yk@multiship.ru>
 */

namespace Kozz\Tests;


use Kozz\Components\Collection;
use Symfony\Component\Stopwatch\Stopwatch;

class PerformanceTest extends \PHPUnit_Framework_TestCase
{

  public function testIterating()
  {
    $stopwatch = new Stopwatch();

    $array = range(1, 200000);
    $collection = Collection::from(new \ArrayIterator($array));

    $stopwatch->start('array');

    foreach($array as $item) {}

    $arrayEvent  = $stopwatch->stop('array');
    $arrayTime   = $arrayEvent->getDuration();

    $stopwatch->start('collection');

    foreach($collection as $item) {}

    $collectionEvent  = $stopwatch->stop('collection');
    $collectionTime   = $collectionEvent->getDuration();

    $this->assertTrue(abs($collectionTime - $arrayTime) < $collectionTime*0.2);

  }


} 