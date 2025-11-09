let spinner;

let globalHasChangedFile = false;
let globalFilePath = '';

function showSpinner(spinnerColor = '#1B3B50', spinnerPosition = 'relative', spinnerTop = '70%', spinnerLeft = '60%') {
    // Spinner config
    const opts = {
        lines: 12,            // Lines number
        length: 7,            // Lenght of each line
        width: 5,             // Widht of the line
        radius: 10,           // Inner radius of the circle
        scale: 1.0,           // Spinner scale
        color: spinnerColor,        // Color
        opacity: 0.25,        // Lines opacity
        rotate: 0,            // Initial rotation
        direction: 1,         // 1: clockwise, -1: anti-clockwise
        speed: 1,             // Spins per second
        trail: 60,            // After the trail (%)
        fps: 20,              // fps
        zIndex: 2e9,          // z-index
        className: 'spinner', // Assinged CSS class
        top: spinnerTop,           // Relative right position from the container
        left: spinnerLeft,          // Relative left position from the container
        shadow: false,        // Shadow
        position: spinnerPosition  // Position CSS
    };

    const spinnerContainer = document.getElementById('spinner-container');
    spinnerContainer.innerHTML += '<div id="spinner"></div>';

    const target = document.getElementById('spinner');

    return new Spinner(opts).spin(target);
}

document.addEventListener('DOMContentLoaded', () => {
    spinner = showSpinner();
    loadTask();
});

async function loadTask() {
    const urlParams = new URLSearchParams(window.location.search);
    const taskId = urlParams.get('taskId');

    const infoContainer = document.getElementById('info-container');

    const titleInput = document.getElementById('title-input');
    const descriptionInput = document.getElementById('description-input');
    const maxScore = document.getElementById('max-score-input');
    const dueDate = document.getElementById('deadline');
    const fileContainer = document.getElementById('files-to-upload');
    const fileInput = document.getElementById('file-input');

    authenticatedFetch('/api/group/getSpecificAssignment.php', {
        method: 'POST',
        body: JSON.stringify({ taskId: taskId })
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                console.error(data.error);
            }

            console.log(data.task);

            const assignment = data.task;

            titleInput.value = assignment.name;
            descriptionInput.value = assignment.description;
            maxScore.value = assignment.maxScore;

            const dueDateParts = assignment.dueDate.split('/');
            const day = dueDateParts[0];
            const month = dueDateParts[1];
            const shortYear = dueDateParts[2].slice(-2);
            const newDueDate = [day, month, shortYear].join('-');
            dueDate.value = newDueDate;

            if (assignment.originalName) {
                fileInput.disabled = true;

                document.getElementById('file-input-label').onclick = () => {
                    notifyAlert('error', 'Para agregar otro archivo debes de borrar el anterior');
                };

                const file = document.createElement('div');
                file.className = 'flex flex-col space-y-1 justify-center bg-[#fafafa] p-4 rounded-2xl shadow-md/30 h-full';

                file.innerHTML = `
                    <div class="flex justify-between items-center space-x-25">
                      <p id="file-name" class="text-sm font-light">${assignment.originalName}</p>
                      <svg onclick="enableNewFile(); globalHasChangedFile = true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                        stroke="#A3A3A3"
                        class="w-6 h-6 interactive hover:stroke-[#CC4033] transition duration-150">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </div>
                    <div class="flex justify-between items-center">
                      <p class="text-sm text-[#A3A3A3] font-light" id="time-remaining">${assignment.size >= 1000 ? (assignment.size / 1024).toFixed(1) + ' MB' : assignment.size + ' KB'}</p>
                    </div>
                `;

                fileContainer.append(file);
            }

            globalFilePath = assignment.filePath;

            spinner.stop();
            infoContainer.classList.remove('hidden');
        }).catch(err => console.error("Error: ", err));
}

function addNewFile(event) {
    const files = event.target.files;
    if (!files.length) return;

    document.getElementById('file-input').disabled = true;
    document.getElementById('file-input-label').onclick = () => notifyAlert('error', 'Primero elimina el archivo');

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
                <p class="text-sm text-[#A3A3A3] font-light" id="time-remaining">${file.size >= 1000 ? (file.size / 1024).toFixed(1) + ' MB' : file.size + ' KB'}</p>
            </div>
        `;

        uploadStatus.appendChild(newFileDiv);
    });
}

function enableNewFile() {
    document.getElementById('file-input-label').onclick = '';
    document.getElementById('files-to-upload').innerHTML = '';

    document.getElementById('file-input').disabled = false;
}

function notifyAlert(type, message, duration = 1500, x = 'right', y = 'top', closeable = false) {
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

async function modifyTask() {

    const urlParams = new URLSearchParams(window.location.search);
    const groupId = urlParams.get('groupId');
    const taskId = urlParams.get('taskId');

    const titleInput = document.getElementById('title-input');
    const descriptionInput = document.getElementById('description-input');
    const maxScoreInput = document.getElementById('max-score-input');
    const dueDateInput = document.getElementById('deadline');
    const fileInput = document.getElementById('file-input');
    const file = fileInput.files.length > 0 ? fileInput.files[0] : null;

    const formData = new FormData();

    formData.append('file', file);
    formData.append('title', titleInput.value);
    formData.append('description', descriptionInput.value);
    formData.append('maxScore', maxScoreInput.value)
    formData.append('dueDate', dueDateInput.value)
    formData.append('groupId', groupId);
    formData.append('taskId', taskId);
    formData.append('hasChangedFile', globalHasChangedFile);
    formData.append('filePath', globalFilePath);

    authenticatedFetch('/api/teacher/modifyAssignment.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                notifyAlert('error', data.error);
                return;
            }

            notyfAlertPlusEvent('success', data.message, () => {
                window.location = `/groups/teacher/assignments/?groupId=${groupId}`;
            });
        }).catch(err => console.error("Error: ", err));
}

function notyfAlertPlusEvent(type, message, onDismiss) {
    let n;

    const duration = 750;
    const notyf = new Notyf({
        duration,
        position: { x: 'right', y: 'top' }
    });

    if (type === 'success') {
        n = notyf.success(message);
    } else {
        n = notyf.error(message);
    }


    // If the users close's the notification
    if (n && typeof n.on === 'function') {
        n.on('dismiss', () => {
            if (onDismiss) onDismiss();
        });
    }

    if (onDismiss) {
        setTimeout(() => {
            onDismiss();
        }, duration);
    }
}