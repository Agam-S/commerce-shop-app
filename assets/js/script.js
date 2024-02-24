function setModeToStorage(mode) {
    localStorage.setItem("selectedMode", mode);
}


function getModeFromStorage() {
    return localStorage.getItem("selectedMode");
}

const radioButtons = document.querySelectorAll('input[type="radio"]');
const body = document.body;

const storedMode = getModeFromStorage();

if (storedMode === "dark") {
    body.classList.add("dark-mode");
    body.classList.remove("light-mode");
    document.getElementById("dark-mode").checked = true;
} else {
    body.classList.add("light-mode");
    body.classList.remove("dark-mode");
    document.getElementById("light-mode").checked = true;
}

radioButtons.forEach(radioButton => {
    radioButton.addEventListener("change", function () {
        if (radioButton.value === "dark") {
            body.classList.add("dark-mode");
            body.classList.remove("light-mode");
            setModeToStorage("dark");
        } else {
            body.classList.add("light-mode");
            body.classList.remove("dark-mode");
            setModeToStorage("light");
        }
    });
});