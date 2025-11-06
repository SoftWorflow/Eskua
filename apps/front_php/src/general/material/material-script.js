document.addEventListener('DOMContentLoaded', () => {
    loadMaterials();
});

async function loadMaterials() {
    const materialsTable = document.getElementById('materials-table');

    authenticatedFetch('/api/user/getAllPublicMaterials.php')
        .then(res => res.json())
        .then(data => {
            if (!data.ok) {
                console.error(data.error);
                return;
            }

            const materials = data.materials;

            if (materials.length === 0) {
                const message = document.createElement('p');
                message.className = 'text-center mt-6';
                message.innerText = 'No hay materiales públicos';
                materialsTable.append(message);
                return;
            }

            materials.forEach(material => {
                const newMaterial = document.createElement('a');
                newMaterial.href = `/general/material/material-info.html?materialId=${material.id}`;
                newMaterial.className = 'w-full h-[120px] border-b-2 border-[#DFDFDF] hover:bg-[#F2F2F2] flex items-center pl-10 transition duration-150 interactive shrink-0 no-underline overflow-hidden';

                newMaterial.innerHTML = `
                    <div class="flex w-full justify-between pr-10">
                        <div class="flex items-center space-x-5">
                            <div class="bg-[#1B3B50] px-4 py-2 shadow-md/25 rounded-md flex justify-center items-center">
                                <img src="../../../../images/MaterialWhite.svg" alt="" class="w-15 h-15">
                            </div>
                            <div class="flex flex-col justify-center w-full">
                                <div class="flex space-x-2 items-center">
                                    <p class="text-xl font-medium text-[#1B3B50] truncate max-w-[350px]" title="${material.name}">${material.name}</p>
                                    <img src="/images/Line.svg" alt="">
                                    <p class="text-[#E1A05B]">${material.type == 'mp4' ? 'Video' : (material.type == 'png' || material.type == 'jpg' || material.type == 'webp' || material.type =='jpeg') ? 'Foto' : 'PDF'}</p>
                                </div>

                                <p class="text-base/5 text-[#6A7282] w-[650px] truncate" title="${material.description}">${material.description}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-10">
                            <p class="text-[#6A7282]">Creado el ${material.createdDate}</p>
                        </div>
                    </div>
                `;

                materialsTable.append(newMaterial);
            });
        }).catch(err => console.error("Error: ", err))
}