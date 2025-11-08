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
                    <img src="${material.type == 'mp4' ? materialTypesImg.video : (material.type == 'png' || material.type == 'jpg' || material.type == 'webp' || material.type =='jpeg') ? materialTypesImg.image : materialTypesImg.pdf}" alt="">
                    <p class="text-base font-semibold text-[#1B3B50]" title="${material.title}">${material.title.length >= 32 ? material.title.slice(0,32) + '...' : material.title}</p>
                    <div class="flex items-center space-x-2">
                      <img src="../../../../images/Line.svg" alt="">
                      <p class="text-sm text-[#6A7282]">${material.type == 'mp4' ? 'Video' : (material.type == 'png' || material.type == 'jpg' || material.type == 'webp' || material.type =='jpeg') ? 'Foto' : 'PDF'}</p>
                    </div>
                  </div>
                  <p class="text-sm text-[#6A7282] pl-5">${formatUploadDate(material.uploadedDate)}</p>
                </div>
            `;

            recentMaterialsContainer.innerHTML += newMaterial; 
        });
        }
    }).catch(err => console.error('Error:', err));
}

function formatUploadDate(uploadedDateString) {
  const uploadedDate = new Date(uploadedDateString.replace(' ', 'T'));
  const currentDate = new Date();

  // Gets the difference between dates in milliseconds
  const timeDifference = currentDate.getTime() - uploadedDate.getTime();

  // Converts the millisecond difference to a minute difference
  const minuteDifference = timeDifference / (1000 * 60);

  // Converts the millisecond difference to an hour difference
  const hoursDifference = timeDifference / (1000 * 60 * 60);

  const goodMinuteDifference = Math.floor(minuteDifference);
  const goodHoursDifference = Math.floor(hoursDifference);

  if (hoursDifference < 24) {
    if (hoursDifference <= 1) {
      return `Agregado hace ${goodMinuteDifference} minutos`;
    }
    
    return `Agregado hace ${goodHoursDifference} horas`;
  } 
}

loadHomeData();