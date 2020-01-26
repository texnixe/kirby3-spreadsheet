<?php

declare(strict_types=1);

namespace texnixe\Spreadsheet;

use SpreadsheetReader;

final class SpreadsheetLoader
{

    public static function getReader(\Kirby\Cms\File $file, array $options = [])
    {
        $reader = new SpreadsheetReader($file->root());
        if ($reader->valid()) {
            if ($options['sheet'] !== false) {
                $sheets = $reader->sheets();
                if ($index = array_search($options['sheet'], $sheets)){
                    $reader->changeSheet($index);
                }
            }
            return $reader;
        }
        return false;

    }

    public static function getTableHead(SpreadsheetReader $reader, bool $header = true)
    {
        if ($header === true) {
            return $reader->current();
        }

        return false;
    }
}
