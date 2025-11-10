let spinner;

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

    document.getElementById('turn-in-form').addEventListener('submit', TurnInAssignment);
});

async function loadTask() {
    const infoContainer = document.getElementById('info-container');

    const urlParams = new URLSearchParams(window.location.search);
    const taskId = urlParams.get('taskId');

    const title = document.getElementById('title');
    const description = document.getElementById('description');
    const maxScore = document.getElementById('max-score');
    const dueDate = document.getElementById('due-date');

    const fileContainer = document.getElementById('file-container');

    authenticatedFetch('/api/group/getSpecificAssignment.php', {
        method: 'POST',
        body: JSON.stringify({ taskId: taskId })
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                let turnedInAssignments = localStorage.getItem('turnedInAssignments');

                if (turnedInAssignments !== null) {
                    try {
                        turnedInAssignments = JSON.parse(turnedInAssignments);
                        turnedInAssignments = turnedInAssignments.filter(id => id !== taskId);
                        localStorage.setItem('turnedInAssignments', JSON.stringify(turnedInAssignments));
                    } catch (err) {
                        // Si el parse falla, limpiamos la clave para no dejar basura
                        localStorage.removeItem('turnedInAssignments');
                    }
                }

                notifyAlert('error', data.error);
                return;
            }

            const task = data.task;

            title.innerText = task.name;
            title.title = task.name;
            description.innerText = task.description;
            maxScore.innerText = task.maxScore + ' puntos';
            dueDate.innerText = task.dueDate;

            if (task.originalName) {
                const file = document.createElement('div')
                file.innerHTML = `
                    <a
                      href="/${task.filePath}" target="_blank"
                      class="no-underline w-full flex p-4 border-b-2 border-[#DFDFDF] items-center space-x-5 hover:bg-[#F2F2F2] transition duration-100 interactive">
                      <img src="/images/AssignmentIcon.svg" alt="">
                      <p class="text-[#1B3B50]" id="file-name">${task.originalName}</p>
                    </a>
                `;

                fileContainer.append(file);
            } else {
                const text = document.createElement('p');
                text.innerText = 'No tiene archivos adjuntos';
                text.className = 'text-center mt-4';

                fileContainer.append(text);
            }

            let turnedInAssignments = localStorage.getItem('turnedInAssignments');

            if (turnedInAssignments !== null) {
                try {
                    turnedInAssignments = JSON.parse(turnedInAssignments);
                } catch (err) {
                    turnedInAssignments = [];
                    localStorage.removeItem('turnedInAssignments');
                }

                if (Array.isArray(turnedInAssignments) && turnedInAssignments.includes(taskId)) {
                    const showPopupButton = document.getElementById('show-popup-button');
                    showPopupButton.onclick = '';

                    showPopupButton.disabled = true;
                    showPopupButton.innerText = 'Entregada';
                    showPopupButton.className = 'turned-in-button h-15 w-1/4';
                }
            }

            if (spinner && typeof spinner.stop === 'function') spinner.stop();

            infoContainer.classList.remove('hidden');
        }).catch(err => {
            console.error("Error: ", err);
            if (spinner && typeof spinner.stop === 'function') spinner.stop();
        });
}

function showTurnInAssignmentPopup() {
    const popup = document.getElementById('turn-in-popup');
    const backArrow = document.getElementById('back-arrow');

    popup.classList.remove('hidden');
    backArrow.onclick = () => {
        popup.classList.add('hidden');
    };
}

function addNewFile(event) {
    const files = event.target.files;
    if (!files.length) return;

    document.getElementById('file-input').disabled = true;
    document.getElementById('file-input-label').onclick = () => notifyAlert('error', 'Solo se puede subir un archivo');

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
                <p class="text-sm text-[#A3A3A3] font-light" id="time-remaining">${formatFileSize(file.size)}</p>
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


function notifyAlert(type, message, duration = 1500, closeable = false, x = 'right', y = 'top') {
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

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    else if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    else if (bytes < 1024 * 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    else return (bytes / (1024 * 1024 * 1024)).toFixed(1) + ' GB';
}

async function TurnInAssignment(e) {
    e.preventDefault();

    const submitTurnInAssignment = document.getElementById('submit-turn-in-assignment');
    submitTurnInAssignment.disabled = true;
    submitTurnInAssignment.className = 'orange-button-disabled h-full px-14';

    const urlParams = new URLSearchParams(window.location.search);
    const groupId = urlParams.get('groupId');
    const taskId = urlParams.get('taskId');
    
    const textInput = document.getElementById('text-message');

    const fileInput = document.getElementById('file-input');
    const file = fileInput.files.length > 0 ? fileInput.files[0] : null;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('text', textInput.value);
    formData.append('taskId', taskId);

    let turnedInAssignments = localStorage.getItem('turnedInAssignments');
    if (turnedInAssignments === null) {
        turnedInAssignments = [];
    } else {
        try {
            turnedInAssignments = JSON.parse(turnedInAssignments);
            if (!Array.isArray(turnedInAssignments)) turnedInAssignments = [];
        } catch (err) {
            turnedInAssignments = [];
        }
    }

    authenticatedFetch('/api/student/turnInAssignment.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
    .then(data => {
        if (!data.ok) {
            notifyAlert('error', data.error);
            submitTurnInAssignment.disabled = false;
            submitTurnInAssignment.className = 'orange-button h-full px-14';
            return;
        }

        turnedInAssignments.push(taskId);
        localStorage.setItem('turnedInAssignments', JSON.stringify(turnedInAssignments));

        showSuccess(data.message, () => {
            window.location = `/groups/student/assignments/?groupId=${groupId}`;
        });
    }).catch(err => {
        console.error("Error: ", err);
        notifyAlert('error', 'Error de red al intentar entregar. Intenta nuevamente.');
        submitTurnInAssignment.disabled = false;
        submitTurnInAssignment.className = 'orange-button h-full px-14';
    });
}


function showSuccess(message, onDismiss) {
    const duration = 750;
    const notyf = new Notyf({
        duration,
        position: { x: 'right', y: 'top' }
    });

    const n = notyf.success(message);

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