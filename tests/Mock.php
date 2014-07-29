<?php
/**
 * @author Ura Kozyrev <yk@multiship.ru>
 */

namespace Kozz\Tests;


use Kozz\Components\IArrayable;
use stdClass;

class Mock implements IArrayable
{
  public $id = 100;
  protected $data;

  public function __construct()
  {
    $this->setData((object)['key'=>true]);
  }

  public function setData(stdClass $data)
  {
    $this->data = $data;
  }

  /**
   * Get the instance as an array.
   *
   * @return array
   */
  public function toArray()
  {
    return $this->data;
  }
}