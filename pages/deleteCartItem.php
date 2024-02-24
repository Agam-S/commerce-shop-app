<?php
session_start();

if (isset($_POST['productIndex'])) {
    $productIndex = (int)$_POST['productIndex']; // Convert to an integer

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $cart = $_SESSION['cart'];

        // Check if the product index is valid
        if ($productIndex >= 0 && $productIndex < count($cart)) {

            unset($cart[$productIndex]);

            // Re-index array
            $cart = array_values($cart);

            // Update the cart in the session
            $_SESSION['cart'] = $cart;
        }
    }
}

// Redirect back to the shopping cart page
header('Location: cart.php');
