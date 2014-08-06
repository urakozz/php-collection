<?php
/**
 * @author Ura Kozyrev <yk@multiship.ru>
 */

namespace Kozz\Interfaces;


interface IArrayable {

  /**
   * Get the instance as an array.
   *
   * @return array
   */
  public function toArray();

}