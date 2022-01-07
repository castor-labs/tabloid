<?php

namespace Castor\Tabloid;

use Castor\Tabloid\SQL\ArrayCollection;
use Closure;

final class DefaultObjectManager implements ObjectManager
{
    private Obj\Tracker $tracker;
    private SQL\Query\Factory $query;

    /**
     * @param Obj\Tracker $tracker
     * @param SQL\Query\Factory $query
     */
    public function __construct(
        Obj\Tracker       $tracker,
        SQL\Query\Factory $query,
    )
    {
        $this->tracker = $tracker;
        $this->query = $query;
    }

    /**
     * @param object $object
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    public function persist(object $object): void
    {
        $class = get_class($object);

        if ($this->tracker->has($object)) {
            $this->query->update($class)->exec($object);
            return;
        }

        $this->query->insert($class)->exec($object);
        $this->tracker->track($object);
    }

    /**
     * @param object $object
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    public function remove(object $object): void
    {
        $identity = $this->tracker->has($object);
        if ($identity === null) {
            return;
        }

        $class = get_class($object);

        $this->query->delete($class)->exec($object);
        $this->tracker->del($object);
    }

    /**
     * @param string $className
     * @param array $id
     * @return object|null
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    public function findOneById(string $className, array $id): ?object
    {
        $object = $this->query->selectOne($className)->exec($id);
        if ($object !== null) {
            $this->tracker->track($object);
        }
        return $object;
    }

    /**
     * @param string $className
     * @param Closure|null $clause
     * @return Collection<object>
     */
    public function findMany(string $className, Closure $clause = null): Collection
    {
        $query = $this->query->select($className);
        if ($clause !== null) {
            $clause($query);
        }
        return new TrackedCollection($this->tracker, $query);
    }

    /**
     * @param Collection $collection
     * @param array $fieldMap
     * @return array
     */
    public function findRelated(Collection $collection, array $fieldMap = []): array
    {
        if (!$collection instanceof ArrayCollection) {
            $collection = ArrayCollection::make($collection);
        }

        $results = [];

        foreach ($fieldMap as $class => $field) {
            $select = $this->query->selectIn($class, $field, $collection);
            $results[$class] = $select;
        }

        return $results;
    }
}