<?php

use Tester\Environment;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

$configurator = new Nette\Configurator;

$configurator->setTempDirectory(__DIR__ . '/temp');

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$testFileName = $_SERVER['argv'][0];
$configFileName = 'config.' . pathinfo($testFileName, PATHINFO_FILENAME) . '.neon';
if (file_exists($configFileName)) {
    $configurator->addConfig($configFileName);
}

$container = $configurator->createContainer();
return $container;