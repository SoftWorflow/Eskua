let isUsernameValid;
let isEmailValid;
let isPasswordValid;
let isSamePassword;

window.addEventListener('DOMContentLoaded', StartEvents);

function StartEvents() {
    AddListeners();
    Google();
}

function Google() {
    
    google.accounts.id.initialize({
      client_id: "761002639560-vejkjfodd513khe9ifmrsjq46o0c619s.apps.googleusercontent.com",
      callback: handleCredentialResponse,
    });

    google.accounts.id.prompt();

    const button = document.getElementById('google-btn');

    button.addEventListener('click', () => {
        google.accounts.id.prompt();
    });

}

function handleCredentialResponse(response) {
    console.log("Token:", response.credential);
}

function AddListeners() {
    const usernameField = document.getElementById('username');
    usernameField.addEventListener('blur', CheckUsername);

    const emailField = document.getElementById('user-email');
    emailField.addEventListener('blur', CheckEmail);

    const passwordField = document.getElementById('user-password');
    passwordField.addEventListener('blur', CheckPassword);
    passwordField.addEventListener('change', VerifySecondTimePassword);

    const verifyPasswordField = document.getElementById('verify-user-password');
    verifyPasswordField.addEventListener('blur', VerifySecondTimePassword);
    verifyPasswordField.addEventListener('change', VerifySecondTimePassword);

    const registerForm = document.getElementById('register-form');
    registerForm.addEventListener('submit', SendRegisterData);
}

function HandleShowingAndHidingPassword() {
    const password = document.getElementById('user-password');
    const eyeIcon = document.getElementById('eye-icon');
    HidingAndShowingHandler(password, eyeIcon);
}

function HandleShowingAndHidingVerifyPassword() {
    const verifyPassword = document.getElementById('verify-user-password');
    const eyeIcon = document.getElementById('eye-icon-verify');
    HidingAndShowingHandler(verifyPassword, eyeIcon);
}

function HidingAndShowingHandler(password, eyeIcon) {
    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.src = 'imgs/Show.png';
    } else {
        password.type = 'password';
        eyeIcon.src = 'imgs/Hide.png';
    }
}

function SendRegisterData(e) {
    e.preventDefault();

    if (isUsernameValid && isEmailValid && isPasswordValid) {
        if (isSamePassword) {
            const registerForm = document.getElementById('register-form');
            const formData = new FormData(registerForm);

            const data = {
                username: registerForm.username.value,
                email: registerForm.email.value,
                password: registerForm.password.value,
            };

            fetch('register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }).then(res => res.json()).then(res => {
                // Server response
            })
            .catch(err => console.error(err));
        }
    }
}

function CheckUsername(e) {
    const usernameField = document.getElementById('username');
    const errMsgElement = document.getElementById('username-error-message');
    const username = e.target.value; 
    let message = '';
    
    //Solo puede tener cualquier letra y puntos
    const regex = /^[A-Za-z.]+$/;
    isUsernameValid = regex.test(username);

    if (username.length === 0) {
        message = 'El nombre de usuario es obligatorio';
    } else if (username.length > 30) {
        isUsernameValid = false; 
        message = 'Nombre de usuario muy largo (30 máx.)';
    } else if (/\d/.test(username)) {
        message = 'No puede contener números';
    } else if (/[^A-Za-z.]/.test(username)) {
        message = 'No puede contener caracteres especiales ni espacios';
    }

    ToggleValidationState(isUsernameValid, usernameField);
    errMsgElement.innerHTML = message;

}

function CheckEmail(e) {
    const emailField = document.getElementById('user-email');
    const emailErrorMessage = document.getElementById('email-error-message');
    const email = e.target.value;

    // (cualquier cosa que permiten)@(cantidad de dominios y subdominios que quieran y que el ultimo tenga 2 o más caracteres)
    const regex = /^[A-Za-z0-9._%+-]+@(?:[A-Za-z0-9-]+\.)+[A-Za-z]{2,}$/;
    isEmailValid = regex.test(email);

    if (email.length > 320) {
        isEmailValid = false;
        emailErrorMessage.innerHTML = 'El correo es muy largo (320 máx.)';
    } else if (!isEmailValid) {
        emailErrorMessage.innerHTML = 'Ingrese un correo válido';
    } else {
        emailErrorMessage.innerHTML = '';
    }

    ToggleValidationState(isEmailValid, emailField);
}

function CheckPassword(e) {
    const passwordField = document.getElementById('user-password');
    const passwordErrorMessage = document.getElementById('password-error-message');
    const password = e.target.value;

    // Al menos una minuscula, una mayuscula, un numero y 8 caracteres
    const regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/;
    isPasswordValid = regex.test(password);

    if (!/^(?=.*[a-z])(?=.*[A-Z]).+$/.test(password)) {
        passwordErrorMessage.innerHTML = 'La contraseña debe contener mayúsculas y minúsculas';
    } else if (!/^(?=.*\d).+$/.test(password)) {
        passwordErrorMessage.innerHTML = 'La contraseña debe incluir al menos un número';
    } else if (password.length < 8) {
        passwordErrorMessage.innerHTML = 'La contraseña es demasiado corta (8 caracteres mín.)';
    } else {
        passwordErrorMessage.innerHTML = ''
    }

    ToggleValidationState(isPasswordValid, passwordField);
}

function VerifySecondTimePassword() {
    const passwordField = document.getElementById('user-password');
    const verifyPasswordField = document.getElementById('verify-user-password');
    const verifyPasswordErrorMessage = document.getElementById('verify-password-error-message');

    const isVerifyPasswordOk = verifyPasswordField.value !== '' && verifyPasswordField.value === passwordField.value;

    if (!isVerifyPasswordOk){
        verifyPasswordErrorMessage.innerHTML = 'Las contraseñas no coinciden';
    } else {
        verifyPasswordErrorMessage.innerHTML = '';
    }

    ToggleValidationState(isVerifyPasswordOk, verifyPasswordField);
    isSamePassword = isVerifyPasswordOk;
}

function ToggleValidationState(valid, field) {
    if (!valid) {
        field.classList.add('invalid');
    } else {
        if (field.classList.contains('invalid')) {
            field.classList.remove('invalid');
        }
    }
}