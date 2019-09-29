<?php

namespace texnixe\Spreadsheet;

return [
    'attr' => [
        'header',
        'class',
        'sheet',
    ],
    'html' => static function($tag) {

        $file   = $tag->file($tag->value());

        if (! $file) {
            return '';
        }

        $options = [
            'class'  => $tag->class ?? option('texnixe.spreadsheet.tableClass', 'kirby-spreadsheet'),
            'header' => $tag->header === 'false' ? false : option('texnixe.spreadsheet.header', true),
            'sheet'  => $tag->sheet ?? false,
        ];

        return \texnixe\Spreadsheet\HtmlGenerator::getHtml($file, $options);
    },
];
