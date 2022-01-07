<?php

namespace Castor\Tabloid\SQL\Query;

use Castor\Tabloid\Obj\Hydrator;
use Castor\Tabloid\SQL\OperationError;
use Castor\Tabloid\SQL\Query;
use Castor\Tabloid\UnexpectedError;

final class Update extends Query
{
    /**
     * @param object $object
     * @throws UnexpectedError
     * @throws OperationError
     */
    public function exec(object $object): void
    {
        $sql = $this->getSql();
        $params = $this->dehydrate($object, Hydrator::FOR_UPDATE);
        $this->connExecute($sql, $params);
    }

    /**
     * @return string
     * @throws UnexpectedError
     */
    protected function buildSql(): string
    {
        $set = [];
        $condition = [];
        foreach ($this->getMetadata()->getFields() as $field) {
            $expr = $this->quote($field->getColumn()->getName()).' = :'.$field->getColumn()->getName();
            if (!$field->getColumn()->isPrimaryKey()) {
                $set[] = $expr;
                continue;
            }
            $condition[] = $expr;
        }

        $table = $this->quote($this->getMetadata()->getTable()->getName());
        $set = implode(', ', $set);
        $condition = implode(' AND ', $condition);

        return "UPDATE $table SET $set WHERE $condition";
    }
}