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

interface Registry
{
    /**
     * @param class-string $class
     * @return Metadata
     *
     * @throws FactoryError
     */
    public function forClass(string $class): Metadata;
}
