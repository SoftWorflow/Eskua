window.addEventListener('DOMContentLoaded', StartEvents);

function StartEvents() {
    AddListeners();
}

function handleCredentialResponse(response) {
    console.log("Token:", response.credential);
}

function AddListeners() {
    const loginForm = document.getElementById('login-form');
    loginForm.addEventListener('submit', SendLogindata);
}


function HandleShowingAndHidingPassword() {
    const password = document.getElementById('password-input');
    const eyeIcon = document.getElementById('password-icon');
    HidingAndShowingHandler(password, eyeIcon);
}

function HidingAndShowingHandler(password, eyeIcon) {
    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.src = '../images/OpenEyeIcon.svg';
    } else {
        password.type = 'password';
        eyeIcon.src = '../images/CloseEyeIcon.svg';
    }
}

function SendLogindata(e) {
    e.preventDefault();
    
    const usernameInput = document.getElementById('username-input');
    const passwordInput = document.getElementById('password-input');

    const username = usernameInput.value;
    const password = passwordInput.value;

}

function ToggleValidationState(valid, field) {
    if (!valid) {
        field.classList.add('invalid');
        field.classList.remove('valid');
    } else {
        if (field.classList.contains('invalid')) {
            field.classList.remove('invalid');
        }
        field.classList.add('valid');
    }
}