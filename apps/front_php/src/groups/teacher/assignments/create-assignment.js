let globalHasChangedFile = false;

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('create-material-form').addEventListener('submit', OnCreateAssignment);
});

async function OnCreateAssignment(e) {
    e.preventDefault();

    const notyf = new Notyf({
        duration: 3500,
        position: { x: 'right', y: 'top' },
        dismissible: false
    });

    const fileInput = document.getElementById('material-file');
    const file = fileInput.files[0];

    const titleInput = document.getElementById('title-input');
    const descriptionInput = document.getElementById('description-input');
    const maxScoreInput = document.getElementById('max-score-input');
    const calendarInput = document.getElementById('deadline');

    const formData = new FormData();
    formData.append('file', file);
    formData.append('title', titleInput.value);
    formData.append('description', descriptionInput.value);
    formData.append('maxSocre', maxScoreInput.value);
    formData.append('dueDate', calendarInput.value);

    if (titleInput.value === '' || descriptionInput.value === '') {
        notyf.error('El nombre y la descripcion son obligatorios');
        return;
    }

    authenticatedFetch('/api/teacher/createAssignment.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
    .then(data => {
        if (!data.ok) {
            notyf.error('Error al crear la tarea');
            return;
        }

    }).catch(err => console.error("Error: ", err));

}