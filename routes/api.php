<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version(['v1', 'v2'], function ($api) {
	echo 999;die;
    require __DIR__ . DIRECTORY_SEPARATOR . 'v1.php';
});