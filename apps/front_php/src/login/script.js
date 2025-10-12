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
        eyeIcon.src = '../images/CloseEyeIcon.svg';
    } else {
        password.type = 'password';
        eyeIcon.src = '../images/OpenEyeIcon.svg';
    }
}

function SendLogindata(e) {
    e.preventDefault();
   
    const usernameInput = document.getElementById('username-input');
    const passwordInput = document.getElementById('password-input');
    const username = usernameInput.value;
    const password = passwordInput.value;
    const data = { username, password };
    
    fetch('/api/user/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'include',
        body: JSON.stringify(data)
    }).then(res => res.json())
    .then(res => {
        if (res.ok) {
            authManager.saveAuth(res);
            
            window.location.replace(window.location.origin + '/home/index.php');
        } else {
            console.error(res.error);
            
            const labels = document.querySelectorAll('label');
            labels.forEach((element, i) => {
                if (!element.classList.contains("-translate-y-6") && !element.classList.contains('absolute')) {
                    element.classList.add('absolute');
                    element.classList.add('-translate-y-6');
                }
            });
            
            if (!usernameInput.classList.contains('invalid-input')) {
                usernameInput.classList.add('invalid-input');
            }
           
            if (!passwordInput.classList.contains('invalid-input')) {
                passwordInput.classList.add('invalid-input');
            }
            
            const errorMsgs = document.querySelectorAll('#error-message');
            errorMsgs.forEach((element, i) => {
                element.innerHTML = res.error;
            });
            
            const notyf = new Notyf({
                duration: 2000,
                position: { x: 'right', y: 'top' },
                dismissible: true
            });
            notyf.error(res.error);
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