<?php

namespace Kozz\Components\Collection;

use ArrayAccess;
use Closure;
use Countable;
use Iterator;
use IteratorAggregate;
use IteratorIterator;
use SplDoublyLinkedList;
use SplQueue;
use Traversable;
use ArrayIterator;
use Kozz\Interfaces\IArrayable;

class Collection implements ArrayAccess, IteratorAggregate, Countable, IArrayable
{

  /**
   * @var SplDoublyLinkedList | Iterator
   */
  protected $container;

  /**
   * @var SplQueue
   */
  protected $modifiers;

  /**
   * @param Traversable $iterator
   */
  public function __construct(Traversable $iterator = null)
  {
    $this->container = $iterator ? : new SplDoublyLinkedList();
    $this->modifiers = new SplQueue();
    $this->ensureArrayModifier();
  }

  /**
   * @param Traversable $from
   *
   * @return Collection
   */
  public static function from(Traversable $from)
  {
    $self = new self();
    foreach ($from as $item)
    {
      $self->push($item);
    }

    return $self;
  }

  public function iteratorToList()
  {
    $list = new SplDoublyLinkedList();
    foreach ($this->container as $item)
    {
      $list->push($item);
    }
    $this->container = $list;
    unset($list);
  }

  /**
   * @param $value
   */
  public function push($value)
  {
    $this->offsetSet(null, $value);
  }

  /**
   * @param null $offset
   * @param      $value
   */
  public function set($offset = null, $value)
  {
    $this->offsetSet($offset, $value);
  }

  /**
   * @param $offset
   *
   * @return mixed
   */
  public function get($offset)
  {
    return $this->offsetGet($offset);
  }

  /**
   * @param $offset
   *
   * @return void
   */
  public function remove($offset)
  {
    $this->offsetUnset($offset);
  }

  /**
   * @param $offset
   *
   * @return bool
   */
  public function exists($offset)
  {
    return $this->offsetExists($offset);
  }

  /**
   * @param callable $modifier
   */
  public function addModifier(Closure $modifier)
  {
    $this->modifiers->enqueue($modifier);
  }

  /**
   * @param mixed $offset
   *
   * @return bool
   */
  public function offsetExists($offset)
  {
    if (!$this->container instanceof ArrayAccess)
    {
      $this->iteratorToList();
    }

    return $this->container->offsetExists($offset);
  }

  /**
   * @param mixed $offset
   *
   * @return mixed
   */
  public function offsetGet($offset)
  {
    if (!$this->container instanceof ArrayAccess)
    {
      $this->iteratorToList();
    }

    return $this->container->offsetGet($offset);
  }

  /**
   * @param null  $offset
   * @param mixed $value
   */
  public function offsetSet($offset = null, $value)
  {
    if (!$this->container instanceof ArrayAccess)
    {
      $this->iteratorToList();
    }

    $this->container->offsetSet($offset, $value);
  }

  /**
   * @param mixed $offset
   */
  public function offsetUnset($offset)
  {
    if (!$this->container instanceof ArrayAccess)
    {
      $this->iteratorToList();
    }
    $this->container->offsetUnset($offset);
  }

  /**
   * @return int
   */
  public function count()
  {
    if (!$this->container instanceof Countable)
    {
      $this->iteratorToList();
    }

    return $this->container->count();
  }

  /**
   * @return array
   */
  public function toArray()
  {
    return array_values(iterator_to_array($this->getFilterIterator()));
  }

  /**
   * @return Iterator - An instance of an object implementing Iterator and Traversable
   */
  public function getIterator()
  {
    return $this->container;
  }

  /**
   * @return \CallbackFilterIterator|IteratorIterator
   */
  public function getFilterIterator()
  {
    $it = $this->getIterator();

    $modifiers = clone $this->modifiers;
    while ($modifiers->count())
    {
      $it = new \CallbackFilterIterator($it, $this->getFilterCallback($modifiers->dequeue()));
    }
    unset($modifiers);
    return $it;
  }

  /**
   * @param callable $closure
   *
   * @return callable
   */
  public function getFilterCallback(Closure $closure)
  {
    $filter = function(&$item) use ($closure){
      $closure($item);
      return true;
    };
    return $filter;
  }

  /**
   * @param void
   */
  private function ensureArrayModifier()
  {
    $this->addModifier(function(&$item){
      $item = ($item instanceof IArrayable) ? $item->toArray() : $item;
    });
  }

}
