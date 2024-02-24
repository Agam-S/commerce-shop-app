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

$name = "";
$email = "";
$question = "";

if (isset($_POST['submitForm'])) {
    $name = $_POST['name'];
    $name = '"' . $name . '"';
    $email = $_POST['email'];
    $email = '"' . $email . '"';
    $question = $_POST['question'];
    $question = '"' . $question . '"';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO Feedback (feedbackName, feedbackEmail, feedbackQues)
        VALUES ($name, $email, $question)";
        $conn->exec($sql);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
    echo "<script>alert('Your Feedback has been submitted! We will contact you shortly!')</script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/home.css">

    <head>

    <body>
        <?= templateNavBar() ?>


        <!-- Content section -->
        <div class="container-fluid" style="background-color: #071220;">
            <div class="row align-items-start w-100 mx-auto">
                <div class="col-md-8 col-12 mx-auto my-5">
                    <div class="homePageText" style="max-width: 500px; margin:auto;">
                        <h2 class="text-truncate" id="content-text">Elevate Your Style</h2>
                        <p class="text-light d-inline-block text-truncate">
                            Discover unique and limited-edition<br>
                            styles, from trendsetting designs to<br>
                            timeless classics. We're your<br>
                            exclusive fashion destination.<br><br>
                            <a href="shop.php"><button class="explorenow-btn">Explore Now</button></a>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 col-12 center-image">
                    <img class="img" src="../assets/images/demo1.png" alt="Demo T-shirt" style="max-height: 400px;">
                </div>
            </div>
        </div>


        <div class="container">
            <h2 class="featured-heading">Featured Products</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="product-demo">
                        <a href="shopOpen.php?id=1">
                            <img src="../assets/images/TeeShirt-1.png" class="product-img" alt="TeeShirt 1">
                        </a>
                        <div class="product-body">
                            <h2 class="product-title">PREMIUM FIRST EDITION SMILEY T-SHIRT</h2>
                            <p class="product-text">$55</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="product-demo">
                        <a href="shopOpen.php?id=2">
                            <img src="../assets/images/TeeShirt-2.png" class="product-img" alt="TeeShirt 2">
                        </a>
                        <div class="product-body">
                            <h2 class="product-title">PREMIUM LIMITED EDITION WHITE T-SHIRT</h2>
                            <p class="product-text">$55</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="product-demo">
                        <a href="shopOpen.php?id=3">
                            <img src="../assets/images/TeeShirt-3.png" class="product-img" alt="TeeShirt 3">
                        </a>
                        <div class="product-body">
                            <h2 class="product-title">PREMIUM WORLDWIDE WHITE T-SHIRT</h2>
                            <p class="product-text">$35</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="featured-button">
            <a href="shop.php" class="view-all-btn ">View All</a>
        </div>



        <div id="contact-form" style="margin-top: 10px; margin-bottom: 10px; background-color: #071220;">
            <form action="" method="post" style="max-width:35rem;" class="mx-auto p-4">
                <h1 class="con text-center" style="font-family: cursive; color: white;">Contact Us</h1>
                <div class="form-group">
                    <label for="name" style="text-align: left; color: white;">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" style="width: 100%;" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <label for="email" style="text-align: left; color: white;">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" style="width: 100%;" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <label for="question" style="text-align: left; color: white;">Question:</label>
                    <textarea class="form-control" id="question" name="question" style="width: 100%;" placeholder="Your Question" rows="4" required></textarea>
                </div>
                <button type="submit" name="submitForm" class="btn btn-primary mx-auto d-flex my-3">Ask question</button>
            </form>
        </div>



        <div class="feedback-row">
            <div class="feedback-column" id="feedback-content">
                <h2 id="feedback-content-text">What People Say About Our Products</h2>

                <div id="carouselIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselIndicators" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselIndicators" data-slide-to="1"></li>
                        <li data-target="#carouselIndicators" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item active text-center p-4">
                            <blockquote class="blockquote text-center">
                                <p class="mb-0"><i class="fas fa-quote-left"></i> Very fantastic design and material looks lovely on me.</p>
                                <label class="blockquote-footer">Kyle</label>
                                <p class="review-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <i class="far fa-star"></i>
                                </p>
                            </blockquote>
                        </div>
                        <div class="carousel-item text-center p-4">
                            <blockquote class="blockquote text-center">
                                <p class="mb-0"><i class="fas fa-quote-left"></i> Absolutely adore the trendy styles this brand offers. The clothes fit so well and they look amazing.</p>
                                <label class="blockquote-footer">Jane</label>
                                <p class="review-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </p>
                            </blockquote>
                        </div>
                        <div class="carousel-item text-center p-4">
                            <blockquote class="blockquote text-center">
                                <p class="mb-0"><i class="fas fa-quote-left"></i> The clothing from this brand is not only stylish but also incredibily comfortable, making it a top choice for both<br> fashion-forward individuals and those seeking everyday wear.</p>
                                <label class="blockquote-footer">User</label>
                                <p class="review-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </p>
                            </blockquote>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
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

            <!-- Cookie Consent Banner -->
            <div id="cookieConsent" class="alert alert-dark alert-info alert-dismissible fade show px-auto" role="alert" style="position: fixed; bottom: 0; left: 0; right: 0; text-align: center; z-index: 9999; margin-bottom: 0; background-color:#071220; color:white; border: 0px; border-radius:0px; padding-right: 10px; padding-left:10px;">
                This website uses cookies and other technologies to ensure you get the best experience on our website and to comply with our privacy policy.
                <div class="mx-auto">
                    <button type="button" class="btn btn-primary btn-sm mx-4 my-2" id="acceptCookie" tabindex="1">Accept</button>
                    <button type="button" class="btn btn-secondary btn-sm my-2" id="rejectCookie" tabindex="2">Reject</button>
                    <button type="button" class="btn btn-secondary btn-sm mx-4 my-2" id="cookiePolicy" data-bs-toggle="modal" data-bs-target="#cookiePolicyModal" tabindex="3">Learn more</button>
                </div>
                <div class="modal fade" id="cookiePolicyModal" tabindex="-1" aria-labelledby="cookiePolicyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cookiePolicyModalLabel" style="color:black;">Data Storage and Usage</h5>
                                <button type="button" class="btn-close mx-auto my-auto " data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start" style="color: black;">
                                <p><strong>Cookies:</strong></p>
                                <p>We do not use third-party cookies. If you choose to click 'Reject,' you will encounter the cookie pop-up again during subsequent visits, as you have not yet accepted the use of cookies.</p>

                                <p><strong>Session Storage:</strong></p>
                                <p>Session storage is a temporary storage mechanism that maintains data for the duration of your visit. When you close your browser, the session data is automatically cleared. We use session storage for the following purposes:</p>
                                <ul>
                                    <li>Maintaining your session while you navigate our site.</li>
                                    <li>Improving user experience during your visit.</li>
                                </ul>

                                <p><strong>Local Storage:</strong></p>
                                <p>Local storage allows us to store data on your device for a longer period. This data is not automatically cleared when you close your browser. We use local storage for the following purposes:</p>
                                <ul>
                                    <li>Remembering your preferences and settings.</li>
                                    <li>Providing a more personalized experience.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="../assets/js/cookie.js"></script>
            <script src="../assets/js/script.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>


    </body>

</html>