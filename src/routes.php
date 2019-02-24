<?php

use Slim\Http\Request;
use Slim\Http\Response;

// use Controllers;

// Routes
$app->get('/', Controllers\ControllerHome::class . ':view_main');

$app->post('/parser', Controllers\ControllerHome::class . ':api_setPrseURL');

$app->get('/parser/cron', Controllers\ControllerCron::class . ':api_processQueue');
$app->get('/parser/cron/test', Controllers\ControllerCron::class . ':api_processQueue_test');
