<?php
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create a logger instance
$logger = new Logger('login');
$logger->pushHandler(new StreamHandler('./../logs/login.log', Logger::DEBUG));
$logger->info('Login page loaded');

/**
 * Verifies the given login username and password.
 * @param PDO $db A PDO object representing the user database.
 * @param string $username A string representation of a username.
 * @param string $password A string representation of an email.
 * @return bool Returns true if the user is an authorized user. False, otherwise.
 */
function validateLogin($db, $username, $password)
{
    // fetch the stored password based on username
    $statement = $db->prepare("SELECT password FROM user WHERE username = :username");
    $statement->bindValue(":username", $username);
    $statement->execute();

    $hashedPassword = $statement->fetchColumn();

    // check that password related to that username exists and it passes verification 
    $isPasswordValid = password_verify($password, $hashedPassword);

    return $hashedPassword && $isPasswordValid;

}


$usernameFeedback = "";
$passwordFeedback = "";
$isValidCredentials = null;

if (isset($_POST["username"]) && isset($_POST["password"])) {
    // sanitize data
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $logger->debug("Form submitted: Username - $username, Password - $password");

    // perform data validation on sanitized data
    if (validateLogin($db, $username, $password)) {
        // set user session
        session_start();

        $isValidCredentials = true; // set toggle to enable feedback message
        $logger->debug("session started...");
        $_SESSION["username"] = $username;
        $_SESSION["logged_in"] = true;
        $_SESSION["last_activity"] = time();

        // redirect user to user's dashboard page
        header("Location: dashboard.php");
        exit();
    } else {
        // if invalid credentials show alert message
        $isValidCredentials = false;
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <main>
        <div class="container">
            <h2 class="mt-5">Welcome Back!</h2>
            <form action="login.php" method="post" id="loginForm">
                <?php if (!$isValidCredentials): ?>
                    <div class="alert alert-danger" role="alert">
                        Invalid username and/or password. Please try again.
                    </div>
                <?php endif; ?>
                <ul class="list-unstyled">
                    <li>
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="loginUsername" name="username" value="bobthebuilder"
                            required />
                    </li>
                    <li>
                        <label for="username" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="loginPassword" value="b12345"
                            required />
                    </li>
                </ul>

                <button type="submit" class="btn btn-primary mt-3">Log In</button>
            </form>
        </div>
    </main>

    <?php if (isset($_GET['timeout']) && $_GET['timeout'] == 1): ?>
        <div class="alert alert-warning" role="alert">
            Your session has expired. Please log in again.
        </div>
    <?php endif; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>