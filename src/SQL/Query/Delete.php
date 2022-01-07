<?php

namespace Castor\Tabloid\SQL\Query;

use Castor\Tabloid\Obj\Hydrator;
use Castor\Tabloid\SQL\OperationError;
use Castor\Tabloid\SQL\Query;
use Castor\Tabloid\UnexpectedError;

final class Delete extends Query
{
    /**
     * @param object $object
     * @throws OperationError
     * @throws UnexpectedError
     */
    public function exec(object $object): void
    {
        $sql = $this->getSql();
        $params = $this->dehydrate($object, Hydrator::FOR_DELETE);
        $this->connExecute($sql, $params);
    }

    /**
     * @return string
     * @throws UnexpectedError
     */
    protected function buildSql(): string
    {
        $condition = [];
        foreach ($this->getMetadata()->getFields() as $field) {
            if (!$field->getColumn()->isPrimaryKey()) {
                continue;
            }

            $condition[] = $this->quote($field->getColumn()->getName()).' = :'.$field->getColumn()->getName();
        }

        $table = $this->quote($this->getMetadata()->getTable()->getName());
        $condition = implode(' AND ', $condition);

        return "DELETE FROM $table WHERE $condition";
    }
}