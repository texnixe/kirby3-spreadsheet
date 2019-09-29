<?php

namespace texnixe\Spreadsheet;

return [
  'attr' => [
      'header',
      'class',
      'sheet'
  ],
  'html' => function($tag) {

      $html   = '';
      $file   = $tag->file($tag->value());

      if (!$file) return $html;

      $options = [
        'class'  => $tag->class ?? option('texnixe.spreadsheet.tableClass', 'kirby-spreadsheet'),
        'header' => $tag->header === 'false' ? false : option('texnixe.spreadsheet.header', true),
        'sheet'  => $tag->sheet ?? false
      ];

      $html = \texnixe\Spreadsheet\SpreadsheetLoader::getData($file, $options);

      return $html;
  }
];
