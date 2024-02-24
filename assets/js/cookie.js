// Check if the user has accepted the cookie
if (document.cookie.split(';').some((item) => item.trim().startsWith('cookieAccepted='))) {
    document.getElementById('cookieConsent').style.display = 'none';
}


document.getElementById('acceptCookie').addEventListener('click', function () {
    //cookie
    document.cookie = 'cookieAccepted=true; expires=Fri, 31 Dec 2024 23:59:59 GMT; path=/';
    // hides the cookie banner
    document.getElementById('cookieConsent').style.display = 'none';
});

document.getElementById('rejectCookie').addEventListener('click', function () {
    //cookie
    //document.cookie = 'cookieAccepted=false; expires=Fri, 31 Dec 2024 23:59:59 GMT; path=/';
    // hides the cookie banner
    document.getElementById('cookieConsent').style.display = 'none';
});