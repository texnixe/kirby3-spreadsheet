<?php

declare(strict_types=1);

namespace texnixe\Spreadsheet;

final class HtmlGenerator
{
    /**
     * @param array $options
     * @param \Kirby\Cms\File $file
     */
    public static function getHtml(\Kirby\Cms\File $file, array $options = []): string
    {
        $options = array_merge(option('texnixe.spreadsheet.tagDefaults'), $options);

        $cache = option('texnixe.spreadsheet.cache');
        if ($cache === true && $response = CacheHandler::cache()->get(md5($file . json_encode($options)))) {
            return $response;
        }

        $reader    = SpreadsheetLoader::getReader($file, $options);
        if ($reader === false) {
            return 'The file "' . $file->filename() . '" is not valid';
        } else {
            $tableHead = SpreadsheetLoader::getTableHead($reader, $options['header']);

            $html      = snippet('spreadsheet-table', ['reader' => $reader, 'tableHead' => $tableHead, 'class' => $options['class']], true);

            if ($cache === true) {
                CacheHandler::cache()->set(
                    md5($file . json_encode($options)),
                    $html,
                    option('texnixe.spreadsheet.expires')
                );
            } else {
                CacheHandler::cache()->flush();
            }

            return $html;
        }
    }

}
