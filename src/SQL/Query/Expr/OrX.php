<?php

namespace Castor\Tabloid\SQL\Query\Expr;

class OrX extends XClause
{
    public function toSql(): string
    {
        return $this->joinSql('OR');
    }
}