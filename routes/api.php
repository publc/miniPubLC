<?php

$app->post('/api/login', 'Auth\AuthAPI@login');
$app->post('/api/register', 'Auth\AuthAPI@register');
$app->map('/api/user', 'Auth\AuthAPI', ['POST', 'PATCH', 'DELETE']);
