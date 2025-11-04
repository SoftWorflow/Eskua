let starsImages = {
    'básico': '../../../../images/GroupsStar.svg',
    'intermedio': '../../../../images/GroupsStar2.svg',
    'avanzado': '../../../../images/GroupsStar3.svg'
};

document.addEventListener('DOMContentLoaded', () => {
    renderGroupsSelect();
});

async function renderGroupsSelect() {
    const groupsContainer = document.getElementById('groups-container');
    const welcomeText = document.getElementById('welcome-text');
    
    welcomeText.innerText = 'Bienvenido nuevamente, ' + authManager.getUser().display_name + '.';

    await authenticatedFetch('/api/teacher/getTeacherGroups.php', {
        method: 'GET'
    }).then(res => res.json())
    .then(data => {
        if (!data.ok) {
            console.error('Error al cargar los grupos');
            console.error(data.message);
            window.location = '/home/index.php';
            return;
        }

        data.groups.forEach(group => {
            const newGroupCard = document.createElement('a');
            newGroupCard.className = 'group transition duration-200 bg-[#1B3B50] shadow-md/25 w-[300px] h-[300px] 2xl:w-[525px] 2xl:h-[525px] rounded-lg flex justify-center items-center p-1.5 hover:bg-[#E1A05B] interactive no-underline';
            newGroupCard.onclick = () => loadSpecificGroup(group.id);

            newGroupCard.innerHTML = `
                <div class="bg-white rounded-md w-full h-full flex flex-col">
                    <div class="md:h-4/6 2xl:h-3/4 border-b-2 border-[#DFDFDF] flex items-center justify-center">
                        <img src="${parseLevelToImage(group.name)}" alt="" class="md:w-16 md:h-16 2xl:h-auto 2xl:w-auto group-hover:scale-105 transition duration-200 ease-in-out">
                    </div>
                    <div class="md:md:h-2/6 2xl:h-1/4 flex flex-col items-center justify-center space-y-2">
                        <p class="md:text-2xl 2xl:text-3xl font-semibold text-[#1B3B50] group-hover:text-[#E1A05B] transition duration-200">${group.name}</p>
                        <div class="flex w-full justify-center space-x-2">
                            <p class="md:text-sm 2xl:text-lg text-[#6A7282]">${group.name}</p>
                            <img src="../../../../images/Line.svg" alt="">
                            <p class="md:text-sm 2xl:text-lg text-[#6A7282]">${group.totalStudents} integrantes</p>
                        </div>
                    </div>
                </div>
            `;

            groupsContainer.append(newGroupCard);
        });
    }).catch(err => console.error("Error: ", err));
}

function loadSpecificGroup(groupId) {
    window.location.href = `/groups/teacher/groupHomeTeacher.html?groupId=${groupId}`;
}

function parseLevelToImage(level) {
    return starsImages[level.toLowerCase()];
}