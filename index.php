<?php

use LukasJankowski\Storage\Factory;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Logger;

require 'vendor/autoload.php';

$config = [];


$config['log'] = new Logger('name');
$config['log']->pushHandler(new BrowserConsoleHandler());

$config['store'] = 'file';
$config['storeConfig'] = [
    'identifier' => 'custom',
    'path' => 'src/resources/store.json'
];

/*
$config['store'] = 'database';
$config['storeConfig'] = [
    'url' => 'mysql://root:@localhost/storage',
    'tableName' => 'storage',
    'identifier' => 'custom'
];
*/

$storage = Factory::createStorage($config);

$storage['myval'] = 'another';

$storage['myval'] = 'super';

isset($storage['notset']);


isset($storage['myval']);


echo $storage['notset'];
echo "<br>";
echo $storage['myval'];

unset($storage['myval']);


echo $storage;

$storage->persist();
