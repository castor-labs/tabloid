<?php

namespace Castor\Tabloid\SQL\Query\Expr;

use Castor\Tabloid\SQL\Query\Clause;

abstract class XClause implements Clause
{
    protected array $parts;

    public function __construct(Clause ...$parts)
    {
        $this->parts = $parts;
    }

    /**
     * @param string $delimiter
     * @param bool $group
     * @return string
     */
    protected function joinSql(string $delimiter, bool $group = true): string
    {
        if ($this->parts === []) {
            return '';
        }
        if (count($this->parts) === 1) {
            return $delimiter.' '.$this->parts[0]->toSql();
        }

        $sql = implode(' '.$delimiter.' ', array_map(static fn(Clause $part) => $part->toSql(), $this->parts));
        if ($group === true) {
            $sql = '('.$sql.')';
        }
        return $sql;
    }
}