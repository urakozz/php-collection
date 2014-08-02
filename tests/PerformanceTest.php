<?php
/**
 * @author Ura Kozyrev <yk@multiship.ru>
 */
namespace Kozz\Tests;

ini_set('xdebug.remote_autostart', 0);
ini_set('xdebug.remote_enable', 0);
ini_set('xdebug.profiler_enable', 0);
if(function_exists('xdebug_disable')) { xdebug_disable(); }

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

    $this->assertTrue(abs($collectionTime - $arrayTime) < $collectionTime*0.3);

  }


} 
