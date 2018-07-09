<?php

$app->map('/api/product/{id}', 'ProductAPI', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']);
