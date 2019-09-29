<?php

@include_once __DIR__ . '/vendor/autoload.php';

load([
    'texnixe\\Spreadsheet\\SpreadsheetLoader' => 'classes/SpreadSheetLoader.php'
], __DIR__);

Kirby::plugin('texnixe/spreadsheet', [
    'options' => [
        'cache' => true,
        'expires' => (60*24*7), // minutes
    ],
    'snippets' => [
        'spreadsheet-table' => __DIR__ . '/snippets/spreadsheet-table.php'
    ],
    'tags' => [
        'spreadsheet' => require_once __DIR__ . '/tags/spreadsheet.php'
    ]
]);
