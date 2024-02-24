<?php
require_once("../scripts/db.php");
require_once("../scripts/functions.php");
require_once("../scripts/verifyUsers.php");
$userName = getUsername();
$customSalt = 10;
$currentUsername = hash('sha256', $userName . $customSalt);

if ($currentUsername && isset($_COOKIE['UID'])) {
    if ($_COOKIE['UID'] !== $currentUsername) {
        header("Location: home.php");
        exit();
    }
}

$firstname = "";
$lastname = "";
$userEmail = "";
$phoneNumber = "";
$userAddress = "";
$postcode = "";
$userState = "";
$userCountry = "";
$userImage = "";


if (isset($_POST['submitPic'])) {

    if (isset($_FILES['userIMG']) && $_FILES['userIMG']['error'] === UPLOAD_ERR_OK) {
        $userIMG = file_get_contents($_FILES['userIMG']['tmp_name']);
    }
    try {
        $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $userName = getUsername();
        $sql = "UPDATE User SET userIMG = :userIMG WHERE userName = :userName";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userIMG', $userIMG, PDO::PARAM_LOB);
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
        $stmt->execute();

        echo "<meta http-equiv='refresh' content='0'>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        echo "<meta http-equiv='refresh' content='0'>";
    }
}

if (isset($_POST['save_profile'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $userEmail = $_POST['userEmail'];
    $phoneNumber = $_POST['phoneNumber'];
    $userAddress = $_POST['userAddress'];
    $postcode = $_POST['postcode'];
    $userState = $_POST['userState'];
    $userCountry = $_POST['userCountry'];
    $userName = getUsername();
    $userIMG = null;



    try {
        $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE User
                SET firstname = :firstname, lastname = :lastname, userEmail = :userEmail, 
                phoneNumber = :phoneNumber, userAddress = :userAddress, postcode = :postcode, 
                userState = :userState, userCountry = :userCountry
                WHERE userName = :userName";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $stmt->bindParam(':userEmail', $userEmail, PDO::PARAM_STR);
        $stmt->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
        $stmt->bindParam(':userAddress', $userAddress, PDO::PARAM_STR);
        $stmt->bindParam(':postcode', $postcode, PDO::PARAM_STR);
        $stmt->bindParam(':userState', $userState, PDO::PARAM_STR);
        $stmt->bindParam(':userCountry', $userCountry, PDO::PARAM_STR);
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);

        $stmt->execute();
        echo "<meta http-equiv='refresh' content='0'>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        echo "<meta http-equiv='refresh' content='0'>";
    }
} else {

    $userName = getUsername();
    try {
        $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT userID, userName, userPassword, userEmail, isAdmin, theme, userIMG, lastname, firstname, userAddress, phoneNumber, postcode, userState, userCountry, isUserArchive FROM User WHERE userName = :userName";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $userEmail = $row['userEmail'];
            $phoneNumber = $row['phoneNumber'];
            $userAddress = $row['userAddress'];
            $postcode = $row['postcode'];
            $userState = $row['userState'];
            $userCountry = $row['userCountry'];
            $userImage = $row['userIMG'];
            $isUserArchive = $row['isUserArchive'];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if (isset($_POST['toggleAccountStatus'])) {
    $userName = getUsername(); // Get the username of the currently logged-in user

    // Update the User table to lock/unlock the account
    $query = "UPDATE User SET isUserArchive = 1 - isUserArchive WHERE userName = :userName";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Destroy the user's session
        session_destroy();

        // Use JavaScript to redirect to login.php
        echo "<script>window.location = 'login.php';</script>";
    } else {
        echo "Error updating user account status: " . $stmt->errorInfo()[2];
    }
    echo "<meta http-equiv='refresh' content='0'>";
}
// Close the database connection
$conn = null;

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/shop.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>

<body>

    <?= templateNavBar() ?>

    <div id="underNavBar" style="background:#061A39; color:white; padding-bottom: 10px; margin-bottom:3.5rem;">
        <h1 class="display-5 text-center">Profile</h1>
        <h2 class="lead text-center">Manage and Update Your Profle</h2>
    </div>

    <div class="container rounded mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <h5 class="text-right">Profile Image</h5>
                    <form name="profileImageForm" action="profile.php" method="post" enctype="multipart/form-data">
                        <div class="d-flex flex-column align-items-center text-center p-3">
                            <?php
                            if (isset($userImage) && !empty($userImage)) {
                                echo '<img class="rounded-circle mt-5" src="data:image/jpeg;base64,' . base64_encode($userImage) . '" alt="Profile Picture" width="200px" height="200px">';
                            } else {
                                echo '<img class="rounded-circle mt-5" src="https://mdbootstrap.com/img/Photos/Others/placeholder-avatar.jpg" alt="Demo Profile" width="200px">';
                            }
                            ?>
                            <span style="font-weight: 500; font-size: 20px;"><?php echo getUsername(); ?></span>
                            <br />
                            <input class="file-upload form-control" type="file" name="userIMG" accept="image/*" />
                            <br />
                            <button class="button btn btn-primary pic" type="submit" name="submitPic">Save Profile Picture</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-5 border-right">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <h5 class="text-center">Information</h5>
                    </div>
                    <form name="profileForm" action="profile.php" method="post">
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="first-name" class="labels">First Name</label>
                                <input type="text" name="firstname" id="first-name" class="form-control" placeholder="First Name" value="<?= $firstname ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="last-name" class="labels">Last Name</label>
                                <input type="text" name="lastname" id="last-name" class="form-control" placeholder="Last Name" value="<?= $lastname ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="email" class="labels">Email</label>
                                <input type="email" name="userEmail" id="email" class="form-control" placeholder="Enter Email" value="<?= $userEmail ?>">
                            </div>
                            <div class="col-md-12"><br>
                                <label for="mobile-number" class="labels">Mobile Number</label>
                                <input type="tel" name="phoneNumber" id="mobile-number" class="form-control" placeholder="Enter Phone Number" value="<?= $phoneNumber ?>">
                            </div>
                            <div class="col-md-12"><br>
                                <label for="address" class="labels">Address</label>
                                <input type="text" name="userAddress" id="address" class="form-control" placeholder="Enter Address" value="<?= $userAddress ?>">
                            </div>
                            <div class="col-md-12"><br>
                                <label for="postcode" class="labels">Postcode</label>
                                <input type="text" name="postcode" id="postcode" class="form-control" placeholder="Enter Postcode" value="<?= $postcode ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="state-region" class="labels">State</label>
                                <input type="text" name="userState" id="state" class="form-control" placeholder="State" value="<?= $userState ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="country" class="labels">Country</label>
                                <input type="text" name="userCountry" id="country" class="form-control" placeholder="Country" value="<?= $userCountry ?>">
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit" name="save_profile">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-3 border-right">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <h5 class="text-right">Optional</h5>
                    </div>
                    <br />
                    <div class="col-md-14">
                        <div class="d-flex justify-content-center align-items-center flex-column">
                            <label for="dark-mode" class="dark-mode">
                                <input type="radio" id="dark-mode" name="mode" value="dark"> Dark Mode
                            </label>
                            <br>
                            <label for="light-mode" class="light-mode">
                                <input type="radio" id="light-mode" name="mode" value="light" checked> Light Mode
                            </label>
                            <br /><br /><br />
          

                <form method="post" action="profile.php">
                                <table>
                                    <tr>
                                        <div class="text-center">
                                            <h5>Account Status: </h5>
                                            <p><?= $isUserArchive ? 'Archived/Locked' : 'Unarchived/Unlocked' ?></p>
                                        </div>

                                    </tr>
                                    <tr>


                                        <input type="hidden" name="userID" value="<?= $userID ?>">
                                        <input type="hidden" name="isUserArchive" value="<?= $isUserArchive ?>">
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#archiveModal" tabindex="3" type="button">
                                            <?= $isUserArchive ? 'Unarchive/Unlock' : 'Archive/Lock' ?> User Account
                                        </button>

                                        <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="archiveModalLabel" style="color: black;">Confirm Archive</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start" style="color: black;">
                                                        <p>Are you sure you want to archive your account?</p>
                                                        <p>Archiving your account <strong>means you'll lose access to your account and you will be logged out.</strong></p>
                                                        <p>To recover your account, you'll need to contact the site admins.</p>

                                                        <form method="post" action="profile.php">
                                                            <input type="hidden" name="userID" value="<?= $userID ?>">
                                                            <input type="hidden" name="archiveAction" value="archive">
                                                            <button class="btn btn-danger archive" type="submit" name="toggleAccountStatus">Continue</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </tr>
                                </table>
                            </form>



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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</body>