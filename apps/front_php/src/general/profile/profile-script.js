let spinner;

function showSpinner() {
    // Spinner config
    const opts = {
        lines: 12,            // Lines number
        length: 7,            // Lenght of each line
        width: 5,             // Widht of the line
        radius: 10,           // Inner radius of the circle
        scale: 1.0,           // Spinner scale
        color: '#1B3B50',        // Color
        opacity: 0.25,        // Lines opacity
        rotate: 0,            // Initial rotation
        direction: 1,         // 1: clockwise, -1: anti-clockwise
        speed: 1,             // Spins per second
        trail: 60,            // After the trail (%)
        fps: 20,              // fps
        zIndex: 2e9,          // z-index
        className: 'spinner', // Assinged CSS class
        top: '50%',           // Relative right position from the container
        left: '50%',          // Relative left position from the container
        shadow: false,        // Shadow
        position: 'relative'  // Position CSS
    };

    const spinnerContainer = document.getElementById('spinner-container');
    spinnerContainer.innerHTML += '<div id="spinner"></div>';

    const target = document.getElementById('spinner');

    return new Spinner(opts).spin(target);
}

document.addEventListener('DOMContentLoaded', () => {
    loadProfileData();
    spinner = showSpinner();
});

function loadProfileData() {
    const infoContainer = document.getElementById('profile-info-container');

    const displayName = document.getElementById('display-name');
    const email = document.getElementById('email');
    const role = document.getElementById('role');
    const profilePic = document.getElementById('profile-picture');
    
    authenticatedFetch('/api/user/getProfile.php', {
        method: 'GET'
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                window.location = '/';
                return;
            }

            displayName.innerText = data.profile['displayName'];
            email.innerText = data.profile['email'];
            role.innerText = capFirstLetter(data.profile['role']);
            profilePic.src = data.profile['profilePic'];

            spinner.stop();
            infoContainer.classList.remove('hidden');
        }).catch(err => console.error("Error: ", err))
}

function capFirstLetter(str) {
  if (!str) return "";

  const primeraLetra = str.charAt(0).toUpperCase();
  
  const restoDeLaCadena = str.slice(1);

  return primeraLetra + restoDeLaCadena;
}