document.addEventListener('DOMContentLoaded', (e) => {
    loadTasks();
});

async function loadTasks() {
    const assignmentsTable = document.getElementById('assignments-table');

    assignmentsTable.innerHTML = '';

    authenticatedFetch('/api/student/getAssignmentsFromGroup.php', {
        method: 'GET'
    }).then(res => res.json())
    .then(data => {
        if (data.ok) {
            if (data[0] === null) {
                const text = document.createElement('p');
                text.textContent = 'No hay tareas';
                text.className = 'text-center mt-6';
                
                assignmentsTable.append(text);
              } else {
              assignmentsTable.innerHTML = '';

              data[0].forEach(task => {
                const newTask = document.createElement('a');
                newTask.className = 'w-full h-[120px] border-b-2 border-[#DFDFDF] hover:bg-[#F2F2F2] flex items-center pl-10 transition duration-150 interactive shrink-0 no-underline';
                newTask.href = 'assignmentInfoStudent.html';
              
                newTask.innerHTML = `
                  <div class="flex w-full justify-between pr-10">
                    <div class="flex items-center space-x-5">
                       <img src="../../../../images/Assignment.png" alt="" class="w-20 h-20 shadow-md/25 rounded-md object-cover">
                       <div class="flex flex-col justify-center w-full">
                          <p class="text-xl font-medium text-[#1B3B50]">${task.name}</p>
                          <p class="text-base/5 text-[#6A7282] w-3/4">${task.description}</p>
                       </div>
                     </div>
                     <div class="flex flex-col items-end space-y-10">
                      <p class="text-[#6A7282]">${task.maxScore}</p>
                      <p class="text-[#CC4033]">Vence el ${task.dueDate}</p>
                     </div>
                  </div>
                `;
                assignmentsTable.append(newTask);
              });

            }
        }
    }).catch(err => console.error("Error: ", err));
}