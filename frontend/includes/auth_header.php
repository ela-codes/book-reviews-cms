<?php

?>


<header class="mb-auto">
    <nav class="navbar navbar-expand-sm">
        <div class="container h-100">
            <a href="../index.php" class="navbar-brand">BookReviews</a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#homeNav" aria-controls="homeNav"
                aria-label="Expand Navigation Bar">
                <div class="navbar-toggler-icon"></div>
            </button>
            <div class="collapse navbar-collapse" id="homeNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="../../frontend/views/dashboard.php" class="nav-link">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="../../frontend/views/browse.php" class="nav-link">Browse</a>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="modal" data-bs-target="#modalId">Logout</button>

                        <!-- Modal Body, hidden by default-->
                        <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"
                                role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTitleId">Log Out</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">Are you sure you want to log out?</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <a class="btn btn-primary" href="../index.php">Yes</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>