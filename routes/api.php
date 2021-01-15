<?php

$router->mount('/server', function() use ($router) {

    $router->get('/(\d+)/autorole', 'Api\Controllers\AutoRole@getAutoRole');

    $router->post('/(\d+)/autorole', 'Api\Controllers\AutoRole@setAutoRole');

    $router->get('/(\d+)/banword', 'Api\Controllers\BanWord@getBanWord');

    $router->post('/(\d+)/banword', 'Api\Controllers\BanWord@setBanWord');

    $router->get('/(\d+)/selfrole', 'Api\Controllers\SelfRole@getSelfRole');

    $router->post('/(\d+)/selfrole', 'Api\Controllers\SelfRole@setSelfRole');
    
    $router->get('/(\d+)/streamers', 'Api\Controllers\Streamers@getStreamers');

    $router->post('/(\d+)/streamers', 'Api\Controllers\Streamers@setStreamers');

    $router->post('/(\d+)/streamers/del', 'Api\Controllers\Streamers@delStreamers');

    $router->get('/(\d+)/log', 'Api\Controllers\Log@getLog');

    $router->get('/(\d+)', 'Api\Controllers\Server@getServer');

    $router->post('/(\d+)', 'Api\Controllers\Server@setSetting');

    $router->get('/auth', 'Api\Controllers\OAuth@getOAuth');
});


