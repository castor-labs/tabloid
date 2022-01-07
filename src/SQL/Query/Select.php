<?php

namespace Castor\Tabloid\SQL\Query;

use Castor\Tabloid\Collection;
use Castor\Tabloid\SQL\OperationError;
use Castor\Tabloid\SQL\Query;
use Castor\Tabloid\SQL\Query\Expr;
use Castor\Tabloid\SQL\Query\Expr\AndX;
use Castor\Tabloid\SQL\Query\Expr\OrX;
use Castor\Tabloid\SQL\Type\ConversionError;
use Castor\Tabloid\UnexpectedError;
use Generator;
use Iterator;
use IteratorAggregate;

class Select extends Query implements Collection, IteratorAggregate
{
    private ?Expr\Where $where = null;
    private array $params = [];
    private int $offset = 0;
    private ?int $limit = null;

    /**
     * @return Generator
     *
     * @throws OperationError
     * @throws UnexpectedError
     */
    public function getIterator(): Iterator
    {
        $sql = $this->getSql();
        $results = $this->connQuery($sql, $this->params);
        foreach ($results as $row) {
            yield $this->hydrate($row);
        }
    }

    /**
     * @param Clause ...$clauses
     * @return Expr\Where
     */
    public function where(Clause ...$clauses): Expr\Where
    {
        if ($this->where === null) {
            $this->where = new Expr\Where(...$clauses);
        }
        return $this->where;
    }

    /**
     * Counts the number of records with no regard for the offset and limit part.
     *
     * @return int
     *
     * @throws OperationError
     * @throws UnexpectedError
     */
    public function count(): int
    {
        $sql = $this->buildSql(true);
        $result = $this->connQuery($sql, $this->params);
        foreach ($result as $row) {
            return (int) ($row['count'] ?? '0');
        }

        return -1;
    }

    /**
     * @param int $offset
     * @param int|null $limit
     * @return $this
     */
    public function slice(int $offset = 0, int $limit = null): Select
    {
        $this->offset = $offset;
        $this->limit = $limit;
        return  $this;
    }

    public function orX(Clause ...$clauses): Clause
    {
        return new OrX(...$clauses);
    }

    public function andX(Clause ...$clauses): Clause
    {
        return new AndX(...$clauses);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function eq(string $field, mixed $value): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $this->params[] = $f->getType()->toDatabaseValue($value);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::eq($col);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function neq(string $field, mixed $value): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $this->params[] = $f->getType()->toDatabaseValue($value);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::neq($col);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function gt(string $field, mixed $value): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $this->params[] = $f->getType()->toDatabaseValue($value);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::gt($col);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function lt(string $field, mixed $value): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $this->params[] = $f->getType()->toDatabaseValue($value);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::lt($col);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function gte(string $field, mixed $value): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $this->params[] = $f->getType()->toDatabaseValue($value);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::gte($col);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function lte(string $field, mixed $value): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $this->params[] = $f->getType()->toDatabaseValue($value);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::lte($col);
    }

    /**
     * @param string $field
     * @param mixed $a
     * @param mixed $b
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function between(string $field, mixed $a, mixed $b): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $this->params[] = $f->getType()->toDatabaseValue($a);
        $this->params[] = $f->getType()->toDatabaseValue($b);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::between($col);
    }

    /**
     * @param string $field
     * @param array $values
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function in(string $field, array $values): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $type = $f->getType();
        $count = 0;
        foreach ($values as $value) {
            $this->params[] = $type->toDatabaseValue($value);
            $count++;
        }
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::in($col, $count);
    }

    /**
     * @param string $field
     * @param array $values
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function notIn(string $field, array $values): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $type = $f->getType();
        $count = 0;
        foreach ($values as $value) {
            $this->params[] = $type->toDatabaseValue($value);
            $count++;
        }
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::notIn($col, $count);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Clause
     * @throws ConversionError
     * @throws UnexpectedError
     */
    public function like(string $field, mixed $value): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $this->params[] = $f->getType()->toDatabaseValue($value);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::like($col);
    }

    /**
     * @param string $field
     * @return Clause
     * @throws UnexpectedError
     */
    public function isNull(string $field): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::isNull($col);
    }

    /**
     * @param string $field
     * @return Clause
     * @throws UnexpectedError
     */
    public function isNotNull(string $field): Clause
    {
        $f = $this->getMetadata()->getFieldForProperty($field);
        $col = $this->quote($f->getColumn()->getName());
        return Expr\RawClause::isNotNull($col);
    }

    /**
     * @param bool $count
     * @return string
     * @throws UnexpectedError
     */
    protected function buildSql(bool $count = false): string
    {
        $table = $this->quote($this->getMetadata()->getTable()->getName());

        $sql = "SELECT * FROM $table";

        if ($count) {
            $id = $this->getMetadata()->getIdFields()[0];
            $col = $this->quote($id->getColumn()->getName());
            $sql = str_replace('*', "COUNT($col) as count", $sql);
        }

        if ($this->where !== null) {
            $sql .= ' WHERE '.$this->where->toSql();
        }

        if (!$count && $this->limit !== null) {
            $sql .= " LIMIT $this->offset, $this->limit";
        }

        return $sql;
    }
}