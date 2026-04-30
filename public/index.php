<?php
declare(strict_types=1);

$app = require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';

echo $app['app_name'] ?? 'Preguntero App';