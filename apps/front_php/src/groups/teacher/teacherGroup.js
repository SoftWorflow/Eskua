let groupStarsImg = '/images/GroupsStar.svg';

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const groupId = urlParams.get('groupId');
    loadGroupHomeData(groupId);
});

async function loadGroupHomeData(groupId) {
    const groupName = document.getElementById('group-name');
    const groupLevel = document.getElementById('group-level');

    const membersTable = document.getElementById('members-table');

    await authenticatedFetch('/api/teacher/getGroupHomeData.php', {
        method: 'POST',
        body: JSON.stringify( {id: groupId} )
    }).then(res => res.json())
    .then(data => {
        if (!data.ok) return;

        const group = data.group;

        let members = [];
        members = data.groupMembers;

        groupName.textContent = group.name ?? 'Group Name';

        groupLevel.textContent = 'Nivel de grupo: ' + (group.level ?? group.name ?? '');

        spawnStars(group.level ?? group.name ?? '');

        membersTable.innerHTML = '';

        const teacherCardDiv = document.createElement('div');
        teacherCardDiv.className = 'w-full h-[120px] border-b-2 border-[#DFDFDF] hover:bg-[#F2F2F2] flex items-center pl-10 transition duration-150 interactive shrink-0';
        teacherCardDiv.innerHTML = `
            <div class="flex space-x-5">
                <img src="${data.teacher.profilePicture}" alt="" class="w-20 h-20 shadow-md/25 rounded-md object-cover">
                <div class="flex flex-col justify-center w-full">
                    <p class="text-xl font-medium text-[#1B3B50]">${data.teacher.displayName}</p>
                    <div class="flex items-center space-x-1">
                    <img src="/images/TeacherCrownIcon.svg" alt="" class="h-5 w-5">
                    <p class="text-lg text-[#E1A05B]">Docente</p>
                    </div>
                </div>
            </div>
        `;

        membersTable.append(teacherCardDiv);

        if (members !== null) {
            members.forEach(member => {
                const displayName = member.displayName;
                const profilePicture = member.profilePicture ?? '/images/DefaultUserProfilePicture.jpg';
                
                const memberCardDiv = document.createElement('div');
                memberCardDiv.className = 'w-full h-[120px] border-b-2 border-[#DFDFDF] hover:bg-[#F2F2F2] flex items-center pl-10 transition duration-150 interactive shrink-0';
                memberCardDiv.innerHTML = `
                    <div class="flex space-x-5">
                        <img src="${profilePicture}" alt="" class="w-20 h-20 shadow-md/25 rounded-md object-cover">
                        <div class="flex flex-col justify-center w-full">
                            <p class="text-xl font-medium text-[#1B3B50]">${displayName}</p>
                            <div class="flex items-center space-x-1">
                            <img src="/images/StudentIcon.svg" alt="" class="h-5 w-5">
                            <p class="text-lg text-[#6A7282]">Alumno</p>
                            </div>
                        </div>
                    </div>
                `;
                
                membersTable.append(memberCardDiv);
            });
        }

    }).catch(err => console.error("Error: ", err));
}

function spawnStars(level) {
    const starsContainer = document.getElementById('stars-container');

    starsQuanitity = parseLevelToStarQuantity(level);
    for (let i = 1; i <= starsQuanitity; i++) {
        const span = document.createElement('span');
        const star = document.createElement('img');
        star.src = groupStarsImg;
        star.className = 'w-4 h-4';

        span.append(star);

        starsContainer.append(span);
    }
}

function parseLevelToStarQuantity(level) {
    switch (level.toLowerCase()) {
        case 'básico':
            return 1;
        case 'intermedio':
            return 2;
        case 'avanzado':
            return 3;
    }
}