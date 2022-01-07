<?php

namespace Castor\Tabloid\SQL\Type;

use LogicException;
use Castor\Tabloid\SQL\Type;

final class ArrayRegistry implements Registry
{
    /**
     * @var array<string,Type>
     */
    private array $types;

    public function __construct()
    {
        $this->types = [];
    }

    public function register(Type $type, string $name = ''): void
    {
        if ('' === $name) {
            $name = get_class($type);
        }
        if (array_key_exists($name, $this->types)) {
            throw new LogicException(sprintf('BaseType with name %s is already registered', $name));
        }

        $this->types[$name] = $type;
    }

    /**
     * @throws NotFound
     */
    public function get(string $type): Type
    {
        if (!array_key_exists($type, $this->types)) {
            throw new NotFound(sprintf('BaseType %s has not been found', $type));
        }

        return $this->types[$type];
    }
}