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

use Castor\Tabloid\Metadata\Attr\Column;
use Castor\Tabloid\SQL\Type;
use ReflectionProperty;

class Field
{
    private ReflectionProperty $property;
    private Type $type;
    private Column $column;

    /**
     * @param ReflectionProperty $property
     * @param Column $column
     * @param Type $type
     */
    public function __construct(ReflectionProperty $property, Column $column, Type $type)
    {
        $this->property = $property;
        $this->column = $column;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->property->getName();
    }

    public function getColumn(): Column
    {
        return $this->column;
    }

    public function getType(): Type
    {
        return $this->type;
    }
}
