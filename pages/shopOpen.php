<?php
session_start();

if (!isset($_SESSION)) {
    session_start();
}

$id = 0;
if (isset($_GET['id'])) {
    $id = ($_GET['id']);
} else {
    echo "error";
}

require_once("../scripts/db.php");
require_once("../scripts/functions.php");

try {
    $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
try {
    $stmt = $conn->prepare("SELECT * FROM Products WHERE productID = " . $id);
    $stmt->execute();

    $result = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

foreach ($result as $row) {
    //adds each row returned into an associative array
    $product = array(
        'productID' => $row['productID'],
        'productName' => $row['productName'],
        'productPrice' => $row['productPrice'],
        'productDesc' => $row['productDesc'],
        'productImage' => $row['productImage'],
        'productStock' => $row['productStock']
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['productName'] ?></title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/shop.css">
    <link rel="stylesheet" href="../assets/css/shopOpen.css">
</head>

<body>

    <?= templateNavBar() ?>

    <div id="underNavBar" style="background:#061A39; color:white; padding-bottom: 10px; margin-bottom:3.5rem;">
        <h1 class="display-5 text-center">Shop</h1>
        <h2 class="lead text-center">Give your self a unique look</h2>
    </div>

    <style>
        .center-image {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>

    <div class="container">
        <div class="row align-items-start w-100 mx-auto">
            <div class="col-md-4 col-12 text-center center-image">
                <?php
                    if (strlen($row['productImage']) < 80) {
                        $imagePath = ($row['productImage']);
                    } else {
                    $imagePath = 'data:image/jpeg;base64,' . base64_encode($row['productImage']);
                    }
                    ?>
            <img src="<?= $imagePath ?>" alt="<?= $row['productName'] ?>" class="img-thumbnail m-3"">
            </div>
            <div class="col-md-8 col-12">
                <form id="productForm" action="cart.php" method="post">
                    <input type="hidden" name="productID" id="productID" value="<?php echo $product['productID'] ?>">
                    <p class="display-6 text-uppercase text-start m-2"><?php echo $product['productName'] ?></p>
                    <p class="display-6 text-uppercase text-start m-2">$<?php echo $product['productPrice'] ?></p>
                    <p class="lead m-2"><?php echo $product['productDesc'] ?></p>
                    <div class="sizeOptions m-2 mt-4 mb-4">
                        <span class="sizeLabel">Size:</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-dark size-btn" name="size" value="S" required>S</button>
                            <button type="button" class="btn btn-dark size-btn" name="size" value="M" required>M</button>
                            <button type="button" class="btn btn-dark size-btn" name="size" value="L" required>L</button>
                            <button type="button" class="btn btn-dark size-btn" name="size" value="XL" required>XL</button>
                        </div>
                        <span id="sizeErrorMessage" style="color: red; display:none;">*Please select a size</span>
                        <input type="hidden" name="size" id="selectedSize" value="" required>
                    </div>
                    <div class="quantityField m-2 mt-4 mb-4">
                        <span class="quantityLabel">Quantity:</span>
                        <div class="input-group">
                            <button class="btn btn-dark quantityBtn" type="button" id="decrement">-</button>
                            <input type="number" class="form-control quantityInput" name="quantity" value="1" min="1" max="<?php echo $product['productStock']; ?>">
                            <button class="btn btn-dark quantityBtn" type="button" id="increment">+</button>
                        </div>
                    </div>

                    <button type="button" class="cartButton mx-2" onclick="cartClick()">
                        <span class="addToCart">Add to cart</span>
                        <span class="added">Added</span>
                        <i class="fas fa-shopping-cart"></i>
                        <i class="fas fa-box"></i>
                    </button>

                </form>
            </div>
        </div>
    </div>
    <!-- Footer section -->
    <div class="footer">
        <div class="footer-left">
            <p><strong>Deluxe Society</strong></p>
            <p><a href="home.php">Home</a>
            <p><a href="shop.php">Shop</a>
            <p><a href="thread.php">Discussion</a>
            <p><a href="aboutUs.php">About Us</a></p>
        </div>

        <div class="footer-center">
            <p><strong>Shopping</strong></p>
            <p><a href="shop.php">Products</a>
            <p><a href="shop.php">T-shirts</a></p>
            <p><a href="aboutUs.php">Customer Service</a>
            <p><a href="shop.php">Exclusive items</a>
            <p><br>© 2023 Deluxe Society</p>
        </div>

        <div class="footer-right">
            <p><strong>My Account</strong></p>
            <p><a href="login.php">Login/Sign in</a></p>
            <p><a href="cart.php">My cart</a></p>
            <p><a href="profile.php">View/Update Details</a></p>
            <p><a href="profile.php">Change Theme</a></p>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="../assets/js/shopOpen.js"></script>
    <script src="../assets/js/script.js"></script>

</body>

</html>