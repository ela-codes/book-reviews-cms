<?php
require "../backend/config/database.php";
require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create a logger instance
$logger = new Logger('registration');
$logger->pushHandler(new StreamHandler('./logs/login.log', Logger::DEBUG));
$logger->info('Login page loaded');


?>