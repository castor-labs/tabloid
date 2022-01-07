<?php

namespace Castor\Tabloid\SQL\Query\Expr;

use Castor\Tabloid\SQL\Query\Clause;

class Where implements Clause
{
    /**
     * @var Clause[]
     */
    private array $clauses;

    /**
     * @param Clause ...$clauses
     */
    public function __construct(Clause ...$clauses)
    {
        $this->clauses = $clauses;
    }

    public function and(Clause $clause): Where
    {
        $this->clauses[] = new AndX($clause);
        return $this;
    }

    public function or(Clause $clause): Where
    {
        $this->clauses[] = new OrX($clause);
        return $this;
    }

    public function toSql(): string
    {
        if ($this->clauses === []) {
            return '';
        }
        if (count($this->clauses) === 1) {
            return $this->clauses[0]->toSql();
        }
        return implode(' ', array_map(static fn(Clause $clause) => $clause->toSql(), $this->clauses));
    }
}