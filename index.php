<?php

@include_once __DIR__ . '/vendor/autoload.php';

load([
    'texnixe\\Spreadsheet\\SpreadsheetLoader' => 'classes/SpreadsheetLoader.php',
    'texnixe\\Spreadsheet\\CacheHandler'      => 'classes/CacheHandler.php',
    'texnixe\\Spreadsheet\\HtmlGenerator'      => 'classes/HtmlGenerator.php',
    //'texnixe\\Spreadsheet\\PageGenerator'      => 'classes/PageGenerator.php',

], __DIR__);

Kirby::plugin('texnixe/spreadsheet', [
    'options' => [
        'cache' => true,
        'expires' => (60*24*7), // minutes
        'tagDefaults' => [
            'sheet'  => false,
            'header' => true,
            'class'  => 'kirby-spreadsheet',
        ],
        'pageGeneratorDefaults' => [
            'header'     => true,
            'parentPage' => 'home',
            'slugField'  => 'lastname',
            'template'   => 'article',
            'status'     => 'listed',
            'sheet'      => false,
        ],
    ],
    'snippets' => [
        'spreadsheet-table' => __DIR__ . '/snippets/spreadsheet-table.php',
    ],
    'tags' => [
        'spreadsheet' => require_once __DIR__ . '/tags/spreadsheet.php',
    ],
]);
