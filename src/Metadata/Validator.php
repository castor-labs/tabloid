<?php

namespace Castor\Tabloid\Metadata;

use Castor\Tabloid\Metadata;

interface Validator
{
    /**
     * Validates the
     * @param Metadata ...$metas
     *
     * @throws ValidationError when validation could not be performed
     */
    public function validate(Metadata ...$metas): void;
}