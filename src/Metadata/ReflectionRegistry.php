<?php

declare(strict_types=1);

/**
 * @project Ekklesion
 * @link https://github.com/castor-labs/ekklesion
 * @package castor/ekklesion
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 CastorLabs Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Tabloid\Metadata;

use Castor\Tabloid\Metadata;
use Castor\Tabloid\Metadata\Attr;
use Castor\Tabloid\SQL\Type;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionType;

class ReflectionRegistry implements Registry
{
    private Type\Registry $types;

    public function __construct(Type\Registry $types)
    {
        $this->types = $types;
    }

    public function forClass(string $class): Metadata
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (\ReflectionException $e) {
            throw new FactoryError('Reflection failed', 0, $e);
        }

        $tableAttr = $reflection->getAttributes(Attr\Table::class)[0] ?? null;
        if (!$tableAttr instanceof ReflectionAttribute) {
            throw new FactoryError(sprintf('Attribute %s is required in class %s', Attr\Table::class, $class));
        }

        /** @var Attr\Table $table */
        $table = $tableAttr->newInstance();

        $metadata = new Metadata($reflection, $table);

        foreach ($reflection->getProperties() as $property) {
            $columnAttr = $property->getAttributes(Attr\Column::class)[0] ?? null;
            if (!$columnAttr instanceof ReflectionAttribute) {
                if (!$property->hasDefaultValue()) {
                    throw new FactoryError(sprintf(
                        'Property "%s" of class "%s" needs to have a default value if is not going to be mapped to the database',
                        $property->getName(),
                        $class
                    ));
                }

                continue;
            }
            /** @var Attr\Column $column */
            $column = $columnAttr->newInstance();

            if ('' === $column->getName()) {
                $column->setName(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property->getName())));
            }

            try {
                $type = $this->types->get($column->getType());
            } catch (Type\NotFound $e) {
                throw new FactoryError('Error while loading type '.$column->getType(), 0, $e);
            }

            if ($column->isNullable()) {
                $type = new Type\Nullable($type);
            }

            $propType = $property->getType();
            if ($propType instanceof ReflectionType && $propType->allowsNull() && !$column->isNullable()) {
                throw new FactoryError(sprintf(
                    'Property "%s" of class "%s" is nullable but the column is not marked as such',
                    $property->getName(),
                    $class
                ));
            }

            $metadata->addField(new Field(
                $property,
                $column,
                $type,
            ));
        }

        return $metadata;
    }
}
