let globalMaterialId;
let globalHasChangedFile = false;
let globalFilePath = '';

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-bar').addEventListener('input', debounce(searchMaterial, 300));
});

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

loadMaterials();

function renderMaterialsTable() {
    const rightContent = document.getElementById('right-content');

    rightContent.innerHTML = `
        <div class="bg-white rounded-t-xl w-full h-full flex items-center space-y-10 p-10">
            
            <div class="w-full h-fit flex flex-col px-12 space-y-12 items-center">
                <div class="flex flex-col items-center space-y-4">
                    <h1 class="text-5xl">Material</h1>
                    <p class="font-light text-sm">Gestiona todo el material público del sistema.</p>
                </div>
                <div class="flex w-full h-14 space-x-8">
                    <div class="relative w-full">
                        <!-- Magnifying glass Icon -->
                        <span class="absolute inset-y-0 left-4 flex items-center text-gray-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                            </svg>
                        </span>
                        <input
                            id="search-bar"
                            type="text"
                            placeholder="Buscar Material..."
                            class="w-full py-3 pl-12 pr-12 rounded-2xl border-0 shadow-sm focus:ring-2 focus:ring-[#E1A05B] transition duration-150"
                        />
                        <button class="absolute inset-y-0 right-12 flex items-center text-gray-400 hover:text-[#E1A05B] transition interactive">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 018 17V13.414L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                        </button>
                        <button onclick="renderCreateMaterial()"class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-[#E1A05B] transition interactive">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
            </div>
                <div class="w-full h-fit h-min-[50vh] shadow-lg/25 flex flex-col rounded-2xl overflow-hidden">
                <div class="bg-[#F4F4F4] w-full border-b border-b-[#DFDFDF] px-8 py-4 rounded-t-2xl grid grid-cols-3">
                    <p class="text-lg">ID</p>
                    <p class="text-lg">Nombre</p>
                    <p class="text-lg">Tipo</p>
                </div>
                <div class="h-[50vh] overflow-y-scroll font-light">
                    <div id="spinner-container"></div>
                    <div class="hidden" id="material-table-content-inner-div"></div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('search-bar').addEventListener('input', debounce(searchMaterial, 300));

    // Cargar los usuarios
    loadMaterials();
}

function debounce(func, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

function searchMaterial(e) {
    const materialTableContentInnerDiv = document.getElementById('material-table-content-inner-div');
    materialTableContentInnerDiv.innerHTML = '';
    materialTableContentInnerDiv.classList.add('hidden');

    const spinner = showSpinner();

    authenticatedFetch('/api/admin/searchMaterials.php', {
        method: 'POST',
        body: JSON.stringify({ title: e.target.value })
    }).then(res => res.json())
    .then(data => {
        if (data.ok) {
            data[0].forEach(material => {
            const newLineDiv = document.createElement('div');
            newLineDiv.classList.add('bg-[#FBFBFB]', 'hover:bg-[#f5f5f5]', 'border-b', 'border-b-[#DFDFDF]', 'grid', 'grid-cols-3', 'px-8', 'py-4', 'interactive');
            newLineDiv.onclick = () => {
                // showGroupDetail(group.id);
            }

            const idColumn = document.createElement('p');
            idColumn.classList.add('text-lg');
            idColumn.textContent = "#" + material.id;

            const titleColumn = document.createElement('p');
            titleColumn.classList.add('text-lg');
            titleColumn.textContent = material.title;

            const typeColumn = document.createElement('p');
            typeColumn.classList.add('text-lg');
            typeColumn.textContent = material.type.toUpperCase();;

            newLineDiv.append(idColumn, titleColumn, typeColumn);
            materialTableContentInnerDiv.appendChild(newLineDiv);
        });

            spinner.stop();
            document.getElementById('spinner-container').innerHTML = '';

            materialTableContentInnerDiv.classList.remove('hidden');
        } else {
            materialTableContentInnerDiv.innerHTML = `<p>${data.message}</p>`;

            spinner.stop();
            document.getElementById('spinner-container').innerHTML = '';

            materialTableContentInnerDiv.classList.remove('hidden');
        }
    }).catch(err => console.error("Error: ", err));
}

function loadMaterials() {
    authenticatedFetch('/api/admin/getAllMaterials.php', { method: 'GET' })
    .then(res => res.json())
    .then(data => {
        if (!data.ok) {
            const notyf = new Notyf({
                duration: 3500,
                position: { x: 'right', y: 'top' },
                dismissible: true
            });

            notyf.error('No hay materiales públicos');
            return;
        }

        const materialTableContentInnerDiv = document.getElementById('material-table-content-inner-div');
        
        
        Object.values(data).forEach(material => {
            if (typeof material !== 'object' || material === null || !material.id) return;
            const newLineDiv = document.createElement('div');
            newLineDiv.classList.add('bg-[#FBFBFB]', 'hover:bg-[#f5f5f5]', 'border-b', 'border-b-[#DFDFDF]', 'grid', 'grid-cols-3', 'px-8', 'py-4', 'interactive');
            newLineDiv.onclick = () => {
                showMaterialDetail(material.id);
            }

            const idColumn = document.createElement('p');
            idColumn.classList.add('text-lg');
            idColumn.textContent = "#" + material.id;

            const titleColumn = document.createElement('p');
            titleColumn.classList.add('text-lg');
            titleColumn.className = 'truncate overflow-hidden whitespace-nowrap pr-8';
            titleColumn.textContent = material.title;

            const typeColumn = document.createElement('p');
            typeColumn.classList.add('text-lg');
            typeColumn.textContent = material.type.toUpperCase();

            newLineDiv.append(idColumn, titleColumn, typeColumn);
            materialTableContentInnerDiv.appendChild(newLineDiv);
        });

        spinner.stop();
        document.getElementById('spinner-container').innerHTML = '';

        materialTableContentInnerDiv.classList.remove('hidden');

    }).catch(err => console.error('Error:', err));
}

function showMaterialDetail(materialId) {
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

    authenticatedFetch('/api/admin/getSpecificMaterialData.php', {
        method: 'POST',
        body: JSON.stringify({ id: materialId })
    })
        .then(res => res.json())
        .then(materialData => {
            spinner.stop();

            globalFilePath = materialData.filePath;
            rightContent.innerHTML = `
                <div class="bg-white w-full h-full rounded-t-xl flex flex-col space-y-12 items-center py-12">
                    <div class="flex flex-col w-full h-full items-center space-y-18 justify-center">
                        <div class="flex flex-col items-center space-y-5">
                            <div class="rounded-full bg-[#173345] w-36 h-36 drop-shadow-lg/45 p-1 flex">
                                <img src="http://192.168.1.118:8080/images/DefaultUserProfilePicture.jpg" 
                                    alt="" class="h-full w-full rounded-full aspect-square object-cover">
                            </div>
                            <div class="flex flex-col items-center space-y-1.5">
                                <h1 class="text-3xl min-w-3xs max-w-2xl truncate overflow-hidden whitespace-nowrap text-center" title="${materialData.title}">${materialData.title}</h1>
                                <p class="font-light text-lg">Tipo de Material: ${materialData.type.toUpperCase()}</p>
                            </div>
                        </div>
                        
                        <div class="w-8/12 flex flex-col drop-shadow-md/25">
                            <div class="bg-[#F4F4F4] w-full border-b border-b-[#DFDFDF] px-6 py-3 rounded-t-2xl">
                                <p class="text-lg">Información General</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Id:</p>
                                <p class="col-span-2">#${materialData.id}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Título:</p>
                                <p class="col-span-2 truncate overflow-hidden whitespace-nowrap" title="${materialData.title}">${materialData.title}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Descripción:</p>
                                <p class="col-span-2 truncate overflow-hidden whitespace-nowrap" title="${materialData.description == '' ? 'N/A' : materialData.description}">${materialData.description == '' ? 'N/A' : materialData.description}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Tipo de Material:</p>
                                <p class="col-span-2">${materialData.type}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] border-b border-b-[#DFDFDF] grid grid-cols-3 px-6 py-4">
                                <p class="font-light col-span-1">Fecha de Creación:</p>
                                <p class="col-span-2">${materialData.uploadedDate}</p>
                            </div>
                            
                            <div class="bg-[#FBFBFB] grid grid-cols-3 px-6 py-4 rounded-b-2xl">
                                <p class="font-light col-span-1">Ruta de Archivos Adjuntos:</p>
                                <p class="col-span-2 truncate overflow-hidden whitespace-nowrap" title="${materialData.filePath}">${materialData.filePath}</p>
                            </div>
                        </div>

                        <div class="flex justify-center gap-8 w-1/2">
                            <button onclick="renderModifyMaterial(${materialData.id}, '${materialData.title}', '${materialData.description}')" class="blue-button interactive w-52 h-18">Modificar Material</button>
                            <button onclick="deleteMaterial(${materialData.id})" class="red-button interactive w-52 h-18">Eliminar Material</button>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            console.error("Ha ocurrido un error: ", err);
            rightContent.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-white text-xl">Error al cargar material</p></div>';
        });
}

async function renderModifyMaterial(materialId, title, description) {
    const rightContent = document.getElementById('right-content');

    globalMaterialId = materialId;

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

    await authenticatedFetch('/api/admin/getFileByMaterial.php', {
        method: 'POST',
        body: JSON.stringify({ id: materialId })
    }).then(res => res.json())
    .then(material => {
        spinner.stop();
        rightContent.innerHTML = `
            <div class="bg-white rounded-t-xl w-full h-full flex items-center justify-center space-y-10 p-10">
            <div class="w-3/4 h-fit flex flex-col px-12 mt-4 space-y-4 items-center">
                <h1 class="text-4xl text-[#1B3B50] font-semibold">Modificar Material</h1>
                <p class="text-[#6A7282]">Modifica la información de un material educativo existente en el sistema Eskua.</p>
                <form id="modify-material-form" class="flex flex-col w-full h-full space-x-8 space-y-6 py-4">\
                    <div class="flex flex-col space-y-4 w-full">
                        <h2 class="text-xl font-medium text-[#1B3B50]">Título del Material</h2>
                        <input
                        type="text"
                        placeholder="Título..."
                        class="w-full py-3 pl-4 rounded-xl border-0 shadow-md/30 focus:ring-2 focus:ring-[#E1A05B] transition duration-150 bg-[#FBFBFB]"
                        required
                        id="title-input"
                        maxlength="128"
                        value="${title}"
                        />
                    </div>
                    <div class="flex flex-col gap-3.5 w-full">
                        <h2 class="text-xl font-medium text-[#1B3B50] mb-1">Descripción</h2>
                        <textarea
                            placeholder="Agrega una descripción..."
                            class="w-full h-40 p-4 rounded-xl shadow-md/30 focus:ring-2 focus:ring-[#E1A05B]
                                transition duration-150 bg-[#FBFBFB] resize-none 
                                text-gray-700 font-light placeholder-gray-400 placeholder:font-light"
                            id="description-input"
                        >${description}</textarea>
                    </div>
                    <div class="flex flex-col space-y-5 w-full">
                        <div class="flex flex-col space-y-0.5">
                            <div>
                                <h2 class="text-xl font-medium text-[#1B3B50]">Adjuntar Archivos</h2>
                                <img src="" alt="">
                            </div>
                            <p class="text-[#A3A3A3] text-sm font-light">Elige cualquier archivo desde tu dispositivo para subir.</p>
                        </div>
                        <div class="flex space-x-5 w-full">
                            <label id="material-file-label" onclick="notifyAlert('error', 'Primero elimina el archivo')" for="material-file" class="flex flex-col space-y-1 border-2 border-dotted border-[#A3A3A3] py-4 px-12 rounded-2xl items-center shadow-md/30 hover:bg-[#fafafa] w-1/2 interactive">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" 
                                    fill="none" stroke="#3550BA" stroke-width="2" 
                                    stroke-linecap="round" stroke-linejoin="round" 
                                    class="w-6 h-6 mx-auto">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 5 17 10"/>
                                    <line x1="12" y1="5" x2="12" y2="15"/>
                                </svg>
                                <p class="font-light text-sm text-[#3550BA]">Abrir el explorador de archivos</p>
                                <p class="font-light text-[12px] text-[#A3A3A3]">PDF, MP4, PNG, JPG, WEBP</p>
                            </label>

                            <input 
                                id="material-file" 
                                type="file" 
                                accept=".pdf,.mp4,.png,.jpg,.jpeg,.webp"
                                class="hidden" 
                                onchange="addNewFile(event)"
                                disabled
                            />
                            <div id="files-to-upload" class="w-1/2" >
                                <div class="flex flex-col space-y-1 justify-center bg-[#fafafa] p-4 rounded-2xl shadow-md/30 h-full">
                                    <div class="flex justify-between items-center space-x-25">
                                        <p id="file-name" class="text-sm font-light">${material.fileOriginalName}</p>
                                        <svg onclick="enableNewFile(); globalHasChangedFile = true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="3" stroke="#A3A3A3" class="w-6 h-6 interactive hover:stroke-[#CC4033] transition duration-150">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm text-[#A3A3A3] font-light" id="time-remaining">${(material.fileSize / 1024).toFixed(1)} KB</p>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>
                    <div class="flex space-x-5 w-full">
                        <button type="submit" class="blue-button py-3.5 w-1/2">Modificar Material</button>
                        <button type="button" onclick="location.reload(); globalHasChangedFile = false" class="red-button py-3.5 w-1/2">Cancelar</button>
                    </div>
                </form>
            </div>
        `;
    }).catch(err => console.error('Error ' + err));

    document.getElementById('modify-material-form').addEventListener('submit', modifyMaterial);
}

function modifyMaterial(e) {
    e.preventDefault();

    const fileInput = document.getElementById('material-file');
    const file = fileInput.files[0];

    const titleInput = document.getElementById('title-input');
    const descriptionInput = document.getElementById('description-input');

    const formData = new FormData();

    if (globalHasChangedFile) formData.append('file', file);
    formData.append('hasChangedFile', globalHasChangedFile);
    formData.append('filePath', globalFilePath);
    formData.append('title', titleInput.value);
    formData.append('description', descriptionInput.value);
    formData.append('id', globalMaterialId);

    authenticatedFetch('/api/admin/modifyMaterial.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
    .then(data => {
        console.log(data);
        if (data.ok) {
            notifyAlert('success', data.message)
            renderMaterialsTable();
        } else {
            notifyAlert('error', data.error);
        }
    }).catch(err => console.error('Error ' + err));
}

function enableNewFile() {
    document.getElementById('material-file-label').onclick = '';
    document.getElementById('files-to-upload').innerHTML = '';

    document.getElementById('material-file').disabled = false;
}

function notifyAlert(type, message, duration = 3500, x = 'right', y = 'top', closeable = true) {
    const notyf = new Notyf({
        duration: duration,
        position: { x: x, y: y },
        dismissible: closeable
    });

    if (type === 'success') {
        notyf.success(message);
    } else {
        notyf.error(message);
    }
}

function deleteMaterial(materialId) {

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
            authenticatedFetch('/api/admin/deleteMaterial.php', {
                method: 'DELETE',
                body: JSON.stringify({ id: materialId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        renderMaterialsTable();
                        notyf.success(data.message);
                    } else {
                        notyf.error(data.message);
                    }
                }).catch(err => console.error("Error: ", err));
        } else {
            notyf.error('La acción ha sido cancelada');
        }
    });
}

function renderCreateMaterial() {
    const rightContent = document.getElementById('right-content');

    rightContent.innerHTML = `
        <div class="bg-white rounded-t-xl w-full h-full flex items-center justify-center space-y-10 p-10">
        <div class="w-3/4 h-fit flex flex-col px-12 mt-4 space-y-4 items-center">
            <h1 class="text-4xl text-[#1B3B50] font-semibold">Crear Material</h1>
            <form id="create-material-form" class="flex flex-col w-full h-full space-x-8 space-y-6 py-4">\
                <div class="flex flex-col space-y-4 w-full">
                    <h2 class="text-2xl font-light">Título del Material</h2>
                    <input
                      type="text"
                      placeholder="Título..."
                      class="w-full py-3 pl-4 rounded-xl border-0 shadow-md/30 focus:ring-2 focus:ring-[#E1A05B] transition duration-150 bg-[#FBFBFB]"
                      required
                      id="title-input"
                      maxlength="128"
                    />
                </div>
                <div class="flex flex-col gap-3.5 w-full">
                    <h2 class="text-2xl font-light mb-1">Descripción</h2>
                    <textarea
                        placeholder="Agrega una descripción..."
                        class="w-full h-40 p-4 rounded-xl shadow-md/30 focus:ring-2 focus:ring-[#E1A05B]
                            transition duration-150 bg-[#FBFBFB] resize-none 
                            text-gray-700 font-light placeholder-gray-400 placeholder:font-light"
                        id="description-input"
                    ></textarea>
                </div>
                <div class="flex flex-col space-y-5 w-full">
                    <div class="flex flex-col space-y-0.5">
                        <div>
                            <h2 class="text-2xl font-light">Adjuntar Archivos</h2>
                            <img src="" alt="">
                        </div>
                        <p class="text-[#A3A3A3] text-sm font-light">Elige cualquier archivo desde tu dispositivo para subir.</p>
                    </div>
                    <div class="flex space-x-5 w-full">
                        <label id="material-file-label" for="material-file" class="flex flex-col space-y-1 border-2 border-dotted border-[#A3A3A3] py-4 px-12 rounded-2xl items-center shadow-md/30 hover:bg-[#fafafa] w-1/2 interactive">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" 
                                fill="none" stroke="#3550BA" stroke-width="2" 
                                stroke-linecap="round" stroke-linejoin="round" 
                                class="w-6 h-6 mx-auto">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 5 17 10"/>
                                <line x1="12" y1="5" x2="12" y2="15"/>
                            </svg>
                            <p class="font-light text-sm text-[#3550BA]">Abrir el explorador de archivos</p>
                            <p class="font-light text-[12px] text-[#A3A3A3]">PDF, MP4, PNG, JPG, WEBP</p>
                        </label>

                        <input 
                            id="material-file" 
                            type="file" 
                            accept=".pdf,.mp4,.png,.jpg,.jpeg,.webp"
                            class="hidden" 
                            onchange="addNewFile(event)"
                        />
                        <div id="files-to-upload" class="w-1/2" ></div>
                    </div>   
                </div>
                <div class="flex space-x-5 w-full">
                    <button type="submit" class="blue-button py-3.5 w-1/2">Crear Material</button>
                    <button type="button" onclick="location.reload()" class="red-button py-3.5 w-1/2">Cancelar</button>
                </div>
            </form>
        </div>
    `;

    document.getElementById('create-material-form').addEventListener('submit', OnCreateElement);
}

let uploadsCompleted = 0;
let totalUploads = 0;
let uploadedFiles = [];

function addNewFile(event) {
    const files = event.target.files;
    if (!files.length) return;

    document.getElementById('material-file').disabled = true;
    document.getElementById('material-file-label').onclick = () => notifyAlert('error', 'Primero elimina el archivo');

    const uploadStatus = document.getElementById('files-to-upload');
    uploadsCompleted = 0;
    totalUploads = files.length;
    uploadedFiles = [];

    uploadStatus.innerHTML = '';

    Array.from(files).forEach(file => {
        const newFileDiv = document.createElement('div');
        newFileDiv.className = 'flex flex-col space-y-1 justify-center bg-[#fafafa] p-4 rounded-2xl shadow-md/30 h-full';

        newFileDiv.innerHTML = `
            <div class="flex justify-between items-center space-x-25">
                <p id="file-name" class="text-sm font-light">${file.name}</p>
                <svg onclick="enableNewFile()" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="3" stroke="#A3A3A3" class="w-6 h-6 interactive hover:stroke-[#CC4033] transition duration-150">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="flex justify-between items-center">
                <p class="text-sm text-[#A3A3A3] font-light" id="time-remaining">${(file.size / 1024).toFixed(1)} KB</p>
            </div>
        `;

        uploadStatus.appendChild(newFileDiv);
    });
}

function OnCreateElement(e) {
    e.preventDefault();

    const fileInput = document.getElementById('material-file');
    const file = fileInput.files[0];

    const titleInput = document.getElementById('title-input');
    const descriptionInput = document.getElementById('description-input');

    const formData = new FormData();

    formData.append('file', file);
    formData.append('title', titleInput.value);
    formData.append('description', descriptionInput.value);

    const notyf = new Notyf({
        duration: 3500,
        position: { x: 'right', y: 'top' },
        dismissible: true
    });

    authenticatedFetch('/api/admin/uploadPublicMaterialFile.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
    .then(data => {
        if (data.ok) {
            renderMaterialsTable();
            notyf.success(data.message);
        } else {
            notyf.error(data.error);
        }
    }).catch(err => console.error("Error: ", err));
}