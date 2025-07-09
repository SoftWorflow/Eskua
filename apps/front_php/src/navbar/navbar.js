window.addEventListener('DOMContentLoaded', () => {
    UserIsRegistered();
});

// Seacrch until found the given id
function WaitElement(id, callback) {
  const existing = document.getElementById(id);
  if (existing) {
    return callback(existing);
  }

  const observer = new MutationObserver((mutation, obs) => {
    const element = document.getElementById(id);
    if (element) {
      obs.disconnect();
      callback(element);
    }
  });

  observer.observe(document.documentElement, {
    childList: true,
    subtree: true,
  });
}

function UserIsRegistered() {
    let token = localStorage.getItem('accessToken');
    let isRegistered;

    if (token) {
        isRegistered = true;
    } else {
        isRegistered = false;
    }

    ChangeNavbar(isRegistered);
}

async function ChangeNavbar(registered) {
    // if (!registered) return;

    // Waits until the Promise returns something
    const rightSideContent = await new Promise(resolve =>
      WaitElement('right-side-content', resolve)
    );

    if (rightSideContent) {
      // Only works when is in running in docker
      const notificationIcon = '../images/bell.png';

      rightSideContent.innerHTML = `
        <img src="${notificationIcon}" alt="notification-logo" id="notification-logo">
        <div class="profile-picutre-container"><img class="profile-picture" src="" alt="profile-picture" id="profile-picture"></div>
      `;
    }

    const navbarOptions = await new Promise(resolve =>
      WaitElement('navbar-options', resolve)
    );

    if (navbarOptions) {

      const option1Href = '#';
      const option2Href = '#';
      const option3Href = '#';
      const option4Href = '#';

      navbarOptions.innerHTML = `
        <li><a href="${option1Href}">Juegos</a></li>
        <li><a href="${option2Href}">IA</a></li>
        <li><a href="${option3Href}">Materiales</a></li>
        <li><a href="${option4Href}">Otros</a></li>
      `;
    }
}