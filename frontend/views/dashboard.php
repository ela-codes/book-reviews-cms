<?php
require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
checkSession();






?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <header class="mb-auto">
        <nav class="navbar navbar-expand-sm">
            <div class="container h-100">
                <a href="#" class="navbar-brand">BookReviews</a>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#homeNav"
                    aria-controls="homeNav" aria-label="Expand Navigation Bar">
                    <div class="navbar-toggler-icon"></div>
                </button>
                <div class="collapse navbar-collapse" id="homeNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="modal" data-bs-target="#modalId">Logout</button>

                                <!-- Modal Body, hidden by default-->
                                <div class="modal fade" id="modalId" tabindex="-1" role="dialog"
                                    aria-labelledby="modalTitleId" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalTitleId">Log Out</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">Are you sure you want to log out?</div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <a class="btn btn-primary" href="logout.php">Yes</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Browse</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>welcome to your dashboard!</h1>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>