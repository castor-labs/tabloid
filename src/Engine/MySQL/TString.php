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

class TString extends BaseType
{
    public const TYPES = ['varchar', 'text'];

    public function toDatabaseValue(mixed $php): string
    {
        if (!is_string($php)) {
            throw new Type\ConversionError('Value sent to database should be a string');
        }

        return $php;
    }

    public function toPhpValue(mixed $db): string
    {
        if (!is_string($db)) {
            throw new Type\ConversionError('Value fetched from database should be a string');
        }

        return $db;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isValidDatabaseType(string $type): bool
    {
        return in_array($type, self::TYPES, true);
    }
}
