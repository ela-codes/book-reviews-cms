<?php
// Contains the the connection for mysql database using PDO.

// Load environment variables using dotenv package
require dirname(__DIR__, 2) . '/vendor/autoload.php'; // load composer
Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__, 2))->safeLoad();

try {
    $db = new PDO($_ENV["DB_DSN"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
} catch (PDOException $e) {
    print "Error: " . $e->getMessage();
    die(); // Force execution to stop on errors.
    // When deploying to production you should handle this
    // situation more gracefully. ¯\_(ツ)_/¯
}
?>