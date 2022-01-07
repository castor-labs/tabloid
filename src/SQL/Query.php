<?php

namespace Castor\Tabloid\SQL;

use Castor\Tabloid\Metadata;
use Castor\Tabloid\Obj;
use Castor\Tabloid\SQL\Query\Select;
use Castor\Tabloid\UnexpectedError;

abstract class Query
{
    private Connection $conn;
    protected Metadata\Registry $registry;
    private Obj\Hydrator $hydrator;
    protected Obj\Inflector $inflector;
    protected string $class;
    private string $sql;

    /**
     * @param Connection $conn
     * @param Metadata\Registry $registry
     * @param Obj\Hydrator $hydrator
     * @param Obj\Inflector $inflector
     * @param string $class
     */
    public function __construct(Connection $conn, Metadata\Registry $registry, Obj\Hydrator $hydrator, Obj\Inflector $inflector, string $class)
    {
        $this->conn = $conn;
        $this->registry = $registry;
        $this->hydrator = $hydrator;
        $this->inflector = $inflector;
        $this->class = $class;
        $this->sql = '';
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function quote(string $identifier): string
    {
        if ($this->conn instanceof Quoter) {
            return $this->conn->quote($identifier);
        }
        return '`'.$identifier.'`';
    }

    /**
     * @return string
     */
    protected function getSql(): string
    {
        if ($this->sql === '') {
            $this->sql = $this->buildSql();
        }

        return $this->sql;
    }

    /**
     * @param array $row
     * @return object
     * @throws UnexpectedError
     */
    protected function hydrate(array $row): object
    {
        try {
            return $this->hydrator->hydrate($this->getMetadata(), $row);
        } catch (Obj\HydratorError $e) {
            throw new UnexpectedError('Error hydrating object of class '.$this->class, 0, $e);
        }
    }

    /**
     * @param object $object
     * @param int $operation
     * @return array
     * @throws UnexpectedError
     */
    protected function dehydrate(object $object, int $operation): array
    {
        try {
            return $this->hydrator->dehydrate($this->getMetadata(), $object, $operation);
        } catch (Obj\HydratorError $e) {
            throw new UnexpectedError('Error dehydrating object of class '.$this->class, 0, $e);
        }
    }

    abstract protected function buildSql(): string;

    /**
     * @param string $sql
     * @param array $params
     * @return int
     * @throws OperationError
     * @throws UnexpectedError
     */
    protected function connExecute(string $sql, array $params = []): int
    {
        try {
            return $this->conn->execute($sql, $params);
        } catch (ConnectionError $e) {
            throw new UnexpectedError('Could not connect to database', 0, $e);
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return iterable
     * @throws OperationError
     * @throws UnexpectedError
     */
    protected function connQuery(string $sql, array $params = []): iterable
    {
        try {
            return $this->conn->query($sql, $params);
        } catch (ConnectionError $e) {
            throw new UnexpectedError('Could not connect to database', 0, $e);
        }
    }

    /**
     * @return string
     * @throws OperationError
     * @throws UnexpectedError
     */
    protected function connLastId(): string
    {
        try {
            return $this->conn->lastInsertedId();
        } catch (ConnectionError $e) {
            throw new UnexpectedError('Could not connect to database', 0, $e);
        }
    }

    /**
     * @return Metadata
     * @throws UnexpectedError
     */
    public function getMetadata(): Metadata
    {
        try {
            return $this->registry->forClass($this->class);
        } catch (Metadata\FactoryError $e) {
            throw new UnexpectedError('Could not load metadata for class '.$this->class, 0, $e);
        }
    }

    /**
     * @param string $class
     * @return Select
     */
    protected function select(string $class): Select
    {
        return new Select(
            $this->conn,
            $this->registry,
            $this->hydrator,
            $this->inflector,
            $class
        );
    }
}