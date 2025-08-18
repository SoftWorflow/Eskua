window.addEventListener('DOMContentLoaded', StartEvents);

function StartEvents() {

}

function OnClickedUser(userType) {
  const rightContent = document.getElementById('right-content');
  rightContent.innerHTML = '';
  fetch('profile/profile.html')
  .then(res => {
    if (!res.ok) throw new Error("User can't load");
    return res.text();
  })
  .then(html => {
    rightContent.insertAdjacentHTML('afterbegin', html);

    // Adds the style for the profile things
    const href = 'profile/profile.css';
    AddStyles(href);

    //Puts the respective fields for each user type
    PutFields(userType);
  })
    .catch(err => console.error(err));
}

function AddStyles(href) {
  if (!document.querySelector(`link[href="${href}"]`)) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    document.head.appendChild(link);
  }
}

function PutFields(userType) {
  const userFields = document.getElementById('user-fields');
  let newFields = null;
  switch (userType.toString().trim()) {
    case 'admin':
      newFields = null;
      break;
    case 'guest':
      newFields = null;
      break;
    case 'teacher':
      newFields = `
        <div class="user-atribute-container">
          <div class="atribute-name-container">
            <img src="" alt="" class="atribute-icon">
            <p class="atribute-title">Grupo</p>
          </div>
          <div class="atribute-value-container">
            <p class="atribute-value" id="display-name">Nombre Grupo</p>
          </div>
        </div>
        <div class="user-atribute-container">
          <div class="atribute-name-container">
            <img src="" alt="" class="atribute-icon">
            <p class="atribute-title">Actividades Creadas</p>
          </div>
          <div class="atribute-value-container">
            <p class="atribute-value" id="display-name">Cantidad de actividades</p>
          </div>
        </div>
      `;
      break;
    case 'student':
      newFields = `
        <div class="user-atribute-container">
          <div class="atribute-name-container">
            <img src="" alt="" class="atribute-icon">
            <p class="atribute-title">Grupo</p>
          </div>
          <div class="atribute-value-container">
            <p class="atribute-value" id="display-name">Nombre Grupo</p>
          </div>
        </div>
      `;
      break;
    default:
      console.error('Error while trying to get user type!');
      break;
  }
  if (newFields != null) {
    userFields.insertAdjacentHTML('beforeend', newFields);
  }
}