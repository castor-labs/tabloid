<?php

namespace Castor\Tabloid\Engine\MySQL;

use SebastianBergmann\ObjectReflector\InvalidArgumentException;

class ColumnInfo
{
    private const TYPE_PATTERN = '/([a-z]*)(\(\d*\))?/';

    public const NULLABLE = 1;
    public const PRIMARY_KEY = 2;
    public const FOREIGN_KEY = 4;
    public const AUTOINCREMENT = 8;

    private string $name;
    private string $typeName;
    private int $typeLength;
    private int $flags;
    private array $row;

    /**
     * @param array $row
     * @return ColumnInfo
     */
    public static function parse(array $row): ColumnInfo
    {
        $name = $row['Field'] ?? null;
        if ($name === null) {
            throw new InvalidArgumentException('$row array must contain a "Field" key');
        }
        $type = $row['Type'] ?? null;
        if ($type === null) {
            throw new InvalidArgumentException('$row array must contain a "Type" key');
        }
        $matches = [];
        preg_match(self::TYPE_PATTERN, $type, $matches);
        $typeName = $matches[1] ?? null;
        $typeLength = (int) trim($matches[2] ?? '0', '()');

        $flags = 0;

        $null = $row['Null'] ?? 'NO';
        if ($null === 'YES') {
            $flags += self::NULLABLE;
        }

        $key = $row['Key'] ?? '';
        if ($key === 'PRI') {
            $flags += self::PRIMARY_KEY;
        }

        $extra = $row['Extra'] ?? '';
        if (str_contains($extra, 'auto_increment')) {
            $flags += self::AUTOINCREMENT;
        }

        return new self($name, $typeName, $typeLength, $flags, $row);
    }

    /**
     * @param string $name
     * @param string $typeName
     * @param int $typeLength
     * @param int $flags
     * @param array $row
     */
    public function __construct(string $name, string $typeName, int $typeLength, int $flags, array $row)
    {
        $this->name = $name;
        $this->typeName = $typeName;
        $this->typeLength = $typeLength;
        $this->flags = $flags;
        $this->row = $row;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * @return int
     */
    public function getTypeLength(): int
    {
        return $this->typeLength;
    }

    /**
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    public function isPrimaryKey(): bool
    {
        return $this->hasFlag(self::PRIMARY_KEY);
    }

    public function isAutoincrement(): bool
    {
        return $this->hasFlag(self::AUTOINCREMENT);
    }

    public function isForeignKey(): bool
    {
        return $this->hasFlag(self::FOREIGN_KEY);
    }

    public function isNullable(): bool
    {
        return $this->hasFlag(self::NULLABLE);
    }

    private function hasFlag(int $flag): bool
    {
        return ($this->flags & $flag) !== 0;
    }
}