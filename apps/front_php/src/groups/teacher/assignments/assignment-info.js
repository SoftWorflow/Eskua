let spinner;

document.addEventListener('DOMContentLoaded', () => {
    loadTask();
});

async function loadTask() {
    const urlParams = new URLSearchParams(window.location.search);
    const groupId = urlParams.get('groupId');
    const taskId = urlParams.get('taskId');

    if ((taskId === undefined || taskId === null) || (groupId === undefined || groupId === null)) {
        window.location = `/groups/teacher/assignments/?groupId=${recivedGroupId}`;
    }

    authenticatedFetch('/api/teacher/getSpecificAssignment.php', {
        method: 'POST',
        body: JSON.stringify({taskId: taskId})
    }).then(res => res.json())
    .then(data => {
        if (!data.ok) {
            console.error('Hubo un error al cargar la tarea');
        }
    }).catch(err => console.error("Error: ", err))
}