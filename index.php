<?php


use Freezemage\Config\FileConfigFactory;


require __DIR__ . '/vendor/autoload.php';


$factory = new FileConfigFactory();
$config = $factory->create('config.ini');
var_dump($config->get('variables.section.a'));