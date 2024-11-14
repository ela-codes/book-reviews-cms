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
$logger->pushHandler(new StreamHandler('./register.log', Logger::DEBUG));
$logger->info('Registration page loaded');


/**
 * Checks whether the username and email exists in the user database.
 * @param string $username A string representation of a username.
 * @param string $username A string representation of an email.
 * @return bool Whether the username and email exists in the user database. 
 */
function checkUserExists($db, $username, $email) {

    $statement = $db->prepare("SELECT COUNT(*) FROM user WHERE username = :username AND email = :email");
    $statement->bindValue(":username", strtolower(trim($username)));
    $statement->bindValue(":email", strtolower(trim($email)));

    $statement->execute();

    $result = $statement->fetchColumn();

    return $result > 0;
}


/**
 * Checks whether the string of password matches.
 */
function checkPasswordMatch($password, $confirm_password) {
    return strcmp(trim($password), trim($confirm_password)) == 0;
}

/**
 * Checks whether the string of password passes the validation rule.
 */
function checkPasswordPattern($password) {
    return preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/", trim($password)) === 1;
}

/**
 * Checks whether the string of username passes the validation rule.
 */
function checkValidUsername($username) {
    $username = trim($username); // Remove leading and trailing whitespace
    return $username !== "" && preg_match('/^[a-zA-Z0-9]+$/', $username) === 1;
}



/**
 * Adds new user to database.
 */
function addUser($db, $username, $password, $email) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // using bcrypt hashing algorithm
    $defaultRole = "USER";

    $query = "INSERT INTO user(username, password, email, role) VALUES (:username, :password, :email, :role)";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", trim($username));
    $statement->bindValue(":email", trim($email));
    $statement->bindValue(":password", $hashedPassword);
    $statement->bindValue(":role", $defaultRole);

    if($statement->execute()) {
        header("Location: register_success.php?success=1");
        exit();
    }
}

$usernameFeedback = "";
$passwordFeedback = "";
$focusPassword = false;
$focusUsername = false;


/**
 * Handle registration form's submitted data.
 */
if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {

    // sanitize data
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $logger->debug("Form submitted: Username - $username, Email - $email, Password - $password, Confirm_Password - $confirm_password");

    // perform data validation on sanitized data
    if ($username && checkValidUsername($username)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $isExistingUser = checkUserExists($db, $username, $email);
            $isMatchingPassword = checkPasswordMatch($password, $confirm_password);
            
    
            if (!$isExistingUser && $isMatchingPassword) {
                $isValidPasswordPattern = checkPasswordPattern($password);

                if ($isValidPasswordPattern) {
                    addUser($db, $username, $email, $password);
                } else {
                    $passwordFeedback = "Passwords must be at least 6 characters long. \n It must contain 1 letter and 1 number.";
                    $focusPassword = true; // Set focus to password if pattern validation fails
                }
            } else {
                $passwordFeedback = "Both passwords must match.";
                $focusPassword = true; // Set focus to password if password mismatch occurs
            }
        }
    } else {
        $usernameFeedback = "Please enter a username containing only letters and numbers.";
        $focusUsername = true; // Set focus to password if username validation fails
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Register</h2>
        <form action="register.php" method="post" id="registerForm">
            <ul class="list-unstyled">
                <li>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required/>
                    <div id="usernameFeedback" class="text-danger"><?= $usernameFeedback ?></div>
                </li>
                <li>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required/>
                </li>
                <li>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required/>
                </li>
                <li>
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required/>
                    <div id="passwordFeedback" class="text-danger"><?= $passwordFeedback ?></div>
                </li>
                <button type="submit" class="btn btn-primary mt-3">Register</button>
            </ul>
        </form>
    </div>

    <!-- Link to Bootstrap JS and dependencies (jQuery and Popper.js) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        <?php if ($focusPassword): ?>
            document.getElementById('password').focus();  // Focus password field if there is an error
        <?php endif; ?>

        <?php if ($focusUsername): ?>
            document.getElementById('username').focus();  // Focus username field if there is an error
        <?php endif; ?>
    </script>
</body>
</html>

