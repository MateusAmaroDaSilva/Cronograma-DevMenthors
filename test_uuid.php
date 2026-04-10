<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$person = \App\Models\Person::first();
echo json_encode([
    'id' => $person->id,
    'type' => gettype($person->id),
    'name' => $person->name
]);
