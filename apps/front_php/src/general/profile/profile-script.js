document.addEventListener('DOMContentLoaded', () => {
    loadProfileData();
});

function loadProfileData() {
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
        }).catch(err => console.error("Error: ", err))
}

function capFirstLetter(str) {
  if (!str) return "";

  const primeraLetra = str.charAt(0).toUpperCase();
  
  const restoDeLaCadena = str.slice(1);

  return primeraLetra + restoDeLaCadena;
}