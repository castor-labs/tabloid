<?php

namespace Castor\Tabloid\SQL\Query\Expr;

class AndX extends XClause
{
    public function toSql(): string
    {
        return $this->joinSql('AND');
    }
}