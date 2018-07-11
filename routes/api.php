<?php

$app->post('/api/login', 'Auth\AuthAPI@login');
$app->post('/api/register', 'Auth\AuthAPI@register');
$app->map('/api/user', 'Auth\AuthAPI', ['POST', 'PATCH', 'DELETE']);
$app->post('/api/logout', 'Auth\AuthAPI@logout');
$app->map('/api/user', 'Auth\AuthAPI', ['POST', 'PATCH', 'DELETE']);
