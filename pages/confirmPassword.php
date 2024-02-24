<?php
require_once("../scripts/db.php");
require_once("../scripts/functions.php");

$passErr = '';
if (isset($_POST['new_password'])) {
    $userPassword = $_POST['userPassword'];
    $token = $_POST['token']; 
    $email = $_POST['email']; 

    try {
        $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit; 
    }

    $sql = "SELECT email, expiration_time FROM password_reset WHERE token = :token AND email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $row = $stmt->fetch();
        if ($row) {
            $email = $row['email'];
            $expiration_time = $row['expiration_time'];

            if (empty($userPassword)) {
                $passErr = "Please enter the password";
            } else {
                $digitPattern = '/\d/';
                $lowerPattern = '/[a-z]/';
                $upperPattern = '/[A-Z]/';
                $symbolPattern = '/[!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?]/';
            
                if (
                    strlen($userPassword) < 10 ||
                    strlen($userPassword) > 20 ||
                    !preg_match($lowerPattern, $userPassword) ||
                    !preg_match($upperPattern, $userPassword) ||
                    !preg_match($digitPattern, $userPassword) ||
                    !preg_match($symbolPattern, $userPassword)
                ) {
                    $passErr = "Password must be between 10-20 characters and contain at least one lowercase letter, one uppercase letter, one number, and one symbol or special character.";
                }
            }
            
        
            if (empty($passErr)) {
                $hashedPassword = password_hash($userPassword, PASSWORD_BCRYPT);

                $updateSql = "UPDATE User SET userPassword = ? WHERE userEmail = ?";
                $updateStmt = $conn->prepare($updateSql);
    
                if ($updateStmt->execute([$hashedPassword, $email])) {
                    echo "Password updated successfully.";
    
                    $deleteSql = "DELETE FROM password_reset WHERE token = ?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    if ($deleteStmt->execute([$token])) {
                        echo "You can go back to login!";
                    } else {
                        echo "Token deletion failed. Please try again later.";
                    }
                } else {
                    echo "Password update failed.";
                }
            } else {
                echo "Please try again";
            }
        } else {
            echo "Database query failed: " . $stmt->errorInfo()[2];
        }
            }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
</head>
    <title>Confirm Password</title>
</head>
<body>
<?= templateNavBar() ?>

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center">Reset Password</h3>
                        <form action="" method="post" name="newPass" style="margin-top: 30px;">
                            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                            <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password:</label>
                                <input type="password" class="form-control" name="userPassword" required>
                            </div>
                            <span class="text-danger"><?php echo $passErr; ?></span>
                            <div class="text-center">
                                <button type="submit" name="new_password" class="btn btn-primary" style="background-color: #061a39;">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



</body>
</html>
