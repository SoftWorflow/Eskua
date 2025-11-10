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
    loadStudentAnswers();
});

async function loadStudentAnswers() {
    const urlParams = new URLSearchParams(window.location.search);
    const taskId = urlParams.get('taskId');

    const infoContainer = document.getElementById('info-container');
    const studentAnswersTable = document.getElementById('student-answers-table');
    
    authenticatedFetch('/api/teacher/getStudentsAssignmentAnswers.php', {
        method: 'POST',
        body: JSON.stringify({ taskId: taskId })
    }).then(res => res.json())
    .then(data => {
        if (!data.ok) {
            const text = document.createElement('p');
            text.innerText = data.error;
            text.className = 'text-center mt-6';

            studentAnswersTable.innerHTML = '';
            studentAnswersTable.append(text);

            spinner.stop();

            infoContainer.classList.remove('hidden');
            return;
        }

        data.answers.forEach(answer => {
            const card = document.createElement('a');
            card.className = 'no-underline interactive';
            card.href = '#';
            card.innerHTML = `
                <div
                  class="w-full h-[120px] border-b-2 border-[#DFDFDF] hover:bg-[#F2F2F2] flex items-center pl-10 transition duration-150 interactive shrink-0">
                  <div class="w-full flex justify-between items-center pr-8">
                    <div class="flex space-x-5">
                      <img src="/images/DefaultUserProfilePicture.webp" alt=""
                        class="w-20 h-20 shadow-md/25 rounded-md object-cover">
                      <div class="flex flex-col justify-center w-full">
                        <!-- STUDENT NAME -->
                        <p class="text-xl font-medium text-[#1B3B50]">Nombre Alumno</p>
                        <!-- NUMBER OF FILES UPLOADED -->
                        <p class="text-lg text-[#6A7282]">1 Archivo Adjunto</p>
                      </div>
                    </div>
                    <!-- TIME OF TURNED IN -->
                    <p class="text-lg text-[#6A7282]">Hoy a las 12:32 </p>
                  </div>
                </div>
            `;

            studentAnswersTable.append(card);
        });

        spinner.stop();

        infoContainer.classList.remove('hidden');
    }).catch(err => console.error("Error: ", err));
}