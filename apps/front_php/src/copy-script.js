function showCopiedTooltip(element) {
  const tooltip = document.createElement('div');
  tooltip.innerText = '¡Copiado!';
  tooltip.className = 'absolute -top-12 left-1/2 -translate-x-1/2 px-4 py-2 rounded-lg text-white text-sm font-medium whitespace-nowrap pointer-events-none z-50 shadow-lg opacity-0 translate-y-2 transition-all duration-300';
  tooltip.style.backgroundColor = '#28C98E';
  
  const parent = element.parentElement;
  const originalPosition = parent.style.position;
  if (getComputedStyle(parent).position === 'static') {
    parent.style.position = 'relative';
  }
  
  parent.appendChild(tooltip);
  
  setTimeout(() => {
    tooltip.classList.remove('opacity-0', 'translate-y-2');
    tooltip.classList.add('opacity-100', 'translate-y-0');
  }, 10);
  
  setTimeout(() => {
    tooltip.classList.remove('opacity-100', 'translate-y-0');
    tooltip.classList.add('opacity-0', 'translate-y-2');
    
    setTimeout(() => {
      tooltip.remove();
      if (originalPosition === '' && parent.style.position === 'relative') {
        parent.style.position = originalPosition;
      }
    }, 300);
  }, 1200);
}

function makeCopyable(element) {
  element.classList.add('cursor-pointer');
  element.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(element.innerText);
      showCopiedTooltip(element);
    } catch (err) {
      console.error('Copy error:', err);
    }
  });
}

function makeCopyableWithText(element, textToCopy) {
  element.classList.add('cursor-pointer');
  element.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(textToCopy);
      showCopiedTooltip(element);
    } catch (err) {
      console.error('Copy error:', err);
    }
  });
}

function initCopyables() {
  document.querySelectorAll('[data-copyable]').forEach(element => {
    element.classList.add('cursor-pointer');
    element.addEventListener('click', async () => {
      const text = element.dataset.copyable || element.innerText;
      try {
        await navigator.clipboard.writeText(text);
        showCopiedTooltip(element);
      } catch (err) {
        console.error('Copy error:', err);
      }
    });
  });
}