<?php
require_once("../scripts/db.php");
require_once("../scripts/verifyUsers.php");
require_once("../scripts/functions.php");

// $ch = curl_init();
// $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
// curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
// curl_setopt($ch, CURLOPT_URL, "https://newsapi.org/v2/everything?apiKey=ee4bfe68aaa34a8aaf6e439622b58ffb&language=en&q=fashion");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// $output = curl_exec($ch);

// // convert response
// $result = json_decode($output, true);

// // handle error; error output
// if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {

//     var_dump($output);
// }

// curl_close($ch);
// // $json_data = file_get_contents("");
// // $result  = json_decode($json_data, true);

// $rand = $result['articles'][\array_rand($result['articles'])];


// $f_title = $rand['title'];
// $f_desc = $rand['description'];
// $f_link = $rand['url'];


try {
    $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
try {
    $stmt = $conn->prepare("SELECT * FROM Thread INNER JOIN User on Thread.userID = User.userID WHERE isArchive = 0 ORDER BY threadID DESC ");
    $stmt->execute();

    $result = $stmt->fetchAll();

    if (isset($_SESSION['username'])) {
        $uName = $_SESSION['username'];
        $customSalt = 10;

        $currentUsername = hash('sha256', $uName . $customSalt);

        if ($currentUsername && isset($_COOKIE['UID'])) {
            if ($_COOKIE['UID'] !== $currentUsername) {
                header("Location: login.php");
                exit();
            }
        }

        $uName = '"' . $uName . '"';

        $stmt = $conn->prepare("SELECT * FROM User WHERE userName = " . $uName);
        $stmt->execute();
        $results = $stmt->fetch();
        $rName = $results['userID'];

        $likedThreads = [];

        foreach ($result as $row) {
            $tID = $row['threadID'];
            $checkQuery = "SELECT * FROM LikedThreads WHERE userID = :userID AND threadID = :threadID";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(':userID', $rName, PDO::PARAM_INT);
            $checkStmt->bindParam(':threadID', $tID, PDO::PARAM_INT);
            $checkStmt->execute();
            $existingLike = $checkStmt->fetch();
            $likedThreads[$tID] = ($existingLike !== false);
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$sortOption = "recent";
$searchKeyword = "";

if (isset($_GET['sort'])) {
    $sortOption = $_GET['sort'];
}

if (isset($_GET['search'])) {
    $searchKeyword = $_GET['search'];
}

if (!empty($searchKeyword)) {
    // If a search keyword is provided, filter by title or content body
    $searchParam = "%$searchKeyword%"; // Create a separate variable
    $stmt = $conn->prepare("SELECT * FROM Thread INNER JOIN User ON Thread.userID = User.userID WHERE isArchive = 0 AND (threadTitle LIKE :keyword OR threadDesc LIKE :keyword) ORDER BY threadID DESC");
    $stmt->bindParam(':keyword', $searchParam);
} else {
    // If no search keyword is provided, use the selected sorting option
    if ($sortOption === "liked") {
        $stmt = $conn->prepare("SELECT * FROM Thread INNER JOIN User ON Thread.userID = User.userID WHERE isArchive = 0 ORDER BY likeCount DESC, threadID DESC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM Thread INNER JOIN User ON Thread.userID = User.userID WHERE isArchive = 0 ORDER BY threadID DESC");
    }
}

$stmt->execute();
$result = $stmt->fetchAll();




$title = "";
$desc = "";

if (isset($_POST['ok'])) {

    $uName = getUsername();
    $customSalt = 10;

    $currentUsername = hash('sha256', $uName . $customSalt);

    if ($currentUsername && isset($_COOKIE['UID'])) {
        if ($_COOKIE['UID'] !== $currentUsername) {
            header("Location: login.php");
            exit();
        }
    }


    $title = $_POST['title'];
    $desc = $_POST['desc'];
    $uName = '"' . $uName . '"';

    $imageData = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    }

    try {
        $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM User WHERE userName = " . $uName);
        $stmt->execute();
        $result = $stmt->fetch();
        $rName = $result['userID'];

        $timestamp = date('Y-m-d H:i:s');
        $sql = "INSERT INTO Thread (threadTitle, threadDesc, dateCreated, threadIMG, isArchive, userID)
        VALUES (:title, :desc, :timestamp, :imageData, false, :rName)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':desc', $desc, PDO::PARAM_STR);
        $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR); // Bind the timestamp variable
        $stmt->bindParam(':imageData', $imageData, PDO::PARAM_LOB);
        $stmt->bindParam(':rName', $rName, PDO::PARAM_INT);
        $stmt->execute();

        $conn->exec($sql);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
    echo "<meta http-equiv='refresh' content='0'>";
}

function getCurrentUserID()
{
    if (isset($_SESSION['username'])) {
        $userName = $_SESSION['username'];
        global $conn;
        $stmt = $conn->prepare("SELECT userID FROM User WHERE userName = '$userName'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['userID'];
    } else {
        return null;
        exit();
    }
}

function getPostUserID($threadID)
{
    global $conn;
    $stmt = $conn->prepare("SELECT userID FROM Thread WHERE threadID = $threadID");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['userID'];
}


if (isset($_POST['delete'])) {
    $threadID = (int)$_POST['threadID'];

    $query = "UPDATE Thread SET isArchive = 1 WHERE threadID = $threadID";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    echo "<meta http-equiv='refresh' content='0'>";
}


if (isset($_POST['postLike'])) {
    $tID = $_POST['likeId'];

    $uName = getUsername();
    $customSalt = 10;

    $currentUsername = hash('sha256', $uName . $customSalt);

    if ($currentUsername && isset($_COOKIE['UID'])) {
        if ($_COOKIE['UID'] !== $currentUsername) {
            header("Location: login.php");
            exit();
        }
    }

    $uName = '"' . $uName . '"';

    $stmt = $conn->prepare("SELECT * FROM User WHERE userName = " . $uName);
    $stmt->execute();
    $result = $stmt->fetch();
    $rName = $result['userID'];

    $checkQuerys = "SELECT userID FROM LikedThreads WHERE userID = :userID AND threadID = :threadID";
    $checkStmts = $conn->prepare($checkQuerys);
    $checkStmts->bindParam(':userID', $rName, PDO::PARAM_INT);
    $checkStmts->bindParam(':threadID', $tID, PDO::PARAM_INT);
    $checkStmts->execute();

    $existingLike = $checkStmts->fetch();

    if (!$existingLike) {
        $insertQuery = "INSERT INTO LikedThreads (userID, threadID) VALUES (:userID, :threadID)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':userID', $rName, PDO::PARAM_INT);
        $insertStmt->bindParam(':threadID', $tID, PDO::PARAM_INT);
        $insertStmt->execute();

        $updateQuery = "UPDATE Thread SET likeCount = likeCount + 1 WHERE threadID = :threadID";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':threadID', $tID, PDO::PARAM_INT);
        $updateStmt->execute();
    } else {
        $deleteQuery = "DELETE FROM LikedThreads WHERE userID = :userID AND threadID = :threadID";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':userID', $rName, PDO::PARAM_INT);
        $deleteStmt->bindParam(':threadID', $tID, PDO::PARAM_INT);
        $deleteStmt->execute();

        $updateQuery = "UPDATE Thread SET likeCount = likeCount - 1 WHERE threadID = :threadID";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':threadID', $tID, PDO::PARAM_INT);
        $updateStmt->execute();
    }
    echo "<meta http-equiv='refresh' content='0'>";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Threads</title>
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
        <h1 class="display-5 text-center">Discussions</h1>
        <h2 class="lead text-center">Join our Deluxe Society and be part of a thriving fashion community.</h2>
    </div>
    <br>

    <!-- <div class="fact-box card" style="padding:20px;margin-bottom:10px; margin-left:15px;">
        <h5>Random Fact!</h5>
        <h6 class="text-title"><?= $f_title ?></h6>
        <p class="text-body"><?= $f_desc ?></p>
        <a class="text-body">Link: <?= $f_link ?></a>

    </div> -->

    <div class="container-fluid">
        <div class="row align-items-start w-100 mx-auto">
            <div class="col-12 col-md-3 col-lg-2 text-center" style="min-width: 120px;">
                <!-- Sidebar -->
                <h3 class="mx-auto">Sort By:</h3>
                <div class="card-body">
                    <div class="form-check align">
                        <a href="thread.php" class="my-2 mx-auto sortButton">Recent</a>
                    </div>
                    <div class="form-check">
                        <a href="thread.php?sort=liked" class="my-2 mx-auto sortButton">Most Liked</a>
                    </div>
                    <h3>Search:</h3>
                    <div class="form-check mb-5">
                        <form action="thread.php" method="GET">
                            <input type="text" name="search" placeholder="Search Title or Comment" class="my-2 mx-auto text-center" style="border-radius:10px; min-height:43px; min-width:200px">
                            <button type="submit" class="mx-auto sortButton">Search</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
                <!-- main card -->

                <div class="main-card mb-3">
                    <div class="card-body p-2 p-sm-3">
                        <div class="media forum-item">
                            <form name="form" action="" method="post" enctype="multipart/form-data">
                                <div class="main-media-body">
                                    <input required class="main-text-title" name="title" id="title" placeholder="Discussion Title">
                                    <input required class="main-text" name="desc" id="desc" placeholder="write post....">

                                </div>

                                <div class="left-icons">
                                    <label for="image" class="upload-label">
                                        <i class="fa-solid fa-image" style="color: #e2bd55; margin-right: 10px;"></i>
                                        Select an image to upload
                                    </label>
                                    <div class="custom-file-input">
                                        <!-- <input type="file" name="image" id="image" class="file-input"> -->
                                        <input type="file" class="form-control" name="image" id="image">
                                    </div>
                                </div>

                                <div class="icons text-center align-self-center my-3">
                                    <button class="button" type="submit" name="ok">Start Discussion</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

                <!-- cards -->
                <?php foreach ($result as $row) : ?>

                    <div class="card mb-2">

                        <div class="card-body p-2 p-sm-3">
                            <div class="media forum-item">

                                <div class="user">
                                    <a>
                                        <?php
                                        if (!empty($row["userIMG"])) {
                                            echo '<img class="mr-3 rounded-circle" src="data:image/jpeg;base64,' . base64_encode($row["userIMG"]) . '" alt="Profile Picture" width="50px">';
                                        } else {
                                            echo '<img class="mr-3 rounded-circle" src="https://mdbootstrap.com/img/Photos/Others/placeholder-avatar.jpg" alt="Demo Profile" width="50px">';
                                        }
                                        ?>
                                        <?php echo $row['userName'] ?></a>
                                </div>
                                <br />

                                <a href="threadOpen.php?id=<?php echo $row['threadID']; ?>" style="text-decoration:none;">
                                    <div class="media-body">
                                        <?php if ($row['threadIMG']) : ?>
                                            <img class="img" src="data:image/jpeg;base64,<?= base64_encode($row['threadIMG']); ?>" alt="Thread Image">
                                        <?php endif; ?>
                                        <h4 class="text-body"><?php echo $row['threadTitle']; ?></h4>
                                        <p class="text-body"><?php echo $row['threadDesc']; ?></p>
                                        <p style="color: #8c929c;"><?php echo $row['dateCreated']; ?></p>
                                    </div>
                                </a>
                                <!-- icons -->
                                <div class="icons text-center align-self-center">
                                    
                                    <form method="post">
                                        <input type="hidden" name="likeId" value="<?php echo $row['threadID']; ?>">
                                        <button type="submit" name="postLike" style="background:none; border:none;">
                                            <?php if (isset($likedThreads[$row['threadID']]) && $likedThreads[$row['threadID']]) { ?>
                                                <i class="fa-solid fa-thumbs-up" style="color: #2865cc;"></i>
                                            <?php } else { ?>
                                                <i class="fa-solid fa-thumbs-up" style="color: #a2abba;"></i>
                                            <?php } ?>
                                            <?php echo $row['likeCount']; ?>

                                        </button>
                                    </form>

                                    <?php
                                    $currentUserName = getCurrentUserID();
                                    $postUserID = getPostUserID($row['threadID']);
                                    ?>

                                    <?php if ($currentUserName === $postUserID) : ?>
                                        <form method="post">
                                            <input type="hidden" name="threadID" value="<?php echo $row['threadID']; ?>">
                                            <button type="submit" name="delete" class="btn btn-white border-secondary bg-white btn-md" style="border:0px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

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