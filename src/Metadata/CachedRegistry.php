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

class CachedRegistry implements Registry
{
    private Registry $factory;
    /**
     * @var array<string,Metadata>
     */
    private array $cache;

    public function __construct(Registry $factory)
    {
        $this->factory = $factory;
        $this->cache = [];
    }

    public function forClass(string $class): Metadata
    {
        if (array_key_exists($class, $this->cache)) {
            return $this->cache[$class];
        }

        $metadata = $this->factory->forClass($class);
        $this->cache[$class] = $metadata;

        return $metadata;
    }
}
