
<?php //header.php
session_start();
$loggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $activePage; ?></title>
        <link rel="stylesheet" href="/assets/css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    </head>

    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">

            <!-- Left Side Logo -->
            <a class="navbar-brand fw-bold fs-1" href="/index.php">eBarter</a>

            <!-- Collapse Button !! possibly fix style issues on collapsed navbar !! -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navCollapse">
                <i id="toggleIcon" class="fa-solid fa-chevron-up"></i>
            </button>

            <!-- Full Navbar -->
            <div class="collapse navbar-collapse" id="navCollapse">

                <!-- Middle Navigation Links -->
                <ul class="navbar-nav mx-auto fs-5 fw-bold mid-bar">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage === 'Home') ? 'active' : ''; ?>" href="/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage === 'Dashboard') ? 'active' : ''; ?>" href="/dashboard/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage === 'Contact') ? 'active' : ''; ?>" href="/contact.php">Contact</a>
                    </li>
                </ul>

                <!-- Right Side Icons -->
                <ul class="navbar-nav align-items-center">

                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="#">
                            <i class="fa-solid fa-cart-shopping fa-lg"></i>
                        </a>
                    </li>

                    <?php if ($loggedIn): ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user fa-lg"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/auth/logout.php">Logout</a></li>
                        </ul>
                    </li>

                    <?php else: ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user fa-lg"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/login.php">Login</a></li>
                            <li><a class="dropdown-item" href="/register.php">Register</a></li>
                        </ul>
                    </li>

                    <?php endif; ?>

                </ul>

            </div>
        </div>
    </nav>



    <script>
    // collapsed navbar chevron change
    document.addEventListener("DOMContentLoaded", function () {
        const icon = document.getElementById("toggleIcon");
        const navbarCollapse = document.getElementById("navCollapse");

        navbarCollapse.addEventListener("show.bs.collapse", function () {
            icon.classList.remove("fa-chevron-up");
            icon.classList.add("fa-chevron-down");
        });

        navbarCollapse.addEventListener("hide.bs.collapse", function () {
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        });
    });
    </script>



