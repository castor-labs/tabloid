<?php

namespace Castor\Tabloid\SQL;

use ArrayIterator;
use Castor\Tabloid\Collection;
use IteratorAggregate;

final class ArrayCollection implements Collection, IteratorAggregate
{
    private array $elements;

    /**
     * @param Collection $collection
     * @return ArrayCollection
     */
    public static function make(Collection $collection): ArrayCollection
    {
        $array = iterator_to_array($collection);
        return new self($array);
    }

    /**
     * @param array $elements
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * @param int $offset
     * @param int|null $limit
     * @return ArrayCollection
     */
    public function slice(int $offset = 0, int $limit = null): ArrayCollection
    {
        return new self(array_slice($this->elements, $offset, $limit));
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, mixed $initial): mixed
    {
        return array_reduce($this->elements, $callback, $initial);
    }
}