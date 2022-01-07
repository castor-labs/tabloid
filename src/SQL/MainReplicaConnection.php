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

namespace Castor\Tabloid\SQL;

use Castor\Tabloid\Transactional;
use Throwable;
use Traversable;

final class MainReplicaConnection implements Connection, Transactional
{
    private Connection $main;
    private Connection $replica;
    private bool $inTransaction;

    /**
     * @param Connection $main
     * @param Connection $replica
     */
    public function __construct(Connection $main, Connection $replica)
    {
        $this->main = $main;
        $this->replica = $replica;
        $this->inTransaction = true;
    }

    public function execute(string $query, array $params = []): int
    {
        // We always want to send state changing operations to the main connection
        return $this->main->execute($query, $params);
    }

    /**
     * @param string $query
     * @param array $params
     * @return iterable
     * @throws ConnectionError
     * @throws OperationError
     */
    public function query(string $query, array $params = []): iterable
    {
        // When in transaction, we always want to read from the main connection
        if (true === $this->inTransaction) {
            return $this->main->query($query, $params);
        }

        return $this->replica->query($query, $params);
    }

    /**
     * @return string
     * @throws ConnectionError
     * @throws OperationError
     */
    public function lastInsertedId(): string
    {
        return $this->main->lastInsertedId();
    }

    /**
     * {@inheritDoc}
     */
    public function wrapInTransaction(callable $operation): mixed
    {
        $wrapped = function () use ($operation): mixed {
            $this->inTransaction = true;

            try {
                $result = $operation();
                $this->inTransaction = false;

                return $result;
            } catch (Throwable $e) {
                $this->inTransaction = false;

                throw $e;
            }
        };

        return $this->doExecuteTransaction($wrapped);
    }

    /**
     * @template T
     *
     * @param callable():T $operation
     *
     * @return T
     */
    private function doExecuteTransaction(callable $operation): mixed
    {
        if ($this->main instanceof Transactional) {
            return $this->main->wrapInTransaction($operation);
        }

        return $operation();
    }
}
