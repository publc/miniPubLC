<?php

$app->get('/', 'HomeController@index');
$app->post('/login', 'Auth\AuthController@login');
$app->post('/login', 'Auth\AuthController@login');
