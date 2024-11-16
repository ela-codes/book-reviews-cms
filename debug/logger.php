<?php 
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function getLogger($name, $filePath) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // Create a logger instance
    $logger = new Logger($name);
    $logger->pushHandler(new StreamHandler($filePath, Logger::DEBUG));
    return $logger;
}
?>
