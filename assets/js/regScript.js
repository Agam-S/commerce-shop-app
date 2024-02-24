var log = document.getElementById("login");
var reg = document.getElementById("signup");
var tog = document.getElementById("btn");
var moc = document.getElementById("up");

var captchaText = document.querySelector(".captcha-text"); // Use correct selector with a dot for class
var reloadBtn = document.getElementById("new"); // Use correct selector with a dot for class
var capInput = document.querySelector(".captcha-input"); // Use correct selector with a dot for class

const pass = () => {
    var password = document.getElementById("password");
    var cpassword = document.getElementById("cpassword");
    var eyeIcon = document.querySelector(".eye-icon");

    if (password.type === "password") {
        password.type = "text";
        cpassword.type = "text";
        eyeIcon.innerHTML = '<i class="fa fa-eye-slash"></i>';
    } else {
        password.type = "password";
        cpassword.type = "password";
        eyeIcon.innerHTML = '<i class="fa fa-eye"></i>';
    }
}
const lpass = () => {
    var password = document.getElementById("lpassword");
    var eyeIcon = document.querySelector(".eye-icon");

    if (password.type === "password") {
        password.type = "text";
        eyeIcon.innerHTML = '<i class="fa fa-eye-slash"></i>';
    } else {
        password.type = "password";
        eyeIcon.innerHTML = '<i class="fa fa-eye"></i>';
    }
}

function signup() {
    log.style.left = "-400px";
    reg.style.left = "50px";
    tog.style.left = "110px";
}

function login() {
    log.style.left = "50px";
    reg.style.left = "450px";
    tog.style.left = "0px";
}

var pos = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z', 'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

var captchaText = document.getElementById('captchaText');
var captchaInput = document.getElementById('captchaInput');
var captchaError = document.getElementById('captchaError');
var reloadBtn = document.getElementById('reloadBtn');

function getCaptcha() {
    var captcha = '';
    for (let i = 0; i < 6; i++) {
        var ranChar = pos[Math.floor(Math.random() * pos.length)];
        captcha += ranChar;
    }
    return captcha;
}

function validateCaptcha() {
    var generatedCaptcha = captchaText.innerText.replace(/\s+/g, '');
    var userCaptcha = captchaInput.value.replace(/\s+/g, '');

    if (generatedCaptcha === userCaptcha) {
        captchaError.textContent = "You are not a robot";
        captchaError.style.color = "green";
    } else {
        captchaError.textContent = "Please enter the correct captcha";
        captchaError.style.color = "red";
    }
}

reloadBtn.addEventListener("click", () => {
    captchaText.innerText = getCaptcha();
    captchaInput.value = '';
    captchaError.textContent = '';
});

// Initialize the captcha
captchaText.innerText = getCaptcha();

// Validate captcha on input change
captchaInput.addEventListener("input", validateCaptcha);



// Form Client Side validation

function printError(elemId, hintMsg) {
    document.getElementById(elemId).innerHTML = hintMsg;
}

function validateForm() {
    // Retrieving the values of form elements 
    var name = document.contactForm.userName.value;
    var email = document.contactForm.userEmail.value;
    var password = document.contactForm.userPassword.value;
    var passwordConfirm = document.contactForm.passwordConfirm.value;

    // Defining error variables with a default value
    var nameErr = emailErr = passErr = passConErr = true; // Added captchaError

    // Validate name
    if(name == "") {
        printError("nameErr", "Please enter your name");
        let nameField = document.getElementById("nameInput");
        nameField.style.borderColor = "#f75239";
        nameField.focus();
    } else {
        var regex = /^[a-zA-Z\s]+$/;                
        if(regex.test(name) === false) {
            printError("nameErr", "Please enter a valid name");
            let nameField = document.getElementById("nameInput");
            nameField.style.borderColor = "#f75239";
            nameField.focus();
        } else {
            printError("nameErr", "");
            let nameField = document.getElementById("nameInput");
            nameField.style.borderColor = "#D3D3D3";
            nameErr = false;
        }
    }
    // Validate email address
    if(email == "") {
        printError("emailErr", "Please enter your email address");
    } else {
        // Regular expression for basic email validation
        var regex = /^\S+@\S+\.\S+$/;
        if(regex.test(email) === false) {
            printError("emailErr", "Please enter a valid email address");
        } else{
            printError("emailErr", "");
            emailErr = false;
        }
    }
    //Validate password
    var digitPattern = /\d/;
    var lowerPattern = /[a-z]/;
    var upperPattern = /[A-Z]/;
    if(password == "") {
        printError("passErr", "Please enter the password");
    } else {
        if (password == "" || password.length < 10 || password.length > 20 || !lowerPattern.test(password) || !upperPattern.test(password) || !digitPattern.test(password)) {           
        printError("passErr", "Match: Characters between 10-20; Atleast one lower, upper case letter and a number.")
        } else {
        printError("passErr", "");
        passErr = false;
        }
    }
    
    //validate passwordConfirm
    if (passwordConfirm == "") {
        printError("passConErr", "Please confirm your password");
    } else {
        if(passwordConfirm !== password) {
            printError("passConErr", "Password does not match. Please ensure it matches with password.");
        } else {
            printError("passConErr", "");
            passConErr = false;
        }
    }
    // //validate captcha
    // if(captcha == "") { // Corrected variable name
    //     printError("captchaError", "Please enter the captcha"); // Corrected error message
    // } else {
    //     printError("captchaError", "");
    //     captchaError = false;
    // }

    if((nameErr || emailErr) == true) {
        return false;
    }
    else{
        alert("Thank you for registerring! Please, click okay and login with your credtianls");

    }
}
// logIn Validation

function printSError(elemSId, hintSMsg) {
    document.getElementById(elemSId).innerHTML = hintSMsg;
}

function signinValidation() {
    var emailS = document.signVald.emailS.value;
    var passwordS = document.signVald.passwordS.value;

    var emailSErr = passSErr = true;

    if (emailS == "") {
        printSError("emailSErr", "Please enter your email address");
    } else {
        // Regular expression for basic email validation
        var regex = /^\S+@\S+\.\S+$/;
        if (regex.test(emailS) === false) {
            printSError("emailSErr", "Please enter a valid email address");
        } else {
            printSError("emailSErr", "");
            emailSErr = false;
        }
    }

    var digitPattern = /\d/;
    var lowerPattern = /[a-z]/;
    var upperPattern = /[A-Z]/;
    if (passwordS == "") {
        printSError("passSErr", "Please enter the password");
    } else {
        if (passwordS == "" || passwordS.length < 10 || passwordS.length > 20 || !lowerPattern.test(passwordS) || !upperPattern.test(passwordS) || !digitPattern.test(passwordS)) {
            printSError("passSErr", "Please enter a valid password");
        } else {
            printSError("passSErr", "");
            passSErr = false;
        }
    }

    if (emailSErr || passSErr) {
        return false;
    }
    else{
        alert("Sueccfully logged in. You will be now taken to the Home page.");

    }
}

