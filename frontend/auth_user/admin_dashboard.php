<?php
// Admin users must have the ability to view all registered users, add users, update users, and delete users.

// INSERT INTO `user` (`user_id`, `username`, `password`, `email`, `role`, `created_at`, `last_login`) VALUES ('1', 'admin', 'admin123', 'admin@bookreviews.com', 'ADMIN', current_timestamp(), current_timestamp()) 

require __DIR__ . '/../includes/session_handler.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';
require __DIR__ . '/../../debug/logger.php';
require __DIR__ . '/../includes/auth_helper.php';

$logger = getLogger("AuthLog", __DIR__ . '/../../debug/userAuth.log');
$logger->info("Admin dashboard page loaded");

checkSession();

$headerLink = __DIR__ . "/../includes/admin_header.php";

function getAllUsers($db)
{
    $query = "SELECT username, email, role, created_at, created_by FROM user";
    $statement = $db->prepare($query);

    $statement->execute();
    $result = $statement->fetchAll();

    return $result;
}

$users = getAllUsers($db);





?>

<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex h-100">
    <div class="container-fluid d-flex flex-column">
        <?php require $headerLink ?>
        <main id="mainContent" class="container h-100 my-4">
            <h2 class="bg-dark text-white ps-2 mb-5">admin dashboard</h2>
            <div class="row my-3">
                <div class="col">
                    <h5>Number of active users: <?= count($users); ?></h5>
                </div>
                <div class="col">
                    <a href="https://localhost/WD2/book-reviews-cms/frontend/views/register.php"
                        class="btn btn-primary btn-md btn-dark text-white float-end">Add New User</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Created By</th>
                            <th scope="col" colspan="2">Modify</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user["username"] ?></td>
                                <td><?= $user["email"] ?></td>
                                <td><?= $user["role"] ?></td>
                                <td><?= $user["created_at"] ?></td>
                                <td><?= $user["created_by"] ?></td>
                                <td><i class="bi bi-pencil-square"></i></td>
                                <td><i class="bi bi-trash3"></i></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <?php require __DIR__ . "/../includes/footer.php" ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>