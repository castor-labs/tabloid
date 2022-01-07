<?php

namespace Castor\Tabloid\Obj;

use WeakMap;

/**
 * The WeakTracker uses a WeakMap to track objects stored and their identity in
 * the database.
 */
final class WeakTracker implements Tracker
{
    /**
     * @var WeakMap<object,int>
     */
    private WeakMap $map;

    /**
     * @return WeakTracker
     */
    public static function create(): WeakTracker
    {
        return new self(new WeakMap());
    }

    public function __construct(WeakMap $map)
    {
        $this->map = $map;
    }

    public function has(object $object): bool
    {
        return $this->map->offsetExists($object);
    }

    public function track(object $object): void
    {
        $this->map->offsetSet($object, time());
    }

    public function del(object $object): void
    {
        $this->map->offsetUnset($object);
    }

    public function count(): int
    {
        return $this->map->count();
    }

    public function refresh(): void
    {
        $this->map = new WeakMap();
    }
}