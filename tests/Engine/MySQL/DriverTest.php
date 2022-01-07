<?php

namespace Castor\Tabloid\Engine\MySQL;

use Castor\Tabloid\Group;
use Castor\Tabloid\Membership;
use Castor\Tabloid\Metadata\ReflectionRegistry;
use Castor\Tabloid\MysqlIntegrationTestCase;
use Castor\Tabloid\Person;

class DriverTest extends MysqlIntegrationTestCase
{
    public function testItValidatesMetadataCorrectly(): void
    {
        $driver = $this->getDriver();
        $registry = new ReflectionRegistry($driver);

        $this->expectNotToPerformAssertions();

        $driver->validate(
            $registry->forClass(Person::class),
            $registry->forClass(Group::class),
            $registry->forClass(Membership::class)
        );
    }
}
