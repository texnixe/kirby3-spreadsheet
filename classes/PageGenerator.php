<?php

declare(strict_types=1);

namespace texnixe\Spreadsheet;

final class PageGenerator
{
/**
     * @param array $options
     * @param \Kirby\Cms\File $file
     */
    public static function pageGenerator(\Kirby\Cms\File $file, array $options = [])
    {
        $options   = array_merge(option('texnixe.spreadsheet.pageGeneratorDefaults'), $options);
        $reader    = SpreadsheetLoader::getReader($file, $options);

        if ($reader === false) {
            return '';
        } else {

            $tableHead = SpreadsheetLoader::getTableHead($reader, $options['header']);
            $parentPage = kirby()->page($options['parentPage']);

            if (! $parentPage) {
                throw new Exception('Error: The parent page does not exist.');
                return null;
            }
            while ($row = $reader->next()) {
                $data = array_combine($tableHead, (array) $row);
                $log = static::createChildren($parentPage, $options, $data);
            }
            if (count($log) === 0) {
                return true;
            }
            return;
        }
    }

    public static function createChildren($parentPage, $options, $data)
    {
        $log = [];
        try {
            $newChild = $parentPage->createChild([
                'slug' => $data['Lastname'],
                'template' => $options['template'],
                'content' => $data,
            ]);

            /* changeStatus error with default template */
            if ($options['template'] !== 'default') {
                $newChild->changeStatus($options['status']);
            }
        } catch(Exception $error) {
            $log[] = "Error: " . $error->getMessage();
        }
        return $log;
    }
}
