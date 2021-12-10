document.addEventListener('DOMContentLoaded', function (e) {
    const showAuthBtn = document.getElementById('brandh-show-auth-form'),
        authContainer = document.getElementById('brandh-auth-container'),
        close = document.getElementById('brandh-auth-close');
    
    showAuthBtn.addEventListener('click', () => {
        authContainer.classList.add('show');        
        showAuthBtn.parentElement.classList.add('hide');
    });

    close.addEventListener('click', () => {
        authContainer.classList.remove('show');
        showAuthBtn.parentElement.classList.remove('hide');
    });
});