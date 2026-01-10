<?php

// Định nghĩa đường dẫn đến Laravel
$basePath = __DIR__;

// Chạy lệnh schedule của Laravel
exec("php {$basePath}/artisan schedule:run");

echo "Cron job executed at " . date('Y-m-d H:i:s') . PHP_EOL;
