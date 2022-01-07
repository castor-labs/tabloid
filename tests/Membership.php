<?php

namespace Castor\Tabloid;

use Castor\Tabloid\Metadata\Attr\Column as Col;
use Castor\Tabloid\Metadata\Attr\Table;
use Castor\Tabloid\Engine\MySQL;
use DateTimeImmutable;

/**
 * Membership is what is known as a pivot entity.
 */
#[Table('memberships')]
class Membership
{
    #[Col(MySQL\TInteger::class, Col::PRIMARY_KEY)]
    private int $personId;
    #[Col(MySQL\TString::class, Col::PRIMARY_KEY)]
    private string $groupSlug;
    #[Col(MySQL\TDateTimeImmutable::class)]
    private DateTimeImmutable $createdAt;

    public function __construct(int $personId, string $groupSlug)
    {
        $this->personId = $personId;
        $this->groupSlug = $groupSlug;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getPersonId(): int
    {
        return $this->personId;
    }

    /**
     * @return string
     */
    public function getGroupSlug(): string
    {
        return $this->groupSlug;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}