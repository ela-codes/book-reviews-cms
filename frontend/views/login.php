<?php
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';
require __DIR__ . '/../includes/auth_helper.php';

// Create a logger instance
$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
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

/**
 * Retrieves the user id based on the submitted username.
 * @param PDO $db A PDO object representing the user database.
 * @param string $username A string representation of a username.
 * @return string A string representing the user id.
 */
function getUserId($db, $username)
{
    $statement = $db->prepare("SELECT user_id FROM user WHERE username = :username");
    $statement->bindValue(":username", $username);
    $statement->execute();

    return $statement->fetchColumn();
}

// declare global variables
$usernameFeedback = "";
$passwordFeedback = "";
$isValidCredentials = null;

if (isset($_POST["username"]) && isset($_POST["password"])) {
    // sanitize data
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $logger->debug("Form submitted: Username - $username");

    // perform data validation on sanitized data
    if (validateLogin($db, $username, $password)) {
        // set user session
        session_start();
        $userId = getUserId($db, $username);
        $userRole = getRole($db, $userId);

        $isValidCredentials = true; // set toggle to enable feedback message
        $logger->debug("password verified. session started...");
        $_SESSION["username"] = $username;
        $_SESSION["user_id"] = $userId;
        $_SESSION["logged_in"] = true;
        $_SESSION["last_activity"] = time();
        $_SESSION["role"] = $userRole;

        $logger->debug("Session created succesful: Username - {$_SESSION["username"]}, User_id - {$_SESSION["user_id"]}");

        // redirect user to user's dashboard page
        if ($userRole === "ADMIN") {
            header("Location: https://localhost/WD2/book-reviews-cms/frontend/auth_user/admin_dashboard.php");
        } elseif ($userRole === "USER") {
            header("Location: https://localhost/WD2/book-reviews-cms/frontend/views/dashboard.php");
        }
        exit();
    } else {
        // if invalid credentials show alert message
        $isValidCredentials = false;
    }

}

?>

<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <header class="mb-auto">
            <nav class="navbar navbar-expand-sm">
                <div class="container h-100">
                    <a href="../index.php" class="navbar-brand">BookReviews</a>
                    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#homeNav"
                        aria-controls="homeNav" aria-label="Expand Navigation Bar">
                        <div class="navbar-toggler-icon"></div>
                    </button>
                    <div class="collapse navbar-collapse" id="homeNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a href="../views/register.php" class="nav-link">Register</a>
                            </li>
                            <li class="nav-item">
                                <a href="../views/browse.php" class="nav-link">Browse</a>
                            </li>
                            <li class="nav-item">
                                <a href="../views/search.php" class="nav-link">Search</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container w-50">
                <h2 class="bg-dark text-white ps-2 my-5">Welcome Back!</h2>
                <form action="login.php" method="post" id="loginForm">
                    <?php if (isset($_GET['timeout']) && $_GET['timeout'] == 1): ?>
                        <div class="alert alert-warning" role="alert">
                            Your session has expired. Please log in again.
                        </div>
                    <?php endif; ?>
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isValidCredentials === false): ?>
                        <div class="alert alert-danger" role="alert">
                            Invalid username and/or password. Please try again.
                        </div>
                    <?php endif; ?>
                    <ul class="list-unstyled">
                        <li>
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="loginUsername" name="username" value=""
                                required />
                        </li>
                        <li>
                            <label for="username" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="loginPassword" value=""
                                required />
                        </li>
                    </ul>

                    <button type="submit" class="btn btn-dark mt-3">Log In</button>
                </form>
            </div>
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>