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
    $query = "SELECT user_id, username, email, role, created_at, created_by FROM user";
    $statement = $db->prepare($query);

    $statement->execute();
    $result = $statement->fetchAll();

    return $result;
}

function editUsername()
{

}

function editEmail()
{

}

function editRole()
{

}

function deleteUser($db, $userId)
{
    try {
        $query = "DELETE FROM user WHERE user_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);

        return $statement->execute();
    } catch (Exception $e) {
        error_log("Error deleting user: " . $e->getMessage());
        return false;
    }
}


// Show all users in admin dashboard
$users = getAllUsers($db);



// Handle the user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["id"])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $logger->debug("Received delete request for user #$id");

    if (deleteUser($db, $id)) {
        $_SESSION['success_message'] = "User deleted successfully.";
        header("Location: admin_dashboard.php"); // Redirect to avoid form resubmission
        exit();
    } else {
        $logger->debug("Failed to delete user.");
        $_SESSION['error_message'] = "Failed to delete user.";
        header("Location: admin_dashboard.php");
        exit();
    }
}


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
            <h2 class="bg-dark text-white ps-2 my-5">admin dashboard</h2>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); // Clear the message after displaying ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

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
                            <th scope="col">User ID</th>
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
                                <td><?= $user["user_id"] ?></td>
                                <td><?= $user["username"] ?></td>
                                <td><?= $user["email"] ?></td>
                                <td><?= $user["role"] ?></td>
                                <td><?= $user["created_at"] ?></td>
                                <td><?= $user["created_by"] ?></td>
                                <td>
                                    <button class="btn" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn" data-bs-toggle="modal"
                                        data-bs-target="#deleteUser-<?= $user["user_id"] ?>"><i class="bi bi-trash3"></i>
                                    </button>
                                    <!-- Modal Body, hidden by default-->
                                    <div class="modal fade" id="deleteUser-<?= $user["user_id"] ?>" tabindex="-1"
                                        role="dialog" aria-labelledby="#deleteUser-<?= $user["user_id"] ?>"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md"
                                            role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteUserModalTitle">Delete User</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want delete user
                                                        <strong><mark><?= $user["username"] ?></mark></strong> from the
                                                        system?
                                                    </p>
                                                    <p class="text-danger">All of the user's posts, comments, and images will be removed.</p>
                                                    <p class="text-danger">This action cannot be reversed.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <form action="admin_dashboard.php?>" method="post">
                                                        <input type="hidden" name="id" value="<?= $user["user_id"] ?>">
                                                        <input type="submit" name="delete" value="Delete"
                                                            class="btn btn-primary" />
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
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