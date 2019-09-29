# Kirby 3 Spreadsheet

Version 1.0.0

This plugin for Kirby 3 provides a KirbyTag that allows you to include a table inside a textarea field from a spreadsheet (Excel or CSV files).

## Installation

### Download

[Download the files](https://github.com/texnixe/kirby-spreadsheet/archive/master.zip) and place them inside `site/plugins/kirby-spreadsheet`.

### Kirby CLI
Installing via Kirby's [command line interface](https://github.com/getkirby/cli):

    $ kirby plugin:install texnixe/kirby-spreadsheet

To update the plugin, run:

    $ kirby plugin:update texnixe/kirby-spreadsheet

### Git Submodule
You can add the Kirby Spreadsheet plugin as a Git submodule.

    $ cd your/project/root
    $ git submodule add https://github.com/texnixe/kirby-spreadsheet.git site/plugins/kirby-spreadsheet
    $ git submodule update --init --recursive
    $ git commit -am "Add Kirby Spreadsheet"

Run these commands to update the plugin:

    $ cd your/project/root
    $ git submodule foreach git checkout master
    $ git submodule foreach git pull
    $ git commit -am "Update submodules"
    $ git submodule update --init --recursive

### How to use

Insert the tag into a textarea field.

**Example**:

```
(spreadsheet: pricelist.xlxs class: pricelist)
```

### Options:

#### class

The class you want to use for the table. If no class is given, the default class is 'kirby-spreadsheet'. You can set a different default class in your `config.php`:

```
'texnixe.spreadsheet.tableClass' => 'kirby-spreadsheet'
```

#### header

Set to `false` if the table does not contain a header, default is `true`

```
(spreadsheet: pricelist.xlxs header: false)
```

#### sheet

Optional, the name of the worksheet.

```
(spreadsheet: pricelist.xlsx sheet: Table1)
```


## License

Kirby Spreadsheet is open-sourced software licensed under the MIT license.

Copyright © date Sonja Broda info@texniq.de https://sonjabroda.com
