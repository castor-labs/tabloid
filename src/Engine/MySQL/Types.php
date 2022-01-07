<?php

namespace Castor\Tabloid\Engine\MySQL;

use Castor\Tabloid\SQL\Type;

final class Types implements Type\Registry
{
    private const TYPES = [
        TBoolean::class,
        TDateTimeImmutable::class,
        TInteger::class,
        TString::class
    ];

    /**
     * @var array<string,BaseType>
     */
    private array $types = [];

    /**
     * @param string $type
     * @return BaseType
     * @throws Type\NotFound
     */
    public function get(string $type): BaseType
    {
        if (!in_array($type, self::TYPES, true)) {
            throw new Type\NotFound(sprintf(
                'BaseType %s does not exist in %s',
                $type,
                __CLASS__
            ));
        }

        $instance = $this->types[$type] ?? null;

        if ($instance === null) {
            $instance = new $type();
            $this->types[$type] = $instance;
        }

        return $instance;
    }

    /**
     * @param string $name
     * @param BaseType $type
     */
    public function add(string $name, BaseType $type): void
    {
        $this->types[$name] = $type;
    }
}