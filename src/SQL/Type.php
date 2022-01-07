<?php

namespace Castor\Tabloid\SQL;

interface Type
{
    /**
     * @param mixed $php
     * @return mixed
     *
     * @throws Type\ConversionError
     */
    public function toDatabaseValue(mixed $php): mixed;

    /**
     * @param mixed $db
     * @return mixed
     *
     * @throws Type\ConversionError
     */
    public function toPhpValue(mixed $db): mixed;
}