<?php
/**
 * @author Ura Kozyrev <yk@multiship.ru>
 */

namespace Kozz\Tests;


use Kozz\Components\IArrayable;

class Mock implements IArrayable
{
  public $id = 100;
  protected $data;

  public function __construct()
  {
    $this->setData(['key'=>true]);
  }

  public function setData($data)
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