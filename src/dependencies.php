<?php
/**
 * DIC configuration
 */

$container = $app->getContainer();

# View renderer
$container['view'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};


# Monolog
$container['logger'] = function ($c) {

    $settings = $c->get('settings')['logger'];

    $logger = new Monolog\Logger($settings['name']);

    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;

};

# Monolog
$container['mongoDB'] = function ($c) {

    return (new MongoDB\Client);

};

