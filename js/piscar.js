document.addEventListener('DOMContentLoaded', function () {
    var addToCartIcons = document.querySelectorAll('.addToCart');

    addToCartIcons.forEach(function (icon) {
        icon.addEventListener('click', function () {
            console.log('Icon clicked!');
            icon.classList.toggle('clicked');
            console.log('Class toggled:', icon.classList.contains('clicked'));

            // You can remove the 'clicked' class after a delay if needed
            setTimeout(function () {
                icon.classList.remove('clicked');
            }, 300); 
        });
    });
});

document.addEventListener('DOMContentLoaded', function(){
    const button = document.querySelector('.all_inputs button');

    button.addEventListener('click', function(){
        button.classList.toggle('clicked');

        setTimeout(function(){
            button.classList.remove('clicked');
        }, 500);
    });
    
});

document.addEventListener('DOMContentLoaded', function(){
    const input_name = document.getElementById("name");
    const after_name = document.getElementById("from_name");

    input_name.addEventListener('input', function(){
        after_name.value = input_name.value;
    });
});

const form = document.getElementById('contact');

form.addEventListener('submit', function(e) {

    const hCaptcha = form.querySelector('textarea[name=h-captcha-response]').value;

    if (!hCaptcha) {
        e.preventDefault();
        alert("Por favor preencha o captcha")
        return
    }
});

window.onload = function() {
    // Reset the form fields when the page loads
    document.getElementById("contact").reset();
};
