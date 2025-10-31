let materialTypesImg = {
    pdf: '../../../../images/DotOrange.svg',
    image: '../../../../images/DotBlue.svg',
    video: '../../../../images/DotGreen.svg'
};

function loadHomeData() {
    const user = authManager.getUser();

    const welcomeText = document.getElementById('welcome-text');
    welcomeText.textContent = 'Bienvenido devuelta, ' + user['display_name'];

    const totalUsers = document.getElementById('total-users');
    const totalPublicMaterial = document.getElementById('total-public-material');
    const totalGuests = document.getElementById('total-guests');
    const totalStudents = document.getElementById('total-students');
    const totalTeachers = document.getElementById('total-teachers');
    const totalAdmins = document.getElementById('total-admins');

    const totalGroups = document.getElementById('total-groups');
    const totalAssignments = document.getElementById('total-assignments');
    const totalTurnedInAssignments = document.getElementById('total-turned-in-assignments');

    const recentMaterialsContainer = document.getElementById('recent-materials-container');

    let recentMaterials = [];

    authenticatedFetch('/api/admin/getBackofficeHomeData.php')
    .then(res => res.json())  
    .then(data => {
        totalUsers.textContent = data.totalUsers;
        totalPublicMaterial.textContent = data.publicMaterialsCount;
        totalGuests.textContent = data.totalGuests;
        totalStudents.textContent = data.totalStudents;
        totalTeachers.textContent = data.totalTeachers;
        totalAdmins.textContent = data.totalAdmins;

        totalGroups.textContent = data.groupsCount; 
        totalAssignments.textContent = data.assignmentsCount;
        totalTurnedInAssignments.textContent = data.turnedInAssignmentsCount;

        recentMaterials = data.recentMaterials;
        if (Array.isArray(recentMaterials) && recentMaterials.length === 0) {
            recentMaterialsContainer.className = 'flex flex-col w-full h-full px-5 space-y-3 items-center mt-8';
            recentMaterialsContainer.innerHTML = '<p>No hay materiales</p>';
        } else {
            recentMaterialsContainer.className = 'flex flex-col w-full h-full px-5 space-y-3 items-center justify-center';
            recentMaterials.forEach(material => {
            const newMaterial = `
                <div class="flex flex-col justify-center w-full rounded-xl border border-[#E5E7EB] shadow-md/25 p-5">
                  <div class="flex items-center space-x-2">
                    <img src="../../../../images/DotOrange.svg" alt="">
                    <p class="text-base font-semibold text-[#1B3B50]">${material.title}</p>
                    <div class="flex items-center space-x-2">
                      <img src="../../../../images/Line.svg" alt="">
                      <p class="text-sm text-[#6A7282]">${material.type == 'mp4' ? 'Video' : (material.type == 'png' || material.type == 'jpg' || material.type == 'webp' || material.type =='jpeg') ? 'Foto' : 'PDF'}</p>
                    </div>
                  </div>
                  <p class="text-sm text-[#6A7282] pl-5">${material.uploadedDate}</p>
                </div>
            `;

            recentMaterialsContainer.innerHTML += newMaterial; 
        });
        }
    }).catch(err => console.error('Error:', err));
}

loadHomeData();