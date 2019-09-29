<?php

namespace texnixe\Spreadsheet;

use Kirby\Cms\File;
use SpreadsheetReader;

class SpreadsheetLoader
{
    private static $indexname = null;

    private static $cache = null;

    private static function cache(): \Kirby\Cache\Cache
    {
        if (!static::$cache) {
            static::$cache = kirby()->cache('texnixe.spreadsheet');
        }
        // create new index table on new version of plugin
        if (!static::$indexname) {
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

    public static function getData(\Kirby\Cms\File $file, Array $options = [])
    {
        $defaults = [
            'sheet'  => false,
            'header' => true,
            'class'  => 'kirby-spreadsheet'
        ];
        $options = array_merge($defaults, $options);

        if(option('texnixe.spreadsheet.cache') === true && $response = static::cache()->get(md5($file . json_encode($options)))) {
            $html = $response;
        }
        else {
            if(option('texnixe.spreadsheet.cache') === false) {
                static::cache()->flush();
            }
            $reader = new SpreadsheetReader($file->root());

            if ($options['sheet'] !== false) {
                $sheets = $reader->sheets();
                if ($index = array_search($options['sheet'], $sheets)){
                    $reader->changeSheet($index);
                }
            }

            $tableHead = false;
            if ($options['header'] === true) {
                $tableHead = $reader->current();
            }

            $html = snippet('spreadsheet-table', ['reader' => $reader, 'tableHead' => $tableHead, 'class' => $options['class']], true);

            static::cache()->set(
                md5($file . json_encode($options)),
                $html,
                option('texnixe.spreadsheet.expires')
            );
        }
        return $html;
    }
}
