<?php
require_once("../scripts/functions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Thank You for Shopping</title>
</head>

<body>
    <?= templateNavBar() ?>
    <div id="underNavBar" style="background:#061A39; color:white; padding-bottom: 10px; margin-bottom:3.5rem;">
        <h1 class="display-5 text-center">Order Confirmation</h1>
        <!-- <h2 class="lead text-center">Give your self a unique look</h2> -->
    </div>
    <br />

    <div class="container d-flex align-items-center justify-content-center mb-5 pb-5" style="min-height: 50vh;">
        <div class="my-auto">
            <div class="text-center">
                <img src="../assets/images/tick.png" alt="" style="width:200px; height:200px;">
            </div>
            <h1 class="display-1 text-center">Thank You</h1>
            <p class="lead text-center">
                Your order was completed successfully.
            </p>
            <p class="lead text-center mt-3">
                Scroll below to see order details
                <i class="fa-solid fa-arrow-down-long"></i>
            </p>
            <div class="text-center">
                <a class="btn btn-primary" href="home.php" role="button">Back to Home</a>
            </div>

        </div>
    </div>
    <h3 class="display-5 text-center">Order Details</h3>
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
                                    <th style="width:10%">Price</th>
                                    <th style="width:10%">Quantity</th>
                                    <!-- <th style="width:16%"></th> -->
                                </tr>
                            </thead>
                            <tbody>
        HTML;

            $subTotal = 0;

            foreach ($products as $product) {
                if (strlen($product['productImage']) < 80) {
                    $imagePath = $product['productImage'];
                } else {
                    $imagePath = 'data:image/jpeg;base64,' . base64_encode($product['productImage']);
                }
                echo <<<HTML
                    <tr>
                        <td data-th="Product">
                            <div class="row">
                                <div class="col-md-3 text-left">
                                    <img src="{$imagePath}" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                </div>
                                <div class="col-md-9 text-left mt-sm-2">
                                    <h5 class="text-uppercase">{$product['productName']}</h5>
                                    <p class="font-weight-light">Size: {$product['userSelectedSize']}</p>
                                </div>
                            </div>
                        </td>
                        <td data-th="Price">$ {$product['productPrice']}</td>
                        <td data-th="Quantity">{$product['userSelectedQuantity']}</td>
                    </tr>
                HTML;


                echo '</tr>';

                $subTotal += $product['productPrice'] * $product['userSelectedQuantity'];
            }

            echo <<<HTML
                            </tbody>
                        </table>
                        <div class="text-end">
                            <h4>Final amount:</h4>
                            <h1>$$subTotal</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        HTML;
        }
    } else {
        header("Location: home.php");
    }
    ?>

    <?php
    unset($_SESSION['cart']);
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