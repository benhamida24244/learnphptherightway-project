<?php

declare(strict_types = 1);

use App\App;
use App\Config;
use App\Controllers\HomeController;
use App\Controllers\TransactionController;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

define('STORAGE_PATH', __DIR__ . '/../storage');
define('VIEW_PATH', __DIR__ . '/../views');

function normalizeRequestUri(string $requestUri): string
{
    $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
    $query = parse_url($requestUri, PHP_URL_QUERY);

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    if ($basePath && $basePath !== '/' && str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath)) ?: '/';
    }

    return $query ? $path . '?' . $query : $path;
}

$router = new Router();

$router
    ->get('/', [HomeController::class, 'index'])
    ->get('/about', [HomeController::class, 'about'])
    ->post('/add-transaction', [TransactionController::class, 'add'])
    ->get('/show-transactions', [TransactionController::class, 'show'])
    ->post('/upload',[TransactionController::class, 'uploadFile']);

(new App(
    $router,
    [
        'uri' => normalizeRequestUri($_SERVER['REQUEST_URI'] ?? '/'),
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET'
    ],
    new Config($_ENV)
))->run();
