window.addEventListener('DOMContentLoaded', StartEvents);

function StartEvents() {
    
}

function ChangeScreen(screen) {
    const rightContent = document.getElementById('right-content');
    rightContent.innerHTML = '';
    RemoveAllStyles();
    switch (screen) {
        case 1:
            fetch('main.html')
            .then(res => {
                if (!res.ok) throw new Error("Main can't load");
                return res.text();
            })
            .then(html => {
                rightContent.insertAdjacentHTML('afterbegin', html);
            })
            .catch(err => console.error(err));
            break;
        case 2:
            fetch('../users/usuarios.html')
            .then(res => {
                if (!res.ok) throw new Error("Users can't load");
                return res.text();
            })
            .then(html => {
                rightContent.insertAdjacentHTML('afterbegin', html);
            })
            .catch(err => console.error(err));

            const href = '../users/user-styles.css';
            AddStyles(href);

            const src = '../users/users.js';
            AddScript(src);
            break;
        case 3:
            break;
        case 4:
            break;
        case 5:
            break;
    }
}

function AddScript(src) {
    if (!document.querySelector(`script[src="${src}"]`)) {
        const script = document.createElement('script');
        script.src = src;
        document.head.appendChild(script);
    }
}

function RemoveScript(src) {
    const script = document.querySelector(`script[src="${src}"]`);
    if (script) {
        script.parentNode.removeChild(script);
    }
}

function RemoveAllScript() {
    RemoveScript('../users/users.js');
}

function AddStyles(href) {
  if (!document.querySelector(`link[href="${href}"]`)) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    document.head.appendChild(link);
  }
}

function RemoveStyles(href) {
  const link = document.querySelector(`link[href="${href}"]`);
  if (link) {
    link.parentNode.removeChild(link);
  }
}

function RemoveAllStyles() {
    RemoveStyles('../users/styles-usuarios.css');
    RemoveStyles('../profile/profile.css');
}