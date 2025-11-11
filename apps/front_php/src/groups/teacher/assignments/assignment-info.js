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
});

async function loadTask() {
    const urlParams = new URLSearchParams(window.location.search);
    const groupId = urlParams.get('groupId');
    const taskId = urlParams.get('taskId');

    const assignmentInfo = document.getElementById('assignment-info');
    const fileContainer = document.getElementById('file-container');

    const name = document.getElementById('name');
    const description = document.getElementById('description');
    const maxScore = document.getElementById('max-score');
    const dueDate = document.getElementById('due-date');

    if ((taskId === undefined || taskId === null) || (groupId === undefined || groupId === null)) {
        window.location = `/groups/teacher/assignments/?groupId=${recivedGroupId}`;
    }

    authenticatedFetch('/api/group/getSpecificAssignment.php', {
        method: 'POST',
        body: JSON.stringify({ taskId: taskId })
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                console.error('Hubo un error al cargar la tarea');
            }

            const task = data.task;

            name.innerText = task.name;

            description.innerText = task.description;

            maxScore.innerText = task.maxScore + ' puntos';

            dueDate.innerText = task.dueDate;

            if (task.originalName) {
                fileContainer.innerHTML = `
                    <a
                    href="/${task.filePath}" target="_blank"
                    class="w-full flex p-4 border-b-2 border-[#DFDFDF] items-center space-x-5 hover:bg-[#F2F2F2] transition duration-100 interactive no-underline">
                    <img src="/images/AssignmentIcon.svg" alt="">
                    <p class="text-[#1B3B50]">${task.originalName}</p>
                    </a>
                `;
            } else {
                const text = document.createElement('p');
                text.innerText = 'No hay archivos adjuntos';
                text.className = 'text-center mt-4';
                fileContainer.append(text);
            }

            spinner.stop();

            assignmentInfo.classList.remove('hidden');

        }).catch(err => console.error("Error: ", err));
}

function deactivateAssignment() {
    const urlParams = new URLSearchParams(window.location.search);
    const taskId = urlParams.get('taskId');
    const groupId = urlParams.get('groupId');

    const notyf = new Notyf({
        duration: 3500,
        position: { x: 'right', y: 'top' },
        dismissible: true
    });

    authenticatedFetch('/api/teacher/deactivateAssignment.php', {
        method: 'POST',
        body: JSON.stringify({ taskId: taskId })
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                notyf.error(data.error);
            }

            showSuccess(data.message, () => {
                window.location = `/groups/teacher/assignments/?groupId=${groupId}`;
            });

        }).catch(err => console.error(err));
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

function modifyAssignment() {
    const urlParams = new URLSearchParams(window.location.search);
    const taskId = urlParams.get('taskId');
    const groupId = urlParams.get('groupId');

    window.location = `/groups/teacher/assignments/modify-assignment.html?taskId=${taskId}&groupId=${groupId}`;
}

function seeTurnedInAssignment() {
    const urlParams = new URLSearchParams(window.location.search);
    const taskId = urlParams.get('taskId');
    const groupId = urlParams.get('groupId');

    window.location = `/groups/teacher/assignments/turned-in/?taskId=${taskId}&groupId=${groupId}`;
}