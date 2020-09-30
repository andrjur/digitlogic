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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function($router){
    //auth
    $router->post('users/login/','UsersController@login');
    $router->post('users/register/','UsersController@register');
    $router->post('users/update/','UsersController@update');

    //services
    $router->post('services/mindNumber/','ServicesController@serviceMindNumber');
    $router->post('services/lifeMatrix/','ServicesController@serviceLifeMatrix');
    $router->post('services/matrix12Life/','ServicesController@serviceMatrix12SpheresLife');
    $router->get('services/getAll/','ServicesController@getAll');

    //serviceHistory
    $router->post('servicesHistory/getAll/','ServicesHistoryController@getAll');

    //purchaseHistory
    $router->post('purchaseHistory/getAllPaid/','PurchaseHistoryController@getAllPaid');
    $router->post('purchaseHistory/create/','PurchaseHistoryController@create');
});
