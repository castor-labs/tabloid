<?php

namespace Castor\Tabloid;

use Castor\Tabloid\SQL\Query\Select;

/**
 * @coversDefaultClass
 */
class ObjectManagerTest extends MysqlIntegrationTestCase
{
    public function setUp(): void
    {
        $this->truncate('people', 'groups', 'memberships');
        $this->seed();
    }

    public function testItFetchesPersonById(): void
    {
        $manager = $this->getObjectManager();
        /** @var Person $person */
        $person = $manager->findOneById(Person::class, ['id' => 4]);
        $this->assertInstanceOf(Person::class, $person);
        $this->assertSame('John Calvin', $person->getName());
    }

    public function testItFetchesOnePersonAndMutates(): void
    {
        $manager = $this->getObjectManager();
        /** @var Person $person */
        $person = $manager->findOneById(Person::class, ['id' => 4]);

        $person->incrementAge();
        $manager->persist($person);

        $rows = $this->queryArray('SELECT * FROM people');
        $this->assertCount(4, $rows);
        $this->assertSame('55', $rows[3]['age']);
        $this->assertSame(55, $person->getAge());
    }

    public function testItSavesOnePerson(): void
    {
        $manager = $this->getObjectManager();
        $person = new Person('John Knox', 33);

        $manager->persist($person);

        // The autogenerated primary key should be populated on persistence
        $this->assertSame(5, $person->getId());

        $rows = $this->queryArray('SELECT * FROM people');
        // We should only have one record
        $this->assertCount(5, $rows);
    }

    public function testItSavesSamePersonImmediatelyMutated(): void
    {
        $manager = $this->getObjectManager();

        $person = new Person('John Knox', 33);

        $manager->persist($person);
        $rows = $this->queryArray('SELECT * FROM people');
        $this->assertCount(5, $rows);
        $this->assertSame('33', $rows[4]['age']);
        $this->assertSame(33, $person->getAge());

        $person->incrementAge();
        $manager->persist($person);

        // If we immediately persist a mutated object, we should have
        // the same state between the database and the object
        $rows = $this->queryArray('SELECT * FROM people');
        $this->assertCount(5, $rows);
        $this->assertSame('34', $rows[4]['age']);
        $this->assertSame(34, $person->getAge());
    }

    public function testItQueriesAllEntities(): void
    {
        $manager = $this->getObjectManager();
        $result = $manager->findMany(Person::class);
        $result->slice(0, 2);
        $people = iterator_to_array($result);

        $this->assertCount(4, $result);
        $this->assertCount(2, $people);
    }

    public function testItQueriesSomeEntities(): void
    {
        $manager = $this->getObjectManager();

        $result = $manager->findMany(Person::class, static function (Select $q) {
            $q->where($q->andX(
                $q->gt('age', 45),
                $q->like('name', 'J%')
            ));
        });

        /** @var Person[] $people */
        $people = iterator_to_array($result);

        $this->assertInstanceOf(TrackedCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame(4, $people[0]->getId());
        $this->assertSame('John Calvin', $people[0]->getName());
    }

    public function testItQueriesRelatedEntities(): void
    {
        $this->markTestIncomplete('This test is not finished');
        $manager = $this->getObjectManager();
        $memberships = $manager->findMany(Membership::class);
        $related = $manager->findRelated($memberships, [
            Person::class => 'personId',
            Group::class => 'groupSlug',
        ]);
    }
}