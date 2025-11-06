let globalHasChangedFile = false;

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('create-material-form').addEventListener('submit', OnCreateAssignment);
});

async function OnCreateAssignment(e) {
    e.preventDefault();

    const fileInput = document.getElementById('material-file');
    const file = fileInput.files[0];

    const titleInput = document.getElementById('title-input');
    const descriptionInput = document.getElementById('description-input');
    const maxScoreInput = document.getElementById('max-score-input');
    const calendarInput = document.getElementById('deadline');

    const urlParams = new URLSearchParams(window.location.search);
    const groupId = urlParams.get('groupId');

    const title = titleInput.value;
    const description = descriptionInput.value;
    const maxScore = maxScoreInput.value;
    const dueDate = calendarInput.value;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('title', title);
    formData.append('description', description);
    formData.append('maxScore', maxScore);
    formData.append('dueDate', dueDate);
    formData.append('groupId', groupId);

    authenticatedFetch('/api/teacher/createAssignment.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
    .then(data => {
        if (!data.ok) {
            let i = 1;
            let errorMsg = [];
            data.error.forEach(error => {
                if (i === 1) {
                    errorMsg.push(error);
                } else {
                    errorMsg.push(' ' + error.toLowerCase());
                }
                i++;
            });
            Swal.fire({
                title: 'ERROR AL CREAR TAREA',
                text: errorMsg,
                icon: 'error',
                confirmButtonText: 'Ok',
            })
            return;
        }

        showSuccess(data.message, () => {
            window.location = `/groups/teacher/assignments/?groupId=${groupId}`;
        });

    }).catch(err => console.error("Error: ", err));

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

function enableNewFile() {
    document.getElementById('material-file-label').onclick = '';
    document.getElementById('files-to-upload').innerHTML = '';

    document.getElementById('material-file').disabled = false;
    document.getElementById('material-file').value = '';
}