document.addEventListener('DOMContentLoaded', () => {
    const loader = document.getElementById('initial-loader');
    const letters = loader ? loader.querySelectorAll('.letter span') : [];
    const progressBar = document.getElementById('progress-bar');
    const loginContainer = document.getElementById('login-container');
    const loginBox = document.querySelector('.login-box');

    if (!loader || !loginContainer || !loginBox) {
        if (loginContainer) loginContainer.style.top = '0';
        if (loginContainer) loginContainer.style.opacity = '1';
        if (loginBox) loginBox.classList.add('show');
        return;
    }

    loader.style.display = 'flex';
    loginContainer.style.top = '100%';
    loginContainer.style.opacity = '0';

    let progress = 0;
    const totalTime = 2000;
    const intervalTime = 20;
    const increment = 100 / (totalTime / intervalTime);

    const interval = setInterval(() => {
        progress += increment;
        if (progress > 100) progress = 100;

        const lettersToFill = Math.floor((progress / 100) * letters.length);
        for (let i = 0; i < lettersToFill; i++) {
            letters[i].style.height = '100%';
        }

        if (progressBar) progressBar.style.width = progress + '%';

        if (progress >= 100) {
            clearInterval(interval);

            loader.style.transition = 'transform 0.8s ease';
            loginContainer.style.transition = 'top 0.8s ease, opacity 0.8s ease';
            loader.style.transform = 'translateY(-100%)';
            loginContainer.style.top = '0';
            loginContainer.style.opacity = '1';
            setTimeout(() => loginBox.classList.add('show'), 100);
            setTimeout(() => loader.style.display = 'none', 800);
        }
    }, intervalTime);
});
