<?php

namespace Castor\Tabloid\SQL\Query;

interface Clause
{
    /**
     * @return string
     */
    public function toSql(): string;
}