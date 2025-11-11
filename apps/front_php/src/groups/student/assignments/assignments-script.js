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
    loadTasks();
});

async function loadTasks() {
    const assignmentsTable = document.getElementById('assignments-table');
    const assignmentsContainer = document.getElementById('assignments-container');

    const urlParams = new URLSearchParams(window.location.search);
    const groupId = urlParams.get('groupId');

    if (groupId === undefined || groupId === null || groupId.length === 0 || groupId === "") {
        window.location = '/';
        return;
    }

    assignmentsTable.classList.add('flex', 'flex-col', 'justify-center', 'items-center');

    assignmentsContainer.innerHTML = '';
    assignmentsContainer.className = 'hidden';

    authenticatedFetch('/api/student/getAssignmentsFromGroup.php', {
        method: 'POST',
        body: JSON.stringify({ id: groupId })
    }).then(res => res.json())
        .then(data => {
            if (!data.ok) {
                const text = document.createElement('p');
                text.textContent = 'No hay tareas';
                text.className = 'text-center mt-6';

                assignmentsTable.append(text);
                return;
            }

            data.tasks.forEach(task => {
                const taskInfo = task[0];
                const newTask = document.createElement('a');
                newTask.className = 'w-full h-[120px] border-b-2 border-[#DFDFDF] hover:bg-[#F2F2F2] flex items-center pl-10 transition duration-150 interactive shrink-0 no-underline';

                const turnedIn = Boolean(taskInfo.turnedIn);
                let importantInfoTask = '<p class="text-[#CC4033]">Vence el ' + taskInfo.dueDate + '</p>';
                if (!turnedIn) {
                    if (!task.isOverdue) {
                        newTask.href = `/groups/student/assignments/info/?taskId=${taskInfo.id}&groupId=${groupId}`;
                    } else {
                        newTask.href = '#';
                        newTask.onclick = () => {
                            notifyAlert('error', 'La tarea ya está vencida');
                        };
                        importantInfoTask = '<p class="text-[#CC4033]">Vencida</p>';
                    }
                } else {
                    // The student turned in the assignment :)
                    newTask.href = `/groups/student/assignments/info/?taskId=${taskInfo.id}&groupId=${groupId}`;
                    importantInfoTask = '<p class="text-[#28C98E]">Entregada</p>';
                }

                newTask.innerHTML = `
                            <div class="flex w-full justify-between pr-10">
                                <div class="flex items-center space-x-5">
                                <img src="/images/Assignment.webp" alt="" class="w-20 h-20 shadow-md/25 rounded-md object-cover">
                                <div class="flex flex-col justify-center w-full">
                                    <p class="text-xl font-medium text-[#1B3B50] max-w-[25vw] truncate">${taskInfo.name}</p>
                                    <p class="text-base/5 text-[#6A7282] max-w-[30vw] truncate">${taskInfo.description}</p>
                                </div>
                                </div>
                                <div class="flex flex-col items-end space-y-10">
                                <p class="text-[#6A7282]">${taskInfo.maxScore}</p>
                                ${importantInfoTask}
                                </div>
                            </div>
                        `;
                assignmentsContainer.append(newTask);
            });

            spinner.stop();

            assignmentsTable.classList.remove('flex', 'flex-col', 'justify-center', 'items-center');
            assignmentsContainer.classList.remove('hidden');

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