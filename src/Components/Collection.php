<?php

namespace Kozz\Components;

use ArrayAccess;
use Closure;
use Countable;
use IteratorAggregate;
use IteratorIterator;
use SplDoublyLinkedList;
use SplQueue;
use Traversable;

class Collection implements ArrayAccess, IteratorAggregate, Countable, IArrayable
{

  /**
   * @var \SplDoublyLinkedList
   */
  protected $container;

  /**
   * @var \SplQueue
   */
  protected $modifiers;

  /**
   * @param void
   */
  public function __construct()
  {
    $this->container     = new SplDoublyLinkedList();
    $this->modifiers     = new SplQueue();
    $this->ensureArrayModifier();
  }

  /**
   * @param Traversable $from
   *
   * @return Collection
   */
  public static function from(\Traversable $from)
  {
    $self = new self();
    foreach ($from as $item)
    {
      $self->push($item);
    }

    return $self;
  }

  /**
   * @param mixed $offset
   *
   * @return bool
   */
  public function offsetExists($offset)
  {
    return $this->container->offsetExists($offset);
  }

  /**
   * @param mixed $offset
   *
   * @return mixed
   */
  public function offsetGet($offset)
  {
    return $this->container->offsetGet($offset);
  }

  /**
   * @param null  $offset
   * @param mixed $value
   */
  public function offsetSet($offset = null, $value)
  {
    if (null === $offset)
    {
      $this->container->push($value);

      return;
    }
    $this->container->offsetSet($offset, $value);
  }

  /**
   * @param mixed $offset
   */
  public function offsetUnset($offset)
  {
    $this->container->offsetUnset($offset);
  }

  /**
   * @param $value
   */
  public function push($value)
  {
    $this->container->push($value);
  }

  /**
   * @param $value
   *
   * @return mixed
   */
  public function get($value)
  {
    return $this->container[$value];
  }

  /**
   * @return int
   */
  public function count()
  {
    return $this->container->count();
  }

  /**
   * @return array
   */
  public function toArray()
  {
    return iterator_to_array($this->getFilterIterator());
  }

  public function addModifier(Closure $modifier)
  {
    $this->modifiers->enqueue($modifier);
  }

  /**
   * @return IteratorIterator - An instance of an object implementing Iterator and Traversable
   */
  public function getIterator()
  {
    return new IteratorIterator($this->container);
  }

  /**
   * @return \CallbackFilterIterator|IteratorIterator
   */
  public function getFilterIterator()
  {
    $it = $this->getIterator();

    while($this->modifiers->count())
    {
      $it = new \CallbackFilterIterator($it, $this->getFilterCallback($this->modifiers->dequeue()));
    }
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