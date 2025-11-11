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
        top: '70%',           // Relative right position from the container
        left: '60%',          // Relative left position from the container
        shadow: false,        // Shadow
        position: 'absolute'  // Position CSS
    };

    const spinnerContainer = document.getElementById('spinner-container');
    spinnerContainer.innerHTML += '<div id="spinner"></div>';

    const target = document.getElementById('spinner');

    return new Spinner(opts).spin(target);
}

const spinner = showSpinner();

authenticatedFetch('/api/admin/getAllUsers.php', { method: 'GET' })
    .then(res => res.json())
    .then(data => {
        const usersTableContentInnerDiv = document.getElementById('users-table-content-inner-div');
        data.forEach(user => {
            const newLineDiv = document.createElement('div');
            newLineDiv.classList.add('bg-[#FBFBFB]', 'hover:bg-[#f5f5f5]', 'border-b', 'border-b-[#DFDFDF]', 'grid', 'grid-cols-3', 'px-8', 'py-4', 'interactive');
            newLineDiv.onclick = () => {
                showUserDetail(user.id);
            }

            const idColumn = document.createElement('p');
            idColumn.classList.add('text-lg');
            idColumn.textContent = "#" + user.id;

            const usernameColumn = document.createElement('p');
            usernameColumn.classList.add('text-lg');
            usernameColumn.textContent = user.username;

            const roleColumn = document.createElement('p');
            roleColumn.classList.add('text-lg');
            roleColumn.textContent = user.role

            newLineDiv.append(idColumn, usernameColumn, roleColumn);
            usersTableContentInnerDiv.appendChild(newLineDiv);
        });

        spinner.stop();
        document.getElementById('spinner-container').innerHTML = '';

        usersTableContentInnerDiv.classList.remove('hidden');

    }).catch(err => console.error('Error:', err));

document.getElementById('search-bar').addEventListener('input', debounce(searchUser, 300));

function debounce(func, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

function searchUser(e) {
    const usersTableContentInnerDiv = document.getElementById('users-table-content-inner-div');
    usersTableContentInnerDiv.innerHTML = '';
    usersTableContentInnerDiv.classList.add('hidden');

    const spinner = showSpinner();

    authenticatedFetch('/api/admin/searchUsers.php', {
        method: 'POST',
        body: JSON.stringify({ username: e.target.value })
    }).then(res => res.json())
        .then(data => {
            if (data.ok) {
                data[0].forEach(user => {
                    const newLineDiv = document.createElement('div');
                    newLineDiv.classList.add('bg-[#FBFBFB]', 'hover:bg-[#f5f5f5]', 'border-b', 'border-b-[#DFDFDF]', 'grid', 'grid-cols-3', 'px-8', 'py-4', 'interactive');
                    newLineDiv.id = `user-${user.id}`;

                    newLineDiv.onclick = () => showUserDetail(user.id);

                    const idColumn = document.createElement('p');
                    idColumn.classList.add('text-lg');
                    idColumn.textContent = "#" + user.id;

                    const usernameColumn = document.createElement('p');
                    usernameColumn.classList.add('text-lg');
                    usernameColumn.textContent = user.username;

                    const roleColumn = document.createElement('p');
                    roleColumn.classList.add('text-lg');
                    roleColumn.textContent = user.role;

                    newLineDiv.append(idColumn, usernameColumn, roleColumn);
                    usersTableContentInnerDiv.appendChild(newLineDiv);
                });

                spinner.stop();
                document.getElementById('spinner-container').innerHTML = '';

                usersTableContentInnerDiv.classList.remove('hidden');
            } else {
                const text = document.createElement('p');
                text.innerText = data.message;
                text.className = 'text-center mt-6';
                usersTableContentInnerDiv.append(text);

                spinner.stop();
                document.getElementById('spinner-container').innerHTML = '';

                usersTableContentInnerDiv.classList.remove('hidden');
            }
        }).catch(err => console.error("Error: ", err));
}

function renderUsersTable() {
    const rightContent = document.getElementById('right-content');

    rightContent.innerHTML = `
        <div class="bg-white rounded-t-xl w-full h-full flex items-center space-y-10 p-10">
        <div class="w-full h-fit flex flex-col px-12 space-y-12">
            <div class="flex flex-col space-y-4">
                <h1 class="text-5xl font-semibold text-[#1B3B50]">Usuarios</h1>
                <p class="text-sm text-[#6A7282]">Gestiona a todos los usuarios registrados en el sistema.</p>
            </div>
                
                <div class="flex w-full h-14 space-x-8">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-4 flex items-center text-gray-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                            </svg>
                        </span>
                        <input id="search-bar" type="text" placeholder="Buscar Usuario..."
                            class="w-full py-3 pl-12 pr-12 rounded-2xl border-0 shadow-sm focus:ring-2 focus:ring-[#E1A05B] transition duration-150"/>
                        <button class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-[#E1A05B] transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 018 17V13.414L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="w-full h-fit h-min-[50vh] shadow-lg/25 flex flex-col rounded-2xl overflow-hidden">
                    <div class="bg-[#F4F4F4] w-full border-b border-b-[#DFDFDF] px-8 py-4 rounded-t-2xl grid grid-cols-3">
                        <p class="text-lg">ID</p>
                        <p class="text-lg">Username</p>
                        <p class="text-lg">Rol</p>
                    </div>
                    <div id="users-table-content" class="h-[50vh] overflow-y-scroll font-light">
                        <div id="spinner-container"></div>
                        <div class="hidden" id="users-table-content-inner-div"></div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('search-bar').addEventListener('input', debounce(searchUser, 300));

    // Cargar los usuarios
    loadUsers();
}

function loadUsers() {
    authenticatedFetch('/api/admin/getAllUsers.php', { method: 'GET' })
        .then(res => res.json())
        .then(data => {
            const usersTableContentInnerDiv = document.getElementById('users-table-content-inner-div');
            usersTableContentInnerDiv.innerHTML = '';

            data.forEach(user => {
                const newLineDiv = document.createElement('div');
                newLineDiv.classList.add('bg-[#FBFBFB]', 'hover:bg-[#f5f5f5]', 'border-b', 'border-b-[#DFDFDF]', 'grid', 'grid-cols-3', 'px-8', 'py-4', 'interactive');
                newLineDiv.id = `user-${user.id}`;

                newLineDiv.onclick = () => showUserDetail(user.id);

                const idColumn = document.createElement('p');
                idColumn.classList.add('text-lg');
                idColumn.textContent = "#" + user.id;

                const usernameColumn = document.createElement('p');
                usernameColumn.classList.add('text-lg');
                usernameColumn.textContent = user.username;

                const roleColumn = document.createElement('p');
                roleColumn.classList.add('text-lg');
                roleColumn.textContent = user.role;

                newLineDiv.append(idColumn, usernameColumn, roleColumn);
                usersTableContentInnerDiv.appendChild(newLineDiv);
            });

            usersTableContentInnerDiv.classList.remove('hidden');
        }).catch(err => console.error('Error:', err));
}

function showUserDetail(userId) {
    const rightContent = document.getElementById('right-content');

    // Spinner config
    const opts = {
        lines: 12,            // Lines number
        length: 7,            // Lenght of each line
        width: 5,             // Widht of the line
        radius: 10,           // Inner radius of the circle
        scale: 1.0,           // Spinner scale
        color: '#ffffff',        // Color
        opacity: 0.25,        // Lines opacity
        rotate: 0,            // Initial rotation
        direction: 1,         // 1: clockwise, -1: anti-clockwise
        speed: 1,             // Spins per second
        trail: 60,            // After the trail (%)
        fps: 20,              // fps
        zIndex: 2e9,          // z-index
        className: 'spinner', // Assinged CSS class
        top: '60%',           // Relative right position from the container
        left: '60%',          // Relative left position from the container
        shadow: false,        // Shadow
        position: 'absolute'  // Position CSS
    };

    rightContent.innerHTML = '<div id="spinner"></div>';

    const target = document.getElementById('spinner');

    const spinner = new Spinner(opts).spin(target);

    authenticatedFetch('/api/admin/getSpecificUserData.php', {
        method: 'POST',
        body: JSON.stringify({ id: userId })
    })
        .then(res => res.json())
        .then(userData => {
            spinner.stop();

            user = userData[0];

            rightContent.innerHTML = `
                <div class="bg-white w-full h-full rounded-t-xl flex flex-col space-y-12 items-center py-12">
                    <div class="flex flex-col w-full h-full items-center space-y-18 justify-center">
                        <div class="flex flex-col items-center space-y-5">
                            <div class="rounded-full bg-[#173345] w-36 h-36 drop-shadow-lg/45 p-1 flex">
                                <img src="${user.profile_pic}" 
                                    alt="" class="h-full w-full rounded-full aspect-square object-cover">
                            </div>
                            <div class="flex flex-col items-center space-y-1.5">
                                <h1 class="text-3xl">${user.display_name || user.username}</h1>
                                <p class="font-light text-lg">Tipo de usuario: ${user.role}</p>
                            </div>
                        </div>
                        
                        <div class="w-8/12 flex flex-col drop-shadow-md/25">
                            <div class="bg-[#F4F4F4] w-full border-b border-b-[#DFDFDF] px-6 py-3 rounded-t-2xl">
                                <p class="text-lg">Datos Personales</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Id:</p>
                                <p class="col-span-2">#${user.id}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Username:</p>
                                <p class="col-span-2">${user.username}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Display Name:</p>
                                <p class="col-span-2">${user.display_name || 'N/A'}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Email:</p>
                                <p class="col-span-2">${user.email || 'N/A'}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Rol:</p>
                                <p class="col-span-2">${user.role}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] grid grid-cols-3 px-6 py-4 rounded-b-2xl">
                                <p class="font-light col-span-1">Grupo:</p>
                                <p class="col-span-2">${user.group || 'N/A'}</p>
                            </div>
                        </div>
                        
                        <button onclick="deleteUser(${user.id})" class="red-button interactive w-52 h-18">
                            Eliminar Usuario
                        </button>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            console.error("Ha ocurrido un error: ", err);
            rightContent.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-white text-xl">Error al cargar usuario</p></div>';
        });
}

function deleteUser(userId) {

    const notyf = new Notyf({
        duration: 3500,
        position: { x: 'right', y: 'top' },
        dismissible: true
    });

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            authenticatedFetch('/api/admin/deleteUser.php', {
                method: 'DELETE',
                body: JSON.stringify({ id: userId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        notyf.success(data.message);
                        renderUsersTable();
                    } else {
                        notyf.error(data.message);
                    }
                }).catch(err => console.error("Error: ", err));
        } else {
            notyf.error('La acción ha sido cancelada');
        }
    });
}