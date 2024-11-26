<?php

$indexLink = "https://localhost/WD2/book-reviews-cms/frontend/index.php";
$loginLink = "https://localhost/WD2/book-reviews-cms/frontend/views/login.php";
$browseLink = "https://localhost/WD2/book-reviews-cms/frontend/views/browse.php";
$searchLink = "https://localhost/WD2/book-reviews-cms/frontend/views/search.php";

?>

<header class="mb-auto">
    <nav class="navbar navbar-expand-sm">
        <div class="container h-100">
            <a href=<?= $indexLink ?> class="navbar-brand">BookReviews</a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#homeNav" aria-controls="homeNav"
                aria-label="Expand Navigation Bar">
                <div class="navbar-toggler-icon"></div>
            </button>
            <div class="collapse navbar-collapse" id="homeNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href=<?= $loginLink ?> class="nav-link">Log In</a>
                    </li>
                    <li class="nav-item">
                        <a href=<?= $browseLink ?> class="nav-link">Browse</a>
                    </li>
                    <li class="nav-item">
                        <a href=<?= $searchLink ?> class="nav-link">Search</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>