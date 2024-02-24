<?php
if (!isset($_SESSION)) {
    session_start();
}

function templateNavBar()
{
 
    if (isset($_SESSION['username'])) {
        $user = $_SESSION['username'];

        $user = '"' . $user . '"';
        $servername = "";
        $username = "";
        $password = "";
        
        $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM User WHERE userName = " . $user);
        $stmt->execute();
        $results = $stmt->fetch();
        $uAdmin = $results['isAdmin'];

        $adminLink = $uAdmin == 1 ? '<a class="dropdown-item" href="admin.php">Admin Page</a>' : '';
        $accountLink = '<a class="dropdown-item" href="profile.php">View Account</a>';
        $logoutLink = '<a class="dropdown-item" href="logout.php">Log Out</a>';
        $loginLink = '';
    } else {
        $accountLink = '';
        $logoutLink = '';
        $loginLink = '<a class="dropdown-item" href="login.php">Login</a>';
        $adminLink = '';
    }

    echo <<<EOT
    <nav class="navbar navbar-expand-sm navbar-dark content-wrapper">
        <!-- Logo -->
        <a href="home.php"><img class="navbar-brand logo" src="../assets/images/Logo.png" alt="Brand Logo"></a>

        <!-- Links -->
        <ul class="navbar-nav mx-auto">
            <li class="nav-item">
                <a href="home.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
                <a href="shop.php" class="nav-link">Shop</a>
            </li>
            <li>
                <a href="aboutUs.php" class="nav-link">About Us</a>
            </li>
            <li>
                <a href="thread.php" class="nav-link">Discussion</a>
            </li>
            <li>
                <a href="news.php" class="nav-link">News</a>
            </li>
        </ul>

        <!-- Dropdown -->
        <ul class="navbar-nav ml-auto" id="dropdownNavbar">
            <li class="nav-item">
                <a class="nav-link" href="cart.php" id="navbarCart" role="button">
                    <i class="fa-solid fa-cart-shopping" style="color: #ffbf00;"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarAccount" role="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-user" style="color: #ffb400;"></i>
                </a>
                
                <div class="dropdown-menu position-absolute start-50 translate-middle-x">
                    <a class="dropdown-item" href="cart.php">My Cart</a>
                    $accountLink
                    $loginLink
                    $adminLink
                    $logoutLink
                </div>
            </li>
        </ul>
    </nav>
EOT;
}
