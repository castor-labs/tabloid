<?php

namespace Castor\Tabloid\Obj;

use Closure;
use RuntimeException;

class ClosureInflector implements Inflector
{
    /**
     * @var array<string,Closure>
     */
    private array $setters;
    /**
     * @var array<string,Closure>
     */
    private array $getters;

    public function __construct()
    {
        $this->setters = [];
        $this->getters = [];
    }

    public function set(object $object, string $prop, mixed $value): void
    {
        $set = $this->getSetterFor(get_class($object));
        $set($object, $prop, $value);
    }

    public function get(object $object, string $prop): mixed
    {
        $get = $this->getGetterFor(get_class($object));
        return $get($object, $prop);
    }

    /**
     * @param string $class
     * @return Closure(object,string):mixed
     */
    private function getGetterFor(string $class): Closure
    {
        if (!array_key_exists($class, $this->getters)) {
            $getter = Closure::bind(static function (object $object, string $name): mixed {
                return $object->{$name};
            }, null, $class);

            if (!$getter instanceof Closure) {
                throw new RuntimeException(sprintf(
                    'Could not bind get Closure to scope %s',
                    $class,
                ));
            }

            $this->getters[$class] = $getter;
        }

        return $this->getters[$class];
    }

    /**
     * @param string $class
     * @return Closure(object,string,mixed):void
     */
    private function getSetterFor(string $class): Closure
    {
        if (!array_key_exists($class, $this->setters)) {
            $setter = Closure::bind(static function (object $object, string $name, mixed $value): void {
                $object->{$name} = $value;
            }, null, $class);

            if (!$setter instanceof Closure) {
                throw new RuntimeException(sprintf(
                    'Could not bind set Closure to scope %s',
                    $class,
                ));
            }

            $this->setters[$class] = $setter;
        }
        return $this->setters[$class];
    }
}