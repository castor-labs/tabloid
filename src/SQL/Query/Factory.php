<?php

namespace Castor\Tabloid\SQL\Query;

use Castor\Tabloid\SQL\ArrayCollection;

interface Factory
{
    /**
     * @param string $class
     * @return Select
     */
    public function select(string $class): Select;

    /**
     * @param string $class
     * @return SelectOne
     */
    public function selectOne(string $class): SelectOne;

    /**
     * @param string $class
     * @param string $field
     * @param ArrayCollection $collection
     * @return Select
     */
    public function selectIn(string $class, string $field, ArrayCollection $collection): Select;

    /**
     * @param string $class
     * @return Insert
     */
    public function insert(string $class): Insert;

    /**
     * @param string $class
     * @return Update
     */
    public function update(string $class): Update;

    /**
     * @param string $class
     * @return Delete
     */
    public function delete(string $class): Delete;
}