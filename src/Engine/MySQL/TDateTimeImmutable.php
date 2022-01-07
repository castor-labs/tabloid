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

use DateTimeImmutable;
use Castor\Tabloid\SQL\Type;

/**
 * @see https://dev.mysql.com/doc/refman/5.7/en/datetime.html
 */
class TDateTimeImmutable extends BaseType
{
    private const FORMAT = 'Y-m-d H:i:s';
    private const TYPES = ['datetime', 'timestamp'];

    public function toDatabaseValue(mixed $php): string
    {
        if (!$php instanceof DateTimeImmutable) {
            throw new Type\ConversionError('Value sent to database should be an instance of DateTimeImmutable');
        }

        return $php->format(self::FORMAT);
    }

    public function toPhpValue(mixed $db): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat(self::FORMAT, $db);
        if (!$date instanceof DateTimeImmutable) {
            throw new Type\ConversionError('Value fetched from database should be date formatted as '.self::FORMAT);
        }

        return $date;
    }

    public function isValidDatabaseType(string $type): bool
    {
        return in_array($type, self::TYPES, true);
    }
}
