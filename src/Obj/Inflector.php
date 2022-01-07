<?php

namespace Castor\Tabloid\Obj;

interface Inflector
{
    /**
     * @param object $object
     * @param string $prop
     * @param mixed $value
     */
    public function set(object $object, string $prop, mixed $value): void;

    /**
     * @param object $object
     * @param string $prop
     * @return mixed
     */
    public function get(object $object, string $prop): mixed;
}