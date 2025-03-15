<?php

declare(strict_types=1);

namespace Doris;

use Doris\StreamLoad\Builder;
use Hyperf\Context\Context;

class Doris
{
    protected static ?StreamLoad $streamLoad = null;

    public static function streamLoad(): StreamLoad
    {
        if (class_exists(Context::class)) {
            return Context::getOrSet(self::class, function () {
                return static::makeStreamLoad();
            });
        }
        if (str_contains(PHP_SAPI, 'cgi')) {
            self::$streamLoad ??= static::makeStreamLoad();
            return static::$streamLoad;
        }

        return static::makeStreamLoad();
    }

    public static function table(string $table): Builder
    {
        return static::makeStreamLoad()->table($table);
    }

    private static function makeStreamLoad(): StreamLoad
    {
        $feHost = getenv('DORIS_FE_HOST') ?? 'http://127.0.0.1:8030';
        $db = getenv('DORIS_DB') ?? '';
        $user = getenv('DORIS_USER') ?? 'root';
        $password = getenv('DORIS_PASSWORD') ?? '';
        $constMemory = (bool) getenv('DORIS_CONST_MEMORY');
        return (new StreamLoad($feHost, $db, $user, $password))->constMemory($constMemory);
    }
}
