<?php
/**
 * @author Ura Kozyrev <yk@multiship.ru>
 */

namespace Kozz\Components;


interface IArrayable {

  /**
   * Get the instance as an array.
   *
   * @return array
   */
  public function toArray();

}