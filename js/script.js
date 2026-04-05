document.addEventListener("DOMContentLoaded", function(){

    const images = [
        "images/bg1.jpg",
        "images/bg2.jpg",
        "images/bg3.jpg"
    ];

    let index = 0;
    const background = document.querySelector(".background");

    background.style.backgroundImage = `url(${images[index]})`;

    setInterval(() => {
        index++;
        if(index >= images.length){
            index = 0;
        }
        background.style.backgroundImage = `url(${images[index]})`;
    }, 4000);

    const toggle = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    toggle.addEventListener("click", function(){
        if(password.type === "password"){
            password.type = "text";
        } else {
            password.type = "password";
        }
    });

});
const loginForm = document.getElementById("loginForm");

if(loginForm){
    loginForm.addEventListener("submit", function(e){
        e.preventDefault();
        window.location.href = "pages/dashboard.html";
    });
}