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
    spinner = showSpinner();
    loadMaterialInfo();
});

async function loadMaterialInfo() {
    const infoContainer = document.getElementById('material-info-container');

    const urlParams = new URLSearchParams(window.location.search);
    const materialId = urlParams.get('materialId');

    const name = document.getElementById('name');
    const description = document.getElementById('description');
    const type = document.getElementById('type');
    const creationDate = document.getElementById('creation-date');
    const fileContainer = document.getElementById('file-container');
    const downloadButton = document.getElementById('download-button');

    authenticatedFetch('/api/user/getSpecificMaterialInfo.php', {
        method: 'POST',
        body: JSON.stringify({ materialId: materialId })
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                console.error('Error cargando el material');
                return;
            }

            const material = data.material;

            name.innerText = material.name;
            name.title = material.name;

            description.innerText = material.description !== '' ? material.description : 'N/A';

            type.innerText = material.type == 'mp4' ? 'Video' : (material.type == 'png' || material.type == 'jpg' || material.type == 'webp' || material.type == 'jpeg') ? 'Foto' : 'PDF';

            creationDate.innerText = 'Creado el ' + material.createdDate;

            fileContainer.innerHTML = `
                <a href="${data.filePath}" target="_blank" class="w-full flex p-4 border-b-2 border-[#DFDFDF] items-center space-x-5 hover:bg-[#F2F2F2] transition duration-100 interactive no-underline" title="${material.originalName}">
                  <img src="../../../../../images/AssignmentIcon.svg" alt="${material.originalName}">
                  <p class="text-[#1B3B50]">${material.originalName}</p>
                </a>
            `;

            downloadButton.download = material.originalName;
            downloadButton.href = data.filePath;
            spinner.stop();
            infoContainer.classList.remove('hidden');
        }).catch(err => {
            console.error("Error: ", err);
            window.location = '/general/material/';
        });
}