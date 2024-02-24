<?php
require_once("../scripts/db.php");
require_once("../scripts/functions.php");

try {
    $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $creditCard = $_POST['credit_card'] ?? '';
    $expiryDate = $_POST['expiry_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $cardHolderName = $_POST['credit_card_name'] ?? '';

    if (empty($creditCard) || !is_numeric($creditCard)) {
        $errors['credit_card'] = "Invalid credit card number";
    }

    if (empty($expiryDate)) {
        $errors['expiry_date'] = "Expiry date is required";
    }

    if (empty($cvv) || !is_numeric($cvv) || strlen($cvv) !== 3) {
        $errors['cvv'] = "Invalid CVV. CVV must be a 3-digit number.";
    }

    if (empty($cardHolderName)) {
        $errors['card_holder_name'] = "Card holder name is required";
    }

    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (empty($address)) {
        $errors['address'] = "Address is required";
    }

    if (empty($city)) {
        $errors['city'] = "City is required";
    }

    if (empty($errors)) {
        $paymentSuccess = true;

        if ($paymentSuccess) {
            header("Location: thankYou.php");
            exit();
        } else {
            $errors[] = "Payment failed. Please try again.";
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/checkout.css">

</head>

<body>
    <?= templateNavBar() ?>

    <div id="underNavBar" style="background:#061A39; color:white; padding-bottom: 10px; margin-bottom:3.5rem;">
        <h1 class="display-5 text-center">Checkout</h1>
        <h2 class="lead text-center">Payment and Billing information</h2>
    </div>

    <div class="container mx-auto my-5">
        <form id="payment-form" method="post">
            <div class="row justify-content-center">
                <div class="col-md-6 d-flex mb-5 mb-md-0">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">
                                Payment Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="cardHolderName" class="form-label">Card Holder Name:</label>
                                <input type="text" class="form-control" id="cardHolderName" name="credit_card_name" required />
                                <?php if (isset($errors['card_holder_name'])) {
                                    echo '<div class="text-danger">' . $errors['card_holder_name'] . '</div>';
                                } ?>
                            </div>
                            <div class="mb-3">
                                <label for="cardNumber" class="form-label">Card Number:</label>
                                <input type="text" class="form-control" id="cardNumber" name="credit_card" placeholder="Valid Card Number" required />
                                <?php if (isset($errors['credit_card'])) {
                                    echo '<div class="text-danger">' . $errors['credit_card'] . '</div>';
                                } ?>
                            </div>
                            <div class="mb-3">
                                <label for="expiryDate" class="form-label">Expiry Date:</label>
                                <input type="text" class="form-control" id="expiryDate" name="expiry_date" required />
                                <?php if (isset($errors['expiry_date'])) {
                                    echo '<div class="text-danger">' . $errors['expiry_date'] . '</div>';
                                } ?>
                            </div>
                            <div class="mb-3">
                                <label for="cvCode" class="form-label">CVV:</label>
                                <input type="text" class="form-control" id="cvCode" name="cvv" required />
                                <?php if (isset($errors['cvv'])) {
                                    echo '<div class="text-danger">' . $errors['cvv'] . '</div>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex mb-5 mb-md-0">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h3 class="card-title">
                                Billing Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" required />
                                <?php if (isset($errors['name'])) {
                                    echo '<div class="text-danger">' . $errors['name'] . '</div>';
                                } ?>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address:</label>
                                <input type="text" class="form-control" id="address" name="address" required />
                                <?php if (isset($errors['address'])) {
                                    echo '<div class="text-danger">' . $errors['address'] . '</div>';
                                } ?>
                            </div>

                            <div class="mb-3">
                                <label for="city" class="form-label">City:</label>
                                <input type="text" class="form-control" id="city" name="city" required />
                                <?php if (isset($errors['city'])) {
                                    echo '<div class="text-danger">' . $errors['city'] . '</div>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-center">
        <button class="btn btn-primary btn-lg" id="submit-button">Submit Payment</button>
    </div>

    <script>
        document.getElementById("submit-button").addEventListener("click", function() {
            document.getElementById("payment-form").submit();
        });
    </script>


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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>