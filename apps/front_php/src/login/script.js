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
    const loginForm = document.getElementById('login-form');
    loginForm.addEventListener('submit', SendLogindata);
}


function HandleShowingAndHidingPassword() {
    const password = document.getElementById('user-password');
    const eyeIcon = document.getElementById('eye-icon');
    HidingAndShowingHandler(password, eyeIcon);
}

function HidingAndShowingHandler(password, eyeIcon) {
    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.src = '../images/show.png';
    } else {
        password.type = 'password';
        eyeIcon.src = '../images/hide.png';
    }
}

function SendLogindata(e) {
    e.preventDefault();
    
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('user-password');

    const username = usernameInput.value;
    const password = passwordInput.value;

    const data = { username, password };

    fetch('/api/user/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }).then(res => res.json()).then(res => {
        // Server response

        if (res.access_token) {
            const host = window.location.hostname;
            window.location.replace(window.location.origin + '/');
        }
    })
    .catch(err => console.error(err));
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