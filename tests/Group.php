<?php

namespace Castor\Tabloid;

use Castor\Tabloid\Metadata\Attr\Column as Col;
use Castor\Tabloid\Metadata\Attr\Table;
use Castor\Tabloid\Engine\MySQL;
use DateTimeImmutable;

#[Table('groups')]
class Group
{
    #[Col(MySQL\TString::class, Col::PRIMARY_KEY)]
    private string $slug;
    #[Col(MySQL\TString::class)]
    private string $name;
    #[Col(MySQL\TDateTimeImmutable::class)]
    private DateTimeImmutable $createdAt;
}