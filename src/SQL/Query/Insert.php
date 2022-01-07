<?php

namespace Castor\Tabloid\SQL\Query;

use Castor\Tabloid\Obj\Hydrator;
use Castor\Tabloid\SQL\OperationError;
use Castor\Tabloid\SQL\Query;
use Castor\Tabloid\SQL\Type\ConversionError;
use Castor\Tabloid\UnexpectedError;

final class Insert extends Query
{
    /**
     * @param object $object
     * @throws UnexpectedError
     * @throws OperationError
     */
    public function exec(object $object): void
    {
        $sql = $this->getSql();
        $params = $this->dehydrate($object, Hydrator::FOR_INSERT);
        $rows = $this->connExecute($sql, $params);
        $this->refreshId($object);
    }

    /**
     * @return string
     * @throws UnexpectedError
     */
    protected function buildSql(): string
    {
        $columns = [];
        $values = [];
        foreach ($this->getMetadata()->getFields() as $field) {
            if ($field->getColumn()->isAutogenerated()) {
                continue;
            }
            $columns[] = $this->quote($field->getColumn()->getName());
            $values[] = ':'.$field->getColumn()->getName();
        }

        $columns = implode(', ', $columns);
        $values = implode(', ', $values);

        $table = $this->quote($this->getMetadata()->getTable()->getName());
        return "INSERT INTO $table ($columns) VALUES ($values)";
    }

    /**
     * @param object $object
     * @return void
     * @throws OperationError
     * @throws UnexpectedError
     */
    protected function refreshId(object $object): void
    {
        if (!$this->getMetadata()->hasAutogeneratedId()) {
            // Happy path. No autogenerated ids.
            return;
        }

        $id = $this->connLastId();

        $idFields = $this->getMetadata()->getIdFields();
        if (count($idFields) > 1) {
            // This case is not supported yet.
            throw new UnexpectedError('Composite keys with autogenerated fields are not yet supported');
        }

        $field = $idFields[0];

        try {
            $value = $field->getType()->toPhpValue($id);
        } catch (ConversionError $e) {
            throw new UnexpectedError(sprintf(
                'Error while casting column "%s" of table "%s" to PHP value',
                $field->getColumn()->getName(),
                $this->getMetadata()->getTable()->getName()
            ), 0, $e);
        }

        $this->inflector->set($object, $field->getName(), $value);
    }
}