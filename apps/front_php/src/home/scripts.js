window.addEventListener('DOMContentLoaded', () => {
  const userString = sessionStorage.getItem('user');
  if (userString) {
    const user = JSON.parse(userString);
    console.log('Display name: ', user.display_name, ', profile picture url: ', user.profile_picture_url);
  }
});

window.addEventListener('scroll', function () {
  const navbar = document.querySelector('nav');
  if (window.scrollY > 10) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});