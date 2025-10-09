document.addEventListener('DOMContentLoaded', () => {
  fetch('../navbar/navbar.html')
    .then(res => {
      if (!res.ok) throw new Error('No se pudo cargar navbar');
      return res.text();
    })
    .then(html => {
      document.getElementById('nav-placeholder').insertAdjacentHTML('afterbegin', html);
    })
    .catch(err => console.error(err));
});