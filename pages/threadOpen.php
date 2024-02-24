<?php
$id = 0;
if (isset($_GET['id'])) {
    $id = ($_GET['id']);
} else {
    echo "error";
}

require_once("../scripts/db.php");
require_once("../scripts/verifyUsers.php");
require_once("../scripts/functions.php");

try {
    $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
try {
    $stmt = $conn->prepare("SELECT * FROM Thread INNER JOIN User on Thread.userID = User.userID WHERE threadID = " . $id);
    $stmt->execute();

    $result = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    $stmt2 = $conn->prepare("SELECT commentBody, userName, dateCreated FROM Comment INNER JOIN User ON Comment.userID = User.userID WHERE threadID = :id AND Comment.isArchive = 0 ORDER BY commentID DESC");
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $resultComment = $stmt2->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
//implementing delete icon on threadOpen.php
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

    header('Location: thread.php');
    exit;
}


$comment = "";

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


    $comment = $_POST['comment'];
    $comment = '"' . $comment . '"';
    $uName = '"' . $uName . '"';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM User WHERE userName = " . $uName);
        $stmt->execute();
        $result = $stmt->fetch();
        $rName = $result['userID'];

        $sql = "INSERT INTO Comment (commentBody, userID, threadID)
        VALUES ($comment, $rName, $id)";
        $conn->exec($sql);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
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
    <link rel="stylesheet" href="../assets/css/threadOpen.css">

</head>

<body>
    <?= templateNavBar() ?>

    <div id="underNavBar" style="background:#061A39; color:white; padding-bottom: 10px; margin-bottom:3.5rem;">
        <h1 class="display-5 text-center">Discussions</h1>
        <h2 class="lead text-center">Join our Deluxe Society and be part of a thriving fashion community.</h2>
    </div>
    <br>

    <?php foreach ($result as $row) : ?>
        <div class="card large text-center align-self-center">
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

                    <div class="media-body">
                        <?php if ($row['threadIMG']) : ?>
                            <img class="img" src="data:image/jpeg;base64,<?= base64_encode($row['threadIMG']) ?>" alt="Thread Image">
                        <?php endif; ?>
                        <h4 class="text-body"><?= $row['threadTitle'] ?></h4>
                        <p class="text">
                            <?= $row['threadDesc'] ?></p>
                        <h6 style="color: #8c929c;"><?= $row['dateCreated'] ?> </h6> <br>

                    </div>
                    <!-- icons -->
                    <div class="icons text-center align-self-center">

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

    <br>
    <br>
    <h6 class="text-center h6">Comments</h6>

    <div class="comment-card">
        <form name="form" action="" method="post">
            <div class="media-body">
                <input required class="text-comment" name="comment" id="comment" placeholder="write comment...." />
            </div>
            <div class="icons text-center align-self-center">
                <i class="fa-solid fa-message" style="color: #e2bd55;">
                    <button name="ok" class="icon-p">Post</button>
                </i>
            </div>
        </form>
    </div>
    </div>

    <div class="comment-container">
        <?php foreach ($resultComment as $row) : ?>
            <div class="comment-card">
                <div class="media forum-item">

                    <div class="user">
                        <a><img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png" class="mr-3 rounded-circle" width="50" alt="User" /><?= $row['userName'] ?></a>
                    </div>

                    <div class="media-body">
                        <p class="text"><?= $row['commentBody'] ?></p>
                        <h6 style="color: #8c929c;"><?= $row['dateCreated'] ?> </h6>
                    </div>

                    <!-- icons -->
                    <div class="icons text-center align-self-center">
                        <a href="#comment" style="text-decoration:none;">
                            <i class="fa-solid fa-reply" style="color: #2865cc;"></i>
                        </a>
                        <i class="fa-solid fa-thumbs-up" style="color: #2865cc;"></i>
                    </div>

                </div>
            </div>

        <?php endforeach; ?>





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