<?php

namespace Castor\Tabloid;

use Castor\Tabloid\Obj\Tracker;
use Generator;
use IteratorAggregate;

final class TrackedCollection implements Collection, IteratorAggregate
{
    private Collection $collection;
    private Tracker $tracker;

    /**
     * @param Tracker $tracker
     * @param Collection $collection
     */
    public function __construct(Tracker $tracker, Collection $collection)
    {
        $this->collection = $collection;
        $this->tracker = $tracker;
    }

    /**
     * @return Generator
     */
    public function getIterator(): Generator
    {
        foreach ($this->collection as $record) {
            $this->tracker->track($record);
            yield $record;
        }
    }

    /**
     * @param int $offset
     * @param int|null $limit
     * @return TrackedCollection
     */
    public function slice(int $offset = 0, int $limit = null): TrackedCollection
    {
        $this->collection->slice($offset, $limit);
        return $this;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->collection->count();
    }
}