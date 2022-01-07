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

interface Connection
{
    /**
     * Executes a state changing operation in an SQL database.
     *
     * It returns the number of rows affected by the operation.
     *
     * @throws ConnectionError when connection could not be established
     * @throws OperationError  when the operation fails for some reason
     */
    public function execute(string $query, array $params = []): int;

    /**
     * Executes a read-only operation in an SQL database.
     *
     * It returns an iterable with the results of the read operation.
     *
     * @param string $query
     * @param array $params
     * @return iterable
     *
     * @throws ConnectionError when connection could not be established
     * @throws OperationError when the operation fails for some reason
     */
    public function query(string $query, array $params = []): iterable;

    /**
     * Returns the id of the last inserted record in the database
     *
     * @return string
     * @throws ConnectionError when connection could not be established
     * @throws OperationError  when the operation fails for some reason
     */
    public function lastInsertedId(): string;
}
