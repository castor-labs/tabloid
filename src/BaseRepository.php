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

namespace Castor\Tabloid;

use Traversable;

/**
 * @template T
 */
abstract class BaseRepository
{
    private ObjectManager $manager;
    private string $className;

    /**
     * @param ObjectManager $manager
     * @param string $className
     */
    public function __construct(ObjectManager $manager, string $className)
    {
        $this->manager = $manager;
        $this->className = $className;
    }

    /**
     * @param object $object
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    protected function basePersist(object $object): void
    {
        $this->manager->persist($object);
    }

    /**
     * @param object $object
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    protected function baseRemove(object $object): void
    {
        $this->manager->remove($object);
    }
    
    public function baseFindOne(array $id): object
    {
        $this->manager->findById();
        throw new \RuntimeException('Not Implemented');
    }

    /**
     * @param int $offset
     * @param int|null $size
     * @return static<T>
     */
    protected function baseSlice(int $offset = 0, int $size = null): static
    {
        throw new \RuntimeException('Not Implemented');
    }

    /**
     * @return int
     */
    protected function baseCount(): int
    {
        throw new \RuntimeException('Not Implemented');
    }

    /**
     * @return Traversable<T>
     */
    protected function baseIterator(): Traversable
    {
        throw new \RuntimeException('Not Implemented');
    }
}
