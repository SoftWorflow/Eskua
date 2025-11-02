let actualPageExtraTailwindClasses =  [
    'border-2',
    'border-[#E1A05B]',
    'rounded-lg',
    'px-4',
    'py-2'
];

function updateSelectedPage(page) {
    actualPageExtraTailwindClasses.forEach(tailwindClass => {
        document.getElementById(page).classList.add(tailwindClass);
    });

    document.getElementById('home').className = 'text-lg hover:text-[#E1A05B] transition interactive';
}