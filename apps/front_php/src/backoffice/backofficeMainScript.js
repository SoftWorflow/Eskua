function loadHomeData() {
    const user = authManager.getUser();

    const welcomeText = document.getElementById('welcome-text');
    welcomeText.textContent = 'Bienvenido devuelta, ' + user['display_name'];

    const totalUsers = document.getElementById('total-users');
    const totalGuests = document.getElementById('total-guests');
    const totalStudents = document.getElementById('total-students');
    const totalTeachers = document.getElementById('total-teachers');
    const totalAdmins = document.getElementById('total-admins');

    authenticatedFetch('/api/admin/getBackofficeHomeData.php')
    .then(res => res.json())  
    .then(data => {
        totalUsers.textContent = data.totalUsers;
        totalGuests.textContent = data.totalGuests;
        totalStudents.textContent = data.totalStudents;
        totalTeachers.textContent = data.totalTeachers;
        totalAdmins.textContent = data.totalAdmins;
    }).catch(err => console.error('Error:', err));
}

loadHomeData();