<?php
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';

// Create a logger instance
$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
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
function addUser($db, $username, $email, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // using bcrypt hashing algorithm
    $defaultRole = "USER";

    $query = "INSERT INTO user(username, password, email, role) VALUES (:username, :password, :email, :role)";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", trim($username));
    $statement->bindValue(":email", trim($email));
    $statement->bindValue(":password", $hashedPassword);
    $statement->bindValue(":role", $defaultRole);

    if($statement->execute()) {
        header("Location: dashboard.php");
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
<html class="h-100" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <header class="mb-auto">
            <nav class="navbar navbar-expand-sm">
                <div class="container h-100">
                    <a href="../index.php" class="navbar-brand">BookReviews</a>
                    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#homeNav" aria-controls="homeNav" aria-label="Expand Navigation Bar">
                        <div class="navbar-toggler-icon"></div>
                    </button>
                    <div class="collapse navbar-collapse" id="homeNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a href="../views/login.php" class="nav-link">Log In</a>
                            </li>
                            <li class="nav-item">
                                <a href="../views/browse.php" class="nav-link">Browse</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container w-50">
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
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

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

