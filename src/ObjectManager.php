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

use Closure;

/**
 * An ObjectManager manages the lifecycle of objects mapped from the database.
 */
interface ObjectManager
{
    /**
     * Persists an object in the Object Manager
     *
     * @param object $object
     *
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    public function persist(object $object): void;

    /**
     * Removes an object from the Object Manager
     *
     * @param object $object
     *
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    public function remove(object $object): void;

    /**
     * Finds an object of the determining class by its id
     *
     * @param string $className
     * @param array $id
     * @return object|null
     *
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    public function findOneById(string $className, array $id): ?object;

    /**
     * Finds a collection of objects according to a clause.
     *
     * If the clause is null, it finds them all.
     *
     * @param string $className
     * @param Closure|null $clause
     * @return Collection<object>
     *
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    public function findMany(string $className, Closure $clause = null): Collection;

    /**
     * Finds a collection of objects related to another an identity reference.
     *
     * It returns an array whose keys are the relevant ids for those records.
     *
     * Every element of the array is an iterable collection.
     *
     * @param Collection $collection
     * @param array $fieldMap
     * @return array<string,Collection<object>
     *
     * @throws UnexpectedError
     * @throws SQL\OperationError
     */
    public function findRelated(Collection $collection, array $fieldMap = []): iterable;
}
