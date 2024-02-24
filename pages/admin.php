<?php
require_once("../scripts/db.php");
require_once("../scripts/verifyUsers.php");
require_once("../scripts/functions.php");

$uName = getUsername();
$customSalt = 10;
$currentUsername = hash('sha256', $uName . $customSalt);

if ($currentUsername && isset($_COOKIE['UID'])) {
    if ($_COOKIE['UID'] !== $currentUsername) {
        header("Location: home.php");
        exit();
    }
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM User WHERE userName = :uName");
    $stmt->bindParam(':uName', $uName, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch();

    $uStatus = $result['isAdmin'];

    if ($uStatus == 0) {
        header("Location: home.php");
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Fetch data from the "Thread" table
try {
    $stmt = $conn->prepare("SELECT threadID, threadTitle, threadIMG, threadDesc, isArchive FROM Thread");
    $stmt->execute();

    // Fetch data as an associative array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
//fetch data form Comment table
try {
    $stmt = $conn->prepare("SELECT commentID, commentBody, dateCreated, threadID, userID, isArchive FROM Comment");
    $stmt->execute();

    // Fetch data as an associative array
    $resultCom = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    $stmt = $conn->prepare("SELECT userID, userName, userEmail, isAdmin, isUserArchive FROM User");
    $stmt->execute();

    // Fetch data as an associative array
    $resultUser = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate and process the form data
        $productName = $_POST['productName'];
        $productDesc = $_POST['productDesc'];
        $productPrice = $_POST['productPrice'];
        $productStock = $_POST['productStock'];
        $size = $_POST['size'];

        // Handle image upload
        $imageData = null;

        if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
            $imageData = file_get_contents($_FILES['productImage']['tmp_name']);

            // Now, insert the product details into the database
            $query = "INSERT INTO Products (productImage, productName, productDesc, productPrice, productStock, size) 
                      VALUES (:imageData, :productName, :productDesc, :productPrice, :productStock, :size)";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':imageData', $imageData, PDO::PARAM_LOB);
            $stmt->bindParam(':productName', $productName);
            $stmt->bindParam(':productDesc', $productDesc);
            $stmt->bindParam(':productPrice', $productPrice);
            $stmt->bindParam(':productStock', $productStock);
            $stmt->bindParam(':size', $size);

            if ($stmt->execute()) {
                echo "Product added successfully!";
            } else {
                echo "Error adding the product.";
            }
        } else {
            echo "Image upload failed.";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    $stmt = $conn->prepare("SELECT * FROM Feedback");
    $stmt->execute();

    // Fetch data as an associative array
    $resultFeed = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if (isset($_POST['updateThread'])) {
    $threadIDd = $_POST['threadID'];
    $query = "UPDATE Thread SET isArchive = 1 - isArchive WHERE threadID = :threadID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':threadID', $threadIDd, PDO::PARAM_INT);
    $stmt->execute();

    echo "<meta http-equiv='refresh' content='0'>";
}

if (isset($_POST['archiveComment'])) {
    $commentID = $_POST['commentID'];
    $isArchive = $_POST['isArchive'];

    // Update the Comment table to archive or unarchive the comment
    $query = "UPDATE Comment SET isArchive = 1 - isArchive WHERE commentID = :commentID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':commentID', $commentID, PDO::PARAM_INT);
    $stmt->execute();

    echo "<meta http-equiv='refresh' content='0'>";
}


if (isset($_POST['updateAdmin'])) {
    $userIDd = $_POST['userID'];
    $query = "UPDATE User SET isAdmin = 1 - isAdmin WHERE userID = :userID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userID', $userIDd, PDO::PARAM_INT);
    $stmt->execute();

    echo "<meta http-equiv='refresh' content='0'>";
}

//locl/unlock
if (isset($_POST['toggleAccountStatus'])) {
    $userID = $_POST['userID'];
    $isUserArchive = $_POST['isUserArchive'];

    // Toggle the account status
    $isUserArchive = $isUserArchive ? 0 : 1;

    // Update the User table to lock/unlock the account
    $query = "UPDATE User SET isUserArchive = :isUserArchive WHERE userID = :userID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':isUserArchive', $isUserArchive, PDO::PARAM_INT);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();

    echo "<meta http-equiv='refresh' content='0'>";
}

//showing products
try {
    $stmt = $conn->prepare("SELECT * FROM Products");
    $stmt->execute();

    // Fetch data as an associative array
    $resultProduct = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


//deleting products
if (isset($_POST['deleteProduct'])) {
    $productID = $_POST['productID'];
    $query = "DELETE FROM Products WHERE productID = :productID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
    $stmt->execute();

    echo "<meta http-equiv='refresh' content='0'>";
}

$conn = null;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/home.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <head>

    <body>
        <?= templateNavBar() ?>

        <div class="container mt-2">
            <div class="d-flex justify-content-center">
                <button class="btn btn-primary btn-header m-2" onclick="showSection('thread-section');">Manage Threads</button>
                <button class="btn btn-primary btn-header m-2" onclick="showSection('comment-section');">Manage Comments/posts</button>
                <button class="btn btn-primary btn-header m-2" onclick="showSection('user-section');">Manage User</button>
                <button class="btn btn-primary  btn-header m-2" onclick="showSection('manage-product-section');">Manage Product</button>
                <button class="btn btn-primary btn-header m-2" onclick="showSection('product-section');">Add Prodcuts</button>
                <button class="btn btn-primary btn-header m-2" onclick="showSection('feedback-section');">Manage Questions/Feedback</button>
            </div>
        </div>

        <!-- Display the table below -->
        <div class="container mt-4" id="thread-section">
            <h2>Thread Data</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Thread Title</th>
                        <th>Image</th>
                        <th>Thread Details</th>
                        <th>Archive</th>
                        <th>Action</th> <!-- New column for the Archive button -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($result as $row) {
                        $threadID = $row['threadID'];
                        $threadTitle = $row['threadTitle'];
                        $threadIMG = $row['threadIMG'];
                        $threadDesc = $row['threadDesc'];
                        $isArchive = $row['isArchive'] ? 'Yes' : 'No';
                        $archiveAction = $row['isArchive'] ? 'Unarchive' : 'Archive'; // Determine button action text

                        echo "<tr>
                <td>$threadTitle</td>
                <td>";

                        if ($threadIMG) {
                            echo "<img class='img' src='data:image/jpeg;base64," . base64_encode($threadIMG) . "' alt='Thread Image' style='width: 100px; height: 100px;'>";
                        }

                        echo "</td>
                <td>$threadDesc</td>
                <td>$isArchive</td>
                <td>
                    <form method='post' name='updateThread' action='admin.php'>
                        <input type='hidden' name='threadID' value='$threadID'>
                        <input type='submit' name='updateThread' value='$archiveAction' class='btn btn-primary archive-btn'>
                    </form>
                </td>
                </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <div class="container mt-4" id="comment-section" style="display: none;">
            <h2>Manage Individual Posts/Comments</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Comment Body</th>
                        <th>Date Created</th>
                        <th>User ID</th>
                        <th>Archive Info</th>
                        <th>Archive</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($resultCom as $row) {
                        $commentID = $row['commentID'];
                        $commentBody = $row['commentBody'];
                        $dateCreated = $row['dateCreated'];
                        $userID = $row['userID'];
                        $isArchive = $row['isArchive'];
                        $archiveInfo = $isArchive ? 'Yes' : 'No';
                        $archiveAction = $isArchive ? 'Unarchive' : 'Archive'; // Determine button text
                    ?>

                        <tr>
                            <td><?= $commentBody ?></td>
                            <td><?= $dateCreated ?></td>
                            <td><?= $userID ?></td>
                            <td><?= $archiveInfo ?></td>
                            <td>
                                <form method="post" action="admin.php">
                                    <input type="hidden" name="commentID" value="<?= $commentID ?>">
                                    <input type="hidden" name="isArchive" value="<?= $isArchive ?>">
                                    <input type="submit" name="archiveComment" value="<?= $archiveAction ?>" class="btn btn-primary archive-btn">
                                </form>
                            </td>
                        </tr>

                    <?php
                    }
                    ?>
                </tbody>
            </table>



        </div>






        <div class="container mt-4" style="display: none;" id="user-section">
            <h2>User Data</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>User Email</th>
                        <th>Admin</th>
                        <th>Make Admin</th>
                        <th>Account Status</th>
                        <th>Lock/Unlock User Account</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($resultUser as $row) {
                        $userID = $row['userID'];
                        $userName = $row['userName'];
                        $userEmail = $row['userEmail'];
                        $isAdmin = $row['isAdmin'] ? 'Yes' : 'No';
                        $isUserArchive = $row['isUserArchive'];
                    ?>
                        <tr>
                            <td><?= $userName ?></td>
                            <td><?= $userEmail ?></td>
                            <td><?= $isAdmin ?></td>
                            <td>
                                <form method="post" action="admin.php">
                                    <input type="hidden" name="userID" value="<?= $userID ?>">
                                    <button type="submit" name="updateAdmin" class="btn btn-primary">
                                        Make/Remove Admin
                                    </button>
                                </form>
                            </td>
                            <td><?= $isUserArchive ? 'Locked' : 'Unlocked' ?></td>
                            <td>
                                <form method="post" action="admin.php">
                                    <input type="hidden" name="userID" value="<?= $userID ?>">
                                    <input type="hidden" name="isUserArchive" value="<?= $isUserArchive ?>">
                                    <button type="submit" name="toggleAccountStatus" class="btn btn-danger">
                                        <?= $isUserArchive ? 'Unlock' : 'Lock' ?> User Account
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

        </div>

        <div class=" container mt-4" style="display:none;" id="manage-product-section">
            <h2>Manage Products</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Delete</th>
                        <!-- <th>Archive</th> -->
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($resultProduct as $row) : ?>
                        <?php
                        $productImage = $row['productImage'];
                        $productName = $row['productName'];
                        $productID = $row['productID'];

                        ?>

                        <?php
                        if (strlen($row['productImage']) < 80) {
                            $imagePath = ($row['productImage']);
                        } else {
                            $imagePath = 'data:image/jpeg;base64,' . base64_encode($row['productImage']);
                        }
                        ?>

                        <tr>
                            <td>
                                <img src="<?= $imagePath ?>" alt="<?= $row['productName'] ?>" class="product-image" style='width: 100px; height: 100px;'>
                            </td>
                            <td><?= $productName ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger mx-4 my-2" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $productID ?>" tabindex="3">Delete</button>
                                <div class="modal fade" id="deleteModal<?= $productID ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel" style="color:black;">Delete Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start" style="color: black;">
                                                <p>Are you sure you want to delete this product?</p>
                                                <p>Deleting this product will remove it from the database and cannot be undone.</p>

                                                <form method="post" action="admin.php">
                                                    <input type="hidden" name="productID" value="<?= $productID ?>">
                                                    <button type="submit" name="deleteProduct" class="btn btn-danger">
                                                        Delete Product
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>


        <div class="container mt-4" style="display: none;" id="product-section">
            <h2>Add a New Product</h2>
            <form action="admin.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="productImage" class="form-label">Product Image:</label>
                    <input type="file" class="form-control" name="productImage" id="productImage" required>
                </div>

                <div class="mb-3">
                    <label for="productName" class="form-label">Product Name:</label>
                    <input type="text" class="form-control" name="productName" id="productName" required>
                </div>

                <div class="mb-3">
                    <label for="productDesc" class="form-label">Product Description:</label>
                    <textarea class="form-control" name="productDesc" id="productDesc" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="productPrice" class="form-label">Product Price:</label>
                    <input type="number" class="form-control" name="productPrice" id="productPrice" required>
                </div>

                <div class="mb-3">
                    <label for="productStock" class="form-label">Product Stock:</label>
                    <input type="number" class="form-control" name="productStock" id="productStock" required>
                </div>

                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>

        <div class="container mt-4" style="display: none;" id="feedback-section">
            <h2>Questions/Feedback</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Asker's Name</th>
                        <th>Asker's Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($resultFeed as $row) {
                        $feedbackQues = $row['feedbackQues'];
                        $feedbackName = $row['feedbackName'];
                        $feedbackEmail = $row['feedbackEmail'];

                        echo "<tr>
                        <td>$feedbackQues</td>
                        <td>$feedbackName</td>
                        <td><label><a href='mailto:$feedbackEmail'>$feedbackEmail</a></label></td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
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
        <script src="../assets/js/admin.js"></script>
        <script src="../assets/js/script.js"></script>
    </body>

</html>