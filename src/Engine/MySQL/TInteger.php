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

namespace Castor\Tabloid\Engine\MySQL;

use Castor\Tabloid\SQL\Type;

class TInteger extends BaseType
{
    public const TYPES = ['int', 'integer', 'number'];

    public function toDatabaseValue(mixed $php): int
    {
        if (!is_int($php)) {
            throw new Type\ConversionError('Value sent to database should be an integer');
        }

        return $php;
    }

    public function toPhpValue(mixed $db): int
    {
        // We cast it only if is a numeric string
        if (is_string($db) && is_numeric($db)) {
            $db = (int) $db;
        }

        if (!is_int($db)) {
            throw new Type\ConversionError('Value fetched from database should be an integer');
        }

        return $db;
    }

    public function isValidDatabaseType(string $type): bool
    {
        return in_array($type, self::TYPES, true);
    }
}
