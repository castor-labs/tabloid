<?php

namespace Castor\Tabloid\SQL\Query\Expr;

use Castor\Tabloid\SQL\Query\Clause;

final class RawClause implements Clause
{
    private string $sql;

    /**
     * @param string $sql
     */
    private function __construct(string $sql)
    {
        $this->sql = $sql;
    }

    public static function eq(string $column): RawClause
    {
        return new self("$column = ?");
    }

    public static function neq(string $column): RawClause
    {
        return new self("$column != ?");
    }

    public static function gt(string $column): RawClause
    {
        return new self("$column > ?");
    }

    public static function lt(string $column): RawClause
    {
        return new self("$column < ?");
    }

    public static function gte(string $column): RawClause
    {
        return new self("$column >= ?");
    }

    public static function lte(string $column): RawClause
    {
        return new self("$column <= ?");
    }

    public static function like(string $column): RawClause
    {
        return new self("$column LIKE ?");
    }

    public static function between(string $column): RawClause
    {
        return new self("$column BETWEEN ? AND ?");
    }

    public static function isNull(string $column): RawClause
    {
        return new self("$column IS NULL");
    }

    public static function isNotNull(string $column): RawClause
    {
        return new self("$column IS NOT NULL");
    }

    public static function in(string $column, int $count): RawClause
    {
        $placeholders = implode(', ', array_fill(0, $count, '?'));
        return new self("$column IN ($placeholders)");
    }

    public static function notIn(string $column, int $count): RawClause
    {
        $placeholders = implode(', ', array_fill(0, $count, '?'));
        return new self("$column NOT IN ($placeholders)");
    }

    public function toSql(): string
    {
        return $this->sql;
    }
}