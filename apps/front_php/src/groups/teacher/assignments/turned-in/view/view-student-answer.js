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
    loadStudentAnswer();
});

async function loadStudentAnswer() {
    const urlParams = new URLSearchParams(window.location.search);
    const answerId = urlParams.get('answerId');

    const infoContainer = document.getElementById('info-container');
    const fileContainer = document.getElementById('file-container');

    const assignmentName = document.getElementById('assignment-name');
    const studentName = document.getElementById('student-name');
    const submittedDate = document.getElementById('submitted-date');
    const studentMessage = document.getElementById('student-text');

    authenticatedFetch('/api/teacher/getSpecificStudentAnswer.php', {
        method: 'POST',
        body: JSON.stringify({ answerId: answerId })
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                notifyAlert('error', data.error);
                return;
            }

            console.log(data);

            const answer = data.answer;

            assignmentName.innerText = answer.assignmentName;
            studentName.innerText = answer.studentDisplayName;

            submittedDate.innerText = formatSubmittedDate(answer.submittedDate);

            studentMessage.innerText = answer.studentTextResponse;

            if (answer.fileOriginalName) {
                const file = document.createElement('a');
                file.className = 'w-full flex p-4 border-b-2 border-[#DFDFDF] items-center justify-between hover:bg-[#F2F2F2] transition duration-100 interactive no-underline';
                file.href = '/' + answer.filePath;
                file.target = '_blank';

                file.innerHTML = `
                    <div class="flex space-x-5 items-center no-underline">
                        <div class="flex space-x-5 items-center">
                            <img src="/images/AssignmentIcon.svg" alt="">
                            <p class="text-[#1B3B50]">${answer.fileOriginalName}</p>
                        </div>
                    </div>
                    <!-- FILE SIZE -->
                    <p class="text-[#6A7282]">${formatFileSize(answer.fileSize)}</p>
                `;

                fileContainer.append(file);
            }

            spinner.stop();

            infoContainer.classList.remove('hidden');
        }).catch(err => console.error("Error: ", err));
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

function formatSubmittedDate(submittedDateStr) {
    if (!submittedDateStr) return '';

    const iso = submittedDateStr.replace(' ', 'T');
    const d = new Date(iso);
    if (isNaN(d)) return submittedDateStr;

    const pad = n => String(n).padStart(2, '0');
    const hora = `${pad(d.getHours())}:${pad(d.getMinutes())}`;

    return `Entregada el ${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${String(d.getFullYear()).slice(-2)} a las ${hora}`;
}