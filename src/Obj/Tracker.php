<?php

namespace Castor\Tabloid\Obj;

interface Tracker
{
    /**
     * @param object $object
     * @return bool
     */
    public function has(object $object): bool;

    /**
     * @param object $object
     */
    public function track(object $object): void;

    /**
     * @param object $object
     */
    public function del(object $object): void;
}