<?php

$api->group([
'prefix' => 'v1',
'namespace' => 'App\Http\Controllers\V1'
], function($app){
	//test
	$app->get('test', 'TestController@index');
});