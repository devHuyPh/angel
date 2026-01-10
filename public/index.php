<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->usePublicPath(__DIR__);

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);

// echo("Hello word");
// echo <<<HTML
// <!DOCTYPE html>
// <html lang="vi">
// <head>
//   <meta charset="UTF-8">
//   <title>44444</title>
//   <meta name="viewport" content="width=device-width, initial-scale=1.0">
//   <style>
//     body {
//       font-family: Arial, sans-serif;
//       background-color: #fff0f0;
//       color: #a80000;
//       text-align: center;
//       padding: 50px 20px;
//     }
//     .warning-box {
//       border: 5px solid #cc0000;
//       background-color: #fff;
//       padding: 40px 20px;
//       max-width: 800px;
//       margin: auto;
//       border-radius: 12px;
//       box-shadow: 0 0 20px rgba(200, 0, 0, 0.5);
//     }
//     h1 {
//       font-size: 40px;
//       color: #cc0000;
//       margin-bottom: 20px;
//       text-transform: uppercase;
//     }
//     p {
//       font-size: 20px;
//       color: #333;
//       margin: 15px 0;
//     }
//     .company-name {
//       font-size: 24px;
//       font-weight: bold;
//       color: #d10000;
//       margin-top: 20px;
//       text-transform: uppercase;
//     }
//     .note {
//       font-style: italic;
//       color: #555;
//       margin-top: 30px;
//       font-size: 16px;
//     }
//     .legal-warning {
//       color: red;
//       font-weight: bold;
//       margin-top: 20px;
//       font-size: 18px;
//     }
//   </style>
// </head>
// <body>
//   <div class="warning-box">
//     <h1>44444</h1>

//   </div>
// </body>
// </html>
// HTML;

// exit;
