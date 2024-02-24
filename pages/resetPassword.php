<?php
require_once("../scripts/db.php");
require_once("../scripts/functions.php");

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$server_name = $_SERVER['SERVER_NAME'];
$base_url = $protocol . $server_name . dirname($_SERVER['PHP_SELF']);
$errorUsername = $errorEmail = "";
$resetStatus = "";
if (isset($_POST['reset'])) {
    $usernamee = $_POST['username'];
    $email = $_POST['email'];

    // Validate the username
    if (empty($usernamee)) {
        $errorUsername = 'Username is required.';
    }

    // Validate the email
    if (empty($email)) {
        $errorEmail = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorEmail = 'Invalid email format.';
    }

    // If there are no validation errors, proceed with sending the reset email
    if (empty($errorUsername) && empty($errorEmail)) {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $token = bin2hex(random_bytes(32));
        $to = $email;
        $from = "wearDrippy@drip.com";
        $subject = "Password Reset Request";
        $message = "Hello $usernamee,\n\n";
        $message .= "You have requested to reset your password. To reset your password, click on the following link:\n\n";
        $message .= "$base_url/confirmPassword.php?token=$token&email=$email\n\n";
        $message .= "This link will expire in 1 hour.\n\n";
        $message .= "If you did not request a password reset, please ignore this email.\n\n";
        $message .= "Best regards,\nWear Drip Team. \n\n";
        $message .= "Stay Drippy!";

        $headers = "From: $from" . "\r\n";
        $headers .= "Reply-To: $to" . "\r\n";

        if (mail($to, $subject, $message, $headers)) {
            $sql = "INSERT INTO password_reset (username, token, email, expiration_time) VALUES (:usernamee, :token, :email, :expiration_time)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':usernamee', $usernamee, PDO::PARAM_INT);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':expiration_time', $expiration_time, PDO::PARAM_STR);
            $stmt->execute();
            $resetStatus= "An email with a password reset link has been sent to your email address.";
        } else {
            $resetStatus =  "Email sending failed. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
<?= templateNavBar() ?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center">Password Reset</h3>
                        <form action="resetPassword.php" method="post" name="reset">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" class="form-control" name="username" >
                                <span class="text-danger"><?php echo $errorUsername; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" name="email" >
                                <span class="text-danger"><?php echo $errorEmail; ?></span>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="reset" class="btn btn-primary" style="background-color: #061a39;">Reset Password</button>
                                <div><i class="fa-solid fa-circle-exclamation" style="color: #bf2222;"></i> Please make sure that the Username and Email you enter match the information you provided during registration.</div>
                                <br>
                                <div style="color: green;"><i class="fa-solid fa-circle-check" style="color: #11ff00;"></i> <?php echo $resetStatus; ?></div>
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
