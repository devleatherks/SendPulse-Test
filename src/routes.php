<?php

use Slim\Http\Request;
use Slim\Http\Response;

// use Controllers;

// Routes
$app->get('/', Controllers\ControllerHome::class . ':view_main');

$app->get('/parser', Controllers\ControllerHome::class . ':api_setPrseURL');
