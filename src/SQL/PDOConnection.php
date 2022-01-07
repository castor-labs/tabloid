<?php

namespace Castor\Tabloid\SQL;

use Castor\Tabloid\Transactional;
use PDO;
use PDOException;
use Throwable;

/**
 * This PDOConnection takes a PDO instance and sets some default to it before
 * querying.
 */
final class PDOConnection implements Connection, Transactional
{
    private PDO $pdo;

    public static function fromUri(string $uri): Connection
    {
        // TODO: Parse uri
        throw new \RuntimeException('Not Implemented');
    }

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->configurePDO();
    }

    /**
     * @inheritDoc
     */
    public function execute(string $query, array $params = []): int
    {
        $stmt = $this->pdo->prepare($query);
        try {
            $stmt->execute($params);
        } catch (PDOException $e) {
            throw new OperationError('Error while executing statement: '.$e->getMessage(), 0, $e);
        }

        return $stmt->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function query(string $query, array $params = []): \Traversable
    {
        $stmt = $this->pdo->prepare($query);
        try {
            $stmt->execute($params);
        } catch (PDOException $e) {
            throw new OperationError('Error while executing statement: '.$e->getMessage(), 0, $e);
        }

        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }

    /**
     * @param callable $operation
     * @return mixed
     * @throws Throwable
     */
    public function wrapInTransaction(callable $operation): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $result = $operation();
            $this->pdo->commit();
            return $result;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function lastInsertedId(): string
    {
        return $this->pdo->lastInsertId();
    }

    private function configurePDO(): void
    {
        // We want to disable MySQL buffering for memory performance.
        // Drawback of this is more tcp overhead + cpu
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, 0);

        // We throw exceptions, always.
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // We want to fetch results as an associative array (map)
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}