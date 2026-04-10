<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$people = \App\Models\Person::take(3)->get();
echo json_encode($people->toArray(), JSON_PRETTY_PRINT);
