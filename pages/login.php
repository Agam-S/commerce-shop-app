<?php
session_start();
require_once("../scripts/db.php");
require_once("../scripts/verifyUsers.php");
require_once("../scripts/functions.php");
verify();

$nameErr = $emailErr = $passErr = $passConErr = '';
$emailSErr = $passSErr = '';
$succ = '';
$succ1 = '';
$captchaErr = '';

if (isset($_POST['contactForm'])) {
    $userName = $_POST['userName'];
    $userEmail = $_POST['userEmail'];
    $userPassword = $_POST['userPassword'];
    $passwordConfirm = $_POST['passwordConfirm'];
    if (isset($_POST['cap'])) {
        $captcha = $_POST['cap'];
    } else {
        $captcha = '';
    }

    if (empty($userName)) {
        $nameErr = "Please enter your name";
    } else {
        $regex = '/^[a-zA-Z0-9_\-\s]+$/';
        if (!preg_match($regex, $userName)) {
            $nameErr = "Please enter a valid name";
        }
    }

    if (empty($userEmail)) {
        $emailErr = "Please enter your email address";
    } else {
        $regex = '/^\S+@\S+\.\S+$/';
        if (!preg_match($regex, $userEmail)) {
            $emailErr = "Please enter a valid email address";
        }
    }

    if (empty($userPassword)) {
        $passErr = "Please enter the password";
    } else {
        $digitPattern = '/\d/';
        $lowerPattern = '/[a-z]/';
        $upperPattern = '/[A-Z]/';
        $symbolPattern = '/[!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?]/';

        if (
            strlen($userPassword) < 10 || strlen($userPassword) > 20 ||
            !preg_match($lowerPattern, $userPassword) ||
            !preg_match($upperPattern, $userPassword) ||
            !preg_match($digitPattern, $userPassword) ||
            !preg_match($symbolPattern, $userPassword)
        ) {
            $passErr = "Password must be between 10-20 characters, contain at least one lower and upper case letter, one number, and one special character (e.g.!@#$).";
        }
    }


    if (empty($passwordConfirm)) {
        $passConErr = "Please confirm your password";
    } elseif ($passwordConfirm !== $userPassword) {
        $passConErr = "Password does not match. Please ensure it matches with the password.";
    }

    if (empty($captcha)) { // Check if captcha field is empty
        $captchaErr = "Please fill in the captcha field";
    }

    if (empty($nameErr) && empty($emailErr) && empty($passErr) && empty($passConErr) && empty($captchaErr)) { // Check if all fields are filled
        // Hash the password
        $hashedPassword = password_hash($userPassword, PASSWORD_BCRYPT);

        try {
            $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insert user data into database
            $sql = "INSERT INTO User (userName, userEmail, userPassword, isAdmin) VALUES (?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userName, $userEmail, $hashedPassword]);

            header("Location: login.php");
            exit();
        } catch (PDOException $e) {

            $succ1 =  "Error: " . $e->getMessage();

            echo '<script>';
            echo 'document.addEventListener("DOMContentLoaded", function() {';
            echo '    function handleErrors() {';
            echo '       const container = document.querySelector(".container");';
            echo '       container.classList.toggle("active");';
            echo '    }';
            echo '    handleErrors();';
            echo '});';
            echo '</script>';
        }
    } else {

        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function() {';
        echo '    function handleErrors() {';
        echo '       const container = document.querySelector(".container");';
        echo '       container.classList.toggle("active");';
        echo '    }';
        echo '    handleErrors();';
        echo '});';
        echo '</script>';
    }
}

if (isset($_POST['signVald'])) {
    $emailS = $_POST['emailS'];
    $passwordS = $_POST['passwordS'];
    $emailSErr = $passSErr = "";

    if (empty($emailS)) {
        $emailSErr = "Please enter your email address";
    } else {
        $regex = '/^\S+@\S+\.\S+$/';
        if (!preg_match($regex, $emailS)) {
            $emailSErr = "Please enter a valid email address";
        }
    }

    $digitPattern = '/\d/';
    $lowerPattern = '/[a-z]/';
    $upperPattern = '/[A-Z]/';
    if (empty($passwordS)) {
        $passSErr = "Please enter the password";
    } else {
        if (strlen($passwordS) < 10 || strlen($passwordS) > 20 || !preg_match($lowerPattern, $passwordS) || !preg_match($upperPattern, $passwordS) || !preg_match($digitPattern, $passwordS)) {
            $passSErr = "Password must be between 10-20 characters and contain at least one lowercase letter, one uppercase letter, and one number.";
        }
    }

    if (empty($emailSErr) && empty($passSErr)) {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=db", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT userName, userPassword, isUserArchive FROM User WHERE userEmail = ?");
            $stmt->execute([$emailS]);
            $row = $stmt->fetch();

            //check if the user's account is unlocked
            if ($row && $row['isUserArchive'] == 0 && password_verify($passwordS, $row['userPassword'])) {
                $hashedUsername = hash('sha256', $row['userName'] . 10);
                $_SESSION["username"] = $row['userName'];
                setcookie('UID', $hashedUsername, time() + 3600, '/');
                header("Location: home.php");
                exit();
            } elseif ($row && $row['isUserArchive'] == 1) {
                $succ = 'Your account is locked. Please <a href="home.php#contact-form">contact the administrator</a>.';
            } else {
                $succ = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $succ = "Error: " . $e->getMessage();
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../assets/images/Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/18f68d3a93.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
    <?= templateNavBar() ?>

    <section>
        <div class="container">
            <div class="user signinBx">
                <div class="imgBx"><img src="../assets/images/Login photo.png" class="mx-auto d-block" alt="" /></div>
                <div class="formBx">
                    <form action="login.php" id="login" class="input-field" name="signVald" method="post">
                        <h2>Sign In</h2>
                        <label for="emailS" class="w-100"><input type="text" id="emailS" name="emailS" placeholder="Email" /></label>
                        <div class="error" style="color: red" aria-label="<?php echo $emailSErr; ?>"><?php echo $emailSErr; ?></div>
                        <label for="passwordS"><input type="password" id="passwordS" name="passwordS" placeholder="Password" id="lpassword" />
                            <div class="error" style="color: red" aria-label="<?php echo $passSErr; ?>"><?php echo $passSErr; ?></div>
                            <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                            <span class="eye-icon" onclick="lpass();">
                                <i class="fa fa-eye"></i>
                            </span>
                            <br />
                            <div class="error" style="color: red"><?php echo $succ; ?></div>
                            <input type="submit" name="signVald" value="Login" />
                            <p class="signup">
                                Forgot Password ?
                                <a href="resetPassword.php">Reset Password</a>
                            </p>
                            <p class="signup">
                                Don't have an account ?
                                <a href="#" onclick="toggleForm();">Sign Up.</a>
                            </p>
                    </form>
                </div>
            </div>
            <div class="user signupBx">
                <div class="formBx">
                    <form action="login.php" id="signup" id="up" class="input-field" name="contactForm" method="post" onsubmit="return validateForm()" novalidate>
                        <h2>Create an account</h2>

                        <input type="text" name="userName" placeholder="Username" />
                        <span class="error" style="color: red"><?php echo $nameErr; ?></span>
                        <input type="email" name="userEmail" placeholder="Email Address" />
                        <span class="error" style="color: red"><?php echo $emailErr; ?></span>
                        <input type="password" name="userPassword" placeholder="Create Password" id="password" />
                        <span class="error" style="color: red"><?php echo $passErr; ?></span>
                        <input type="password" name="passwordConfirm" placeholder="Confirm Password" id="cpassword" />
                        <span class="error" style="color: red"><?php echo $passConErr; ?></span>
                        <span class="eye-icon" onclick="pass();">
                            <i class="fa fa-eye"></i>
                        </span>
                        <div class="captcha-container">
                            <div class="captcha-text" id="captchaText"></div>
                            <img id="captcha-image" src="../assets/images/captcha.png" alt="CAPTCHA Image">
                            <div id="new"><i class="fa-solid fa-rotate-right" id="reloadBtn" tabindex="0"></i></div>
                        </div>
                        <input type="text" class="captcha-input" placeholder="Enter CAPTCHA" id="captchaInput" name="cap" aria-label="Enter the Captcha">
                        <div class="error" id="captchaError"></div>
                        <span class="error" style="color: red"><?php echo $captchaErr; ?></span>
                        <br />
                        <div class="error"><?php echo $succ1; ?></div>
                        <input type="submit" name="contactForm" value="Sign Up" />
                        <p class="signup">
                            Already have an account ?
                            <a href="#" onclick="toggleForm();">Sign in.</a>
                        </p>

                    </form>
                </div>
                <div class="imgBx"><img src="../assets/images/Login photo.png" class="img-fluid" alt="" /></div>
            </div>
        </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="../assets/js/login.js"></script>
    <script src="../assets/js/regScript.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>