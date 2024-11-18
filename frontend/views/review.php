<?php
// require __DIR__ . '/../includes/session_handler.php';
// require __DIR__ . '/../../vendor/autoload.php';
// require __DIR__ . '/../../backend/config/database.php';
// require __DIR__ . '/../../debug/logger.php';

// // Create a logger instance
// $logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
// checkSession();

// $logger->info("Dashboard page loaded for username - {$_SESSION["username"]}, id - {$_SESSION["user_id"]}");

if($_SERVER["REQUEST_METHOD"] === "GET") {
    // Sanitize
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Validate to ensure id is an integer
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {

        // Build and prepare SQL String with :id placeholder parameter.
        $query = "SELECT * FROM review WHERE id = :id";
        $statement = $db->prepare($query);

        // Bind the :id parameter with binding-type of Integer.
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Fetch the row selected by primary key id.
        $row = $statement->fetch();

    } else {
        header("Location: dashboard.php"); // Redirect if ID is not an integer
        exit;
    }
}
?>

<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="d-flex h-100 text-center">
    <div class="container-fluid d-flex flex-column">
        <header class="mb-auto">
            <nav class="navbar navbar-expand-sm">
                <div class="container h-100">
                    <a href="index.php" class="navbar-brand">BookReviews</a>
                    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#homeNav" aria-controls="homeNav" aria-label="Expand Navigation Bar">
                        <div class="navbar-toggler-icon"></div>
                    </button>
                    <div class="collapse navbar-collapse" id="homeNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a href="./views/login.php" class="nav-link">Log In</a>
                            </li>
                            <li class="nav-item">
                                <a href="./views/browse.php" class="nav-link">Browse</a>
                            </li>
                            <li class="nav-item">
                                <a href="./views/browse.php" class="nav-link">Dashboard</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="row">
            <div class="container w-50">


            </div>
        </main>
        <?php require __DIR__ . "/includes/footer.php" ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>