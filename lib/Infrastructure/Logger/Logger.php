<?php

/*  Poweradmin, a friendly web-based admin tool for PowerDNS.
 *  See <https://www.poweradmin.org> for more details.
 *
 *  Copyright 2007-2010 Rejo Zenger <rejo@zenger.nl>
 *  Copyright 2010-2024 Poweradmin Development Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Poweradmin\Infrastructure\Logger;

use DateTimeImmutable;
use Psr\Log\AbstractLogger;
use Stringable;

class Logger extends AbstractLogger
{
    private const DEFAULT_DATETIME_FORMAT = 'c';

    private LogHandlerInterface $handler;

    public function __construct(LogHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $timestamp = (new DateTimeImmutable())->format(self::DEFAULT_DATETIME_FORMAT);
        $this->handler->handle([
            'message' => self::interpolate((string)$message, $context),
            'level' => strtoupper($level),
            'timestamp' => ($timestamp),
        ]);
    }

    function interpolate(string $message, array $context = []): string
    {
        $replace = array();
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }
}