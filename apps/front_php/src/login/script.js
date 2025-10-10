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
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data)
    }).then(res => res.json())
    .then(res => {
        if (res.ok) {
            // Saves the access token things is the sessionStorage
            sessionStorage.setItem("access_token", res.access_token);
            sessionStorage.setItem("access_expires_at", res.access_expires_at);

            // Saves user data (display name & profile pic) in the session storage
            sessionStorage.setItem('user', JSON.stringify(res.user));

            // Redirects the user to the home page
            window.location.replace(window.location.origin + '/');
            console.log("test");
        } else {
            console.error(res.error);
            const inputs = document.querySelectorAll('[id="input-container"]');

            inputs.forEach((element, i) => {
                ToggleValidationState(false, element);
            });

            const errorMsg = document.querySelectorAll('[id="error-message"]');

            errorMsg.forEach((element, i) => {
                element.innerHTML = res.error;
            });
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