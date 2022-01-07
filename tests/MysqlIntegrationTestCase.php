<?php

namespace Castor\Tabloid;

use Castor\Tabloid\Engine\MySQL;
use Castor\Tabloid\Metadata;
use Castor\Tabloid\Obj;
use Castor\Tabloid\SQL\PDOConnection;
use Castor\Tabloid\SQL\Query\DefaultFactory;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;

class MysqlIntegrationTestCase extends TestCase
{
    private static ?MySQL\Driver $driver = null;
    private static ?Obj\WeakTracker $tracker = null;
    private static ?ObjectManager $manager = null;

    protected function getDriver(): MySQl\Driver
    {
        if (self::$driver === null) {
            try {
                $pdo = new PDO('mysql:host=mysql;dbname=test', 'user', 'pass');
                self::$driver = new MySQL\Driver(
                    new PDOConnection($pdo),
                    new MySQL\Types()
                );
            } catch (Exception $e) {
                $this->markTestSkipped('Error while connecting to the database: '.$e->getMessage());
            }
        }

        return self::$driver;
    }

    /**
     * @param string ...$tables
     * @throws SQL\ConnectionError
     * @throws SQL\OperationError
     */
    protected function truncate(string ...$tables): void
    {
        $conn = $this->getDriver();
        $conn->execute('SET foreign_key_checks = 0');
        foreach ($tables as $table) {
            $conn->execute("TRUNCATE TABLE `$table`");
        }
        $conn->execute('SET foreign_key_checks = 1');
    }

    protected function seed(): void
    {
        $sql = <<<EOT
        INSERT INTO `people` (name, age, enabled, created_at) VALUES 
        ('John Doe', 22, 1, '2019-03-22 18:00:00'),
        ('Anna Doe', 23, 0, '2019-03-23 18:00:00'),
        ('John Smith', 45, 1, '2019-04-12 15:32:21'),
        ('John Calvin', 54, 0, '2019-04-14 15:32:21');

        INSERT INTO `groups` (slug, name, created_at) VALUES 
        ('living', 'Living People', '2019-03-22 18:00:00'),
        ('dead', 'Dead People', '2019-03-22 18:00:00'),
        ('theologians', 'Theologians', '2019-03-22 19:00:00');

        INSERT INTO memberships (person_id, group_slug, created_at) VALUES 
        (1, 'living', '2020-11-23 18:00:00'),
        (2, 'living', '2020-11-23 18:00:00'),
        (3, 'dead', '2020-11-23 18:00:00'),
        (4, 'theologians', '2020-11-23 18:00:00'),
        (4, 'dead', '2020-11-23 18:00:00')
        EOT;

        $this->getDriver()->execute($sql);
    }

    protected function getObjectTracker(): Obj\WeakTracker
    {
        if (self::$tracker === null) {
            self::$tracker = Obj\WeakTracker::create();
        }
        return self::$tracker;
    }

    protected function getObjectManager(): ObjectManager
    {
        if (self::$manager === null) {
            $inflector = new Obj\ClosureInflector();
            self::$manager = new DefaultObjectManager(
                $this->getObjectTracker(),
                new DefaultFactory(
                    $this->getDriver(),
                    new Metadata\ReflectionRegistry(
                        $this->getDriver()
                    ),
                    new Obj\DefaultHydrator(
                        $inflector
                    ),
                    $inflector
                )
            );
        }

        return self::$manager;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array[]
     * @throws SQL\ConnectionError
     * @throws SQL\OperationError
     */
    public function queryArray(string $sql, array $params = []): array
    {
        $rows = $this->getDriver()->query('SELECT * FROM people');
        if (!is_array($rows)) {
            $rows = iterator_to_array($rows);
        }
        return $rows;
    }
}