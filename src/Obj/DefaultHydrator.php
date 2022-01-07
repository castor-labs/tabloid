<?php

namespace Castor\Tabloid\Obj;

use Castor\Tabloid\SQL\Type;
use Castor\Tabloid\Metadata;

/**
 * ClosureHydrator uses closure scope techniques to be able to read and write
 * from and to encapsulated objects.
 *
 * This PHP trick is explained better in the linked blog post:
 *
 * @link https://ocramius.github.io/blog/accessing-private-php-class-members-without-reflection/
 */
final class DefaultHydrator implements Hydrator
{
    private Inflector $inflector;

    /**
     * @param Inflector $inflector
     */
    public function __construct(Inflector $inflector)
    {
        $this->inflector = $inflector;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(Metadata $metadata, array $row): object
    {
        $className = $metadata->getClass()->getName();
        try {
            $object = $metadata->getClass()->newInstanceWithoutConstructor();
        } catch (\ReflectionException $e) {
            throw new HydratorError('Could not create a constructorless instance of '.$className, 0, $e);
        }

        foreach ($metadata->getFields() as $field) {
            $column = $field->getColumn()->getName();

            if (!array_key_exists($column, $row)) {
                throw new HydratorError(sprintf(
                    'No key named after column "%s" is present in the row result array',
                    $column
                ));
            }

            try {
                $this->inflector->set($object, $field->getName(), $field->getType()->toPhpValue($row[$column] ?? null));
            } catch (Type\ConversionError $e) {
                throw new HydratorError(sprintf(
                    'Could not convert value of column "%s" of table "%s" to PHP value',
                    $column,
                    $metadata->getTable()->getName()
                ), 0, $e);
            }
        }

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function dehydrate(Metadata $metadata, object $object, int $operation): array
    {
        $className = $metadata->getClass()->getName();

        $data = [];
        foreach ($metadata->getFields() as $field) {
            if ($operation === self::FOR_INSERT && $field->getColumn()->isAutogenerated()) {
                continue;
            }

            if ($operation === self::FOR_DELETE && !$field->getColumn()->isPrimaryKey()) {
                continue;
            }

            $property = $field->getName();

            try {
                $value = $field->getType()->toDatabaseValue($this->inflector->get($object, $property));
            } catch (Type\ConversionError $e) {
                throw new HydratorError(sprintf(
                    'Could not convert value of property "%s" of class "%s" to database value',
                    $property,
                    $className
                ), 0, $e);
            }

            $data[$field->getColumn()->getName()] = $value;
        }

        return $data;
    }
}