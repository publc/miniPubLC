<?php

$app->map('/api/login', 'Auth\AuthAPI', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']);
