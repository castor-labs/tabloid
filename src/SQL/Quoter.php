<?php

namespace Castor\Tabloid\SQL;

interface Quoter
{
    /**
     * @param string $identifier
     * @return string
     */
    public function quote(string $identifier): string;
}