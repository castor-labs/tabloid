<?php

namespace Castor\Tabloid\SQL\Query;

use Castor\Tabloid\Collection;
use Castor\Tabloid\Metadata;
use Castor\Tabloid\Obj;
use Castor\Tabloid\SQL\ArrayCollection;
use Castor\Tabloid\SQL\Connection;
use Castor\Tabloid\SQL\Query;
use Castor\Tabloid\SQL\Type\ConversionError;
use Castor\Tabloid\UnexpectedError;
use RuntimeException;

final class DefaultFactory implements Factory
{
    private Connection $conn;
    private Metadata\Registry $registry;
    private Obj\Hydrator $hydrator;
    private Obj\Inflector $inflector;

    private const INSERT = 'insert';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const SELECT_ONE = 'select_one';

    /**
     * @var array<string,<array<string,Query>>
     */
    private array $cache;

    /**
     * @param Connection $conn
     * @param Metadata\Registry $registry
     * @param Obj\Hydrator $hydrator
     * @param Obj\Inflector $inflector
     */
    public function __construct(Connection $conn, Metadata\Registry $registry, Obj\Hydrator $hydrator, Obj\Inflector $inflector)
    {
        $this->conn = $conn;
        $this->registry = $registry;
        $this->hydrator = $hydrator;
        $this->inflector = $inflector;
        $this->cache = [];
    }

    /**
     * @param string $class
     * @return Select
     */
    public function select(string $class): Select
    {
        return new Select(
            $this->conn,
            $this->registry,
            $this->hydrator,
            $this->inflector,
            $class
        );
    }

    /**
     * @param string $class
     * @return SelectOne
     */
    public function selectOne(string $class): SelectOne
    {
        $query = $this->get(self::SELECT_ONE, $class);
        if (!$query instanceof SelectOne) {
            $query = new SelectOne(
                $this->conn,
                $this->registry,
                $this->hydrator,
                $this->inflector,
                $class
            );
            $this->set(self::SELECT_ONE, $class, $query);
        }
        return $query;
    }

    /**
     * @param string $class
     * @param string $field
     * @param ArrayCollection $collection
     * @return Select
     * @throws UnexpectedError
     */
    public function selectIn(string $class, string $field, ArrayCollection $collection): Select
    {
        $ids = $collection->reduce(function (array $carry, mixed $object) use ($field) {
            $value = $this->inflector->get($object, $field);
            if ($value === null) {
                throw new UnexpectedError(sprintf('No property named %s in object of class %s', $field, get_class($object)));
            }
            $carry[] = $value;
            return $carry;
        }, []);

        $q = $this->select($class);
        $idFields = $q->getMetadata()->getIdFields();
        if (count($idFields) !== 1) {
            throw new UnexpectedError(sprintf('Target class %s of sub selection cannot have a composite identifier', $class));
        }
        $idField = $idFields[0];
        throw new RuntimeException('Not Implemented');
    }

    /**
     * @param string $class
     * @return Insert
     */
    public function insert(string $class): Insert
    {
        $query = $this->get(self::INSERT, $class);
        if (!$query instanceof SelectOne) {
            $query = new Insert(
                $this->conn,
                $this->registry,
                $this->hydrator,
                $this->inflector,
                $class
            );
            $this->set(self::INSERT, $class, $query);
        }
        return $query;
    }

    /**
     * @param string $class
     * @return Update
     */
    public function update(string $class): Update
    {
        $query = $this->get(self::UPDATE, $class);
        if (!$query instanceof SelectOne) {
            $query = new Update(
                $this->conn,
                $this->registry,
                $this->hydrator,
                $this->inflector,
                $class
            );
            $this->set(self::UPDATE, $class, $query);
        }
        return $query;
    }

    /**
     * @param string $class
     * @return Delete
     */
    public function delete(string $class): Delete
    {
        $query = $this->get(self::DELETE, $class);
        if (!$query instanceof SelectOne) {
            $query = new Delete(
                $this->conn,
                $this->registry,
                $this->hydrator,
                $this->inflector,
                $class
            );
            $this->set(self::DELETE, $class, $query);
        }
        return $query;
    }

    protected function get(string $operation, string $class): ?Query
    {
        return $this->cache[$operation][$class] ?? null;
    }

    protected function set(string $operation, string $class, Query $query): void
    {
        $this->cache[$operation][$class] = $query;
    }
}