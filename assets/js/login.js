const toggleForm = () => {
    const container = document.querySelector('.container');
    container.classList.toggle('active');
};

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
