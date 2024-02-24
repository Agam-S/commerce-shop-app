<?php
require_once("../scripts/db.php");
require_once("../scripts/functions.php");

try {
    $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedID = isset($_POST['productID']) ? $_POST['productID'] : 'Not selected';
    $selectedSize = isset($_POST['size']) ? $_POST['size'] : 'Not selected';
    $selectedQuantity = isset($_POST['quantity']) ? $_POST['quantity'] : '1';

    // Call the function to add the product to the cart
    addProductToCart($selectedID, $selectedSize, $selectedQuantity, $conn);

    // Redirect to a different page to avoid form resubmission on refresh
    header("Location: cart.php");
    exit();
}

function addProductToCart($id, $selectedSize, $selectedQuantity, $conn)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM Products WHERE productID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {

            $product = array(
                'productID' => $result['productID'],
                'productName' => $result['productName'],
                'productPrice' => $result['productPrice'],
                'productDesc' => $result['productDesc'],
                'productImage' => $result['productImage'],
                'productStock' => $result['productStock'],
                'size' => $selectedSize,
                'quantity' => $selectedQuantity
            );

            $found = false;

            // Loop through the cart items to check for a matching product and size
            foreach ($_SESSION['cart'] as &$cartItem) {
                if ($cartItem['productID'] === $product['productID'] && $cartItem['size'] === $selectedSize) {
                    // Update the quantity
                    $cartItem['quantity'] += $selectedQuantity;
                    $found = true;
                    break;
                }
            }

            // If no match was found, add a new item to the cart
            if (!$found) {
                $_SESSION['cart'][] = $product;
            }
        } else {
            echo "Product not found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>
    <?= templateNavBar() ?>
    <div id="underNavBar" style="background:#061A39; color:white; padding-bottom: 10px; margin-bottom:3.5rem;">
        <h1 class="display-5 text-center">Shopping Cart</h1>
        <h2 class="lead text-center">Edit or remove from cart</h2>
    </div>
    <br>

    <?php
    if (isset($_SESSION['cart'])) {
        $cart = $_SESSION['cart'];

        if (!empty($cart)) {
            $numItems = count($cart);


            for ($i = 0; $i < $numItems; $i++) {
                $product = [
                    'arrayID' => $i,
                    'productName' => $cart[$i]['productName'],
                    'productPrice' => $cart[$i]['productPrice'],
                    'productImage' => $cart[$i]['productImage'],
                    'userSelectedSize' => $cart[$i]['size'],
                    'userSelectedQuantity' => $cart[$i]['quantity']
                ];

                $products[] = $product; // Add the product to the array
            }
            echo <<<HTML
            <section class="pt-5 pb-5">
                <div class="container">
                    <div class="row w-100">
                        <div class="col-lg-12 col-md-12 col-12">
                            <table id="shoppingCart" class="table table-condensed table-responsive">
                                <thead>
                                    <tr>
                                        <th style="width:60%">Product</th>
                                        <th style="width:12%">Price</th>
                                        <th style="width:10%">Quantity</th>
                                        <th style="width:16%"></th>
                                    </tr>
                                </thead>
                                <tbody>
            HTML;

            $subTotal = 0;
            foreach ($products as $product) {
                include 'cartItems.php';
                $subTotal += $product['productPrice'] * $product['userSelectedQuantity'];
            }

            echo <<<HTML
                                </tbody>
                            </table>
                            <div class="text-end">
                                <h4>Subtotal:</h4>
                                <h1>$$subTotal</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 d-flex align items-center">
                        <div class="col-sm-6 order-md-2 text-end">
                            <a href="checkout.php" class="btn btn-primary mb-4 btn-lg pl-5 pr-5 mx-3">Checkout</a>
                        </div>
                        <div class="col-sm-6 mb-3 mb-m-1 order-md-1 text-md-left">
                            <a href="shop.php" style="text-decoration: none;">
                                <i class="fas fa-arrow-left mr-2"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            HTML;
        } else {
            echo <<<HTML
            <div class="container">
                <div class="row">
                    <div class="offset-lg-3 col-lg-6 col-md-12 col-12 text-center">
                        <img src="../assets/images/AdobeStock_623046799.jpeg" alt="" class="img-fluid mb-4">
                        <h2>Your shopping cart is currently empty</h2>
                        <a href="shop.php" class="btn btn-primary my-2">Explore Products</a>
                    </div>
                </div>
            </div>
            HTML;
        }
    } else {
        echo <<<HTML
        <div class="container">
            <div class="row">
                <div class="offset-lg-3 col-lg-6 col-md-12 col-12 text-center">
                    <img src="../assets/images/AdobeStock_623046799.jpeg" alt="" class="img-fluid mb-4">
                    <h2>Your shopping cart is currently empty</h2>
                    <a href="shop.php" class="btn btn-primary my-2">Explore Products</a>
                </div>
            </div>
        </div>
        HTML;
    }

    ?>

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
    
    <script src="../assets/js/script.js"></script>

</body>

</html>