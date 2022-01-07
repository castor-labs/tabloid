<?php

namespace Castor\Tabloid\SQL\Query;

use Castor\Tabloid\SQL\OperationError;
use Castor\Tabloid\SQL\Query;
use Castor\Tabloid\SQL\Type\ConversionError;
use Castor\Tabloid\UnexpectedError;

class SelectOne extends Query
{
    /**
     * @param array $id
     * @return object|null
     * @throws UnexpectedError
     * @throws OperationError
     */
    public function exec(array $id): ?object
    {
        $sql = $this->getSql();
        $params = [];
        foreach ($this->getMetadata()->getIdFields() as $field) {
            if (!array_key_exists($field->getName(), $id)) {
                throw new UnexpectedError(sprintf(
                    'Required id field "%s" for entity "%s" is not present',
                    $field->getName(),
                    $this->getMetadata()->getClass()
                ));
            }

            try {
                $value = $field->getType()->toDatabaseValue($id[$field->getName()]);
            } catch (ConversionError $e) {
                throw new UnexpectedError('Error converting PHP value to database value', 0, $e);
            }
            $params[$field->getColumn()->getName()] = $value;
        }

        $rows = $this->connQuery($sql, $params);
        foreach ($rows as $row) {
            return $this->hydrate($row);
        }

        return null;
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

        return "SELECT * FROM $table WHERE $condition";
    }
}