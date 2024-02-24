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
try {
    $stmt = $conn->prepare("SELECT studentID, studentName, studentRole, studentEmail FROM Team");
    $stmt->execute();

    $result = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/thread.css">

</head>

<body>
    <?= templateNavBar() ?>

    <div id="underNavBar" style="background:#061A39; color:white; padding-bottom: 10px; margin-bottom:3.5rem;">
        <h1 class="display-5 text-center">About Us</h1>
        <h2 class="lead text-center">Join our Deluxe Society and be part of a thriving fashion community.</h2>
    </div>
    <br>


    <div class="row container-fluid">
        <div class="col">
            <!-- First Row Content -->
            <h1 class="text-center mb-5" style="font-family: cursive;">Our Team</h1>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
                <?php foreach ($result as $row) : ?>

                    <div class="card mx-auto" style="min-height:200px; max-height:450px; min-width:300px; max-width:300px;">
                        <div class="card-body">
                            <div class="media forum-item">

                                <h6 class="text-title">ID: <?= $row['studentID'] ?></h6>
                            </div>
                            <div class="media-body">
                                <h5><a href="#" class="text-body">Name: <?= $row['studentName'] ?></a></h5>
                                <h6><a href="#" class="text-body">Role: <?= $row['studentRole'] ?></a></h6>
                                <p class="text">Email: <a href="mailto:<?= $row['studentEmail'] ?>"><?= ' ', $row['studentEmail'] ?></a></p>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <!-- Second Row Content -->
            <div class="container-fluid mt-5">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <h1 class="mb-4" style="font-family: cursive;">Terms and Conditions and Privacy Policy</h1>
                    </div>

                    <div class="col-md-6">
                        <div class="accordion" id="termsPrivacyAccordion">
                            <!-- Copyright -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="copyrightHeading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#copyrightCollapse" aria-expanded="true" aria-controls="copyrightCollapse">
                                        Copyright
                                    </button>
                                </h2>
                                <div id="copyrightCollapse" class="accordion-collapse collapse" aria-labelledby="copyrightHeading" data-bs-parent="#termsPrivacyAccordion">
                                    <div class="accordion-body">
                                        <!-- Copyright content here -->
                                        <ul>
                                            <li>User-Generated Content Ownership: Users will retain ownership of the content they post on the discussion page. However, they will grant the platform a non-exclusive, worldwide license to use, display, and distribute their content on the site for its intended purpose.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Privacy Policies -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="privacyHeading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#privacyCollapse" aria-expanded="false" aria-controls="privacyCollapse">
                                        Privacy Policies
                                    </button>
                                </h2>
                                <div id="privacyCollapse" class="accordion-collapse collapse" aria-labelledby="privacyHeading" data-bs-parent="#termsPrivacyAccordion">
                                    <div class="accordion-body">
                                        <!-- Privacy Policies content here -->
                                        <ul>
                                            <li>Data Collection and Usage: Our Privacy Policy outlines the types of personal information we collect, such as usernames and email addresses. The data is used solely for account management, communication, and personalization of the user experience. We do not sell user data to third parties.</li>
                                            <li>User Consent: By using our platform, users implicitly consent to the collection and processing of their data as outlined in the Privacy Policy. We require explicit consent when users sign up or engage with certain features that may require additional data processing.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms of Use -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="termsHeading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#termsCollapse" aria-expanded="false" aria-controls="termsCollapse">
                                        Terms of Use
                                    </button>
                                </h2>
                                <div id="termsCollapse" class="accordion-collapse collapse" aria-labelledby="termsHeading" data-bs-parent="#termsPrivacyAccordion">
                                    <div class="accordion-body">
                                        <!-- Terms of Use content here -->
                                        <ul>
                                            <li>User Conduct: Our Terms of Use clearly define acceptable and prohibited behaviors on the platform. Users are expected to adhere to community guidelines, which include rules against harassment, hate speech, spam, and other harmful activities. Violations may result in account suspension or termination.</li>
                                            <li>Account Suspension/Termination: We reserve the right to suspend or terminate user accounts for repeated violations of the Terms of Use, as well as for any activities that pose a risk to the community or platform integrity.</li>
                                            <li>Dispute Resolution: Any disputes between users and the platform will be resolved through alternative dispute resolution methods, including arbitration or mediation. We may also outline a choice of authority for legal actions if required.</li>
                                            <li>Forum Archiving: The admin site is used to archive forums for historical reference and site maintenance purposes. The archived content remains accessible but cannot be modified or interacted with.</li>
                                            <li>Intellectual Property Rights: We encourage users to report any copyright or intellectual property infringements they come across and emphasize the platform's commitment to respecting these rights.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
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

    <script src="../assets/js/script.js"></script>

</body>

</html>