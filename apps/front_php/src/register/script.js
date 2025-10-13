window.addEventListener('DOMContentLoaded', StartEvents);

function StartEvents() {
    AddListeners();
}

function AddListeners() {
    const registerForm = document.getElementById('register-form');
    registerForm.addEventListener('submit', SendRegisterData);

    const passwordIcons = document.querySelectorAll('#password-icon');
    passwordIcons.forEach((element) => {
        element.addEventListener('click', () => {
            const input = element.nextElementSibling;
            HidingAndShowingHandler(input, element);
        });
    });
}

function HidingAndShowingHandler(password, eyeIcon) {
    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.src = '../images/CloseEyeIcon.svg';
    } else {
        password.type = 'password';
        eyeIcon.src = '../images/OpenEyeIcon.svg';
    }
}

function SendRegisterData(e) {
    e.preventDefault();
    
    const usernameInput = document.getElementById('username-input');
    const emailInput = document.getElementById('email-input');
    const passwordInput = document.getElementById('password-input');
    const confirmPasswordInput = document.getElementById('confirm-password-input');

    const username = usernameInput.value;
    const email = emailInput.value;
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    const userData = { username, email, password, confirmPassword };

    fetch('/api/user/register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'include',
        body: JSON.stringify(userData)
    }).then(res => res.json())
    .then(res => {
        if (res.ok) {

        } else {
            if (res.username) {
                const usernameError = res.username['error'];
                const usernameErrorElement = document.getElementById('username-error-message');
                usernameErrorElement.innerHTML = usernameError;

                const usernameLabel = document.getElementById('username-label');
                AddErrorLabelStyle(usernameLabel);
            }

            if (res.email){
                const emailError = res.email['error'];
                const emailErrorElement = document.getElementById('email-error-message');
                emailErrorElement.innerHTML = emailError;

                const emailLabel = document.getElementById('email-label');
                AddErrorLabelStyle(emailLabel);
            } else {
                const emailLabel = document.getElementById('email-label');
                const emailErrorElement = document.getElementById('email-error-message');
                emailErrorElement.innerHTML = '';
                RemoveErrorLabelStyle(emailLabel);
            }

            if (res.password) {
                const passwordError = res.password['error'];
                const passwordErrorElement = document.getElementById('password-error-message');
                passwordErrorElement.innerHTML = passwordError;

                const passwordLabel = document.getElementById('password-label');
                AddErrorLabelStyle(passwordLabel);
            }

            if (res.confirmPassword) {
                const confirmPasswordError = res.confirmPassword['error'];
                const confirmPasswordErrorElement = document.getElementById('confirm-password-error-message');
                confirmPasswordErrorElement.innerHTML = confirmPasswordError;

                const confirmPasswordLabel = document.getElementById('confirm-password-label');
                AddErrorLabelStyle(confirmPasswordLabel);
            }
        }
    })
    .catch(err => console.error(err));
}

function AddErrorLabelStyle(element) {
    element.classList.add('absolute');
    element.classList.add('-translate-y-6');
}

function RemoveErrorLabelStyle(element) {
    element.classList.remove('absolute');
    element.classList.remove('-translate-y-6');
}