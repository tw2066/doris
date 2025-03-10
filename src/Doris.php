<?php

declare(strict_types=1);

namespace Doris;

use Hyperf\Context\Context;

class Doris
{
    public static function streamLoad(): StreamLoad
    {
        if (class_exists(Context::class)) {
            return Context::override(self::class, function () {
                return static::makeStreamLoad();
            });
        }
        return static::makeStreamLoad();
    }

    private static function makeStreamLoad(): StreamLoad
    {
        $feHost = getenv('DORIS_FE_HOST') ?? 'http://127.0.0.1:8030';
        $db = getenv('DORIS_DB') ?? '';
        $user = getenv('DORIS_USER') ?? 'root';
        $password = getenv('DORIS_PASSWORD') ?? '';
        return new StreamLoad($feHost, $db, $user, $password);
    }
}
