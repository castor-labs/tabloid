<?php

namespace Castor\Tabloid\Obj;

use Castor\Tabloid\Metadata;

interface Hydrator
{
    public const FOR_INSERT = 1;
    public const FOR_UPDATE = 2;
    public const FOR_DELETE = 3;

    /**
     * @param Metadata $metadata
     * @param array $row
     *
     * @throws HydratorError when hydration to an object could not be performed
     */
    public function hydrate(Metadata $metadata, array $row): object;

    /**
     * @param Metadata $metadata
     * @param object $object
     * @param int $operation
     * @return array
     *
     * @throws HydratorError when hydration to an object could not be performed
     */
    public function dehydrate(Metadata $metadata, object $object, int $operation): array;
}