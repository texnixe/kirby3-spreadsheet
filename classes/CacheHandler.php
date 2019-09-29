<?php

declare(strict_types=1);

namespace texnixe\Spreadsheet;

final class CacheHandler
{
    private static $indexname = null;

    private static $cache = null;

    public static function cache(): \Kirby\Cache\Cache
    {
        if (! static::$cache) {
            static::$cache = kirby()->cache('texnixe.spreadsheet');
        }
        // create new index table on new version of plugin
        if (! static::$indexname) {
            static::$indexname = 'index'.str_replace('.', '', kirby()->plugin('texnixe/spreadsheet')->version()[0]);
        }
        return static::$cache;
    }

    public static function flush()
    {
        if (static::$cache) {
            return static::cache()->flush();
        }
    }
}
