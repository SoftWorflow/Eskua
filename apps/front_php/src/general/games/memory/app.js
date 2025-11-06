// ========================================
// CATÁLOGO DE SEÑAS LSU (30 letras)
// ========================================
const CATALOGO_SENAS = [
  { id: 'A', letra: 'A', imgSeña: '/general/games/memory/img/signs/A.png' },
  { id: 'B', letra: 'B', imgSeña: '/general/games/memory/img/signs/B.png' },
  { id: 'C', letra: 'C', imgSeña: '/general/games/memory/img/signs/C.png' },
  { id: 'D', letra: 'D', imgSeña: '/general/games/memory/img/signs/D.png' },
  { id: 'E', letra: 'E', imgSeña: '/general/games/memory/img/signs/E.png' },
  { id: 'F', letra: 'F', imgSeña: '/general/games/memory/img/signs/F.png' },
  { id: 'G', letra: 'G', imgSeña: '/general/games/memory/img/signs/G.png' },
  { id: 'H', letra: 'H', imgSeña: '/general/games/memory/img/signs/H.png' },
  { id: 'I', letra: 'I', imgSeña: '/general/games/memory/img/signs/I.png' },
  { id: 'J', letra: 'J', imgSeña: '/general/games/memory/img/signs/J.png' },
  { id: 'K', letra: 'K', imgSeña: '/general/games/memory/img/signs/K.png' },
  { id: 'L', letra: 'L', imgSeña: '/general/games/memory/img/signs/L.png' },
  { id: 'M', letra: 'M', imgSeña: '/general/games/memory/img/signs/M.png' },
  { id: 'N', letra: 'N', imgSeña: '/general/games/memory/img/signs/N.png' },
  { id: 'Ñ', letra: 'Ñ', imgSeña: '/general/games/memory/img/signs/Ñ.png' },
  { id: 'O', letra: 'O', imgSeña: '/general/games/memory/img/signs/O.png' },
  { id: 'P', letra: 'P', imgSeña: '/general/games/memory/img/signs/P.png' },
  { id: 'Q', letra: 'Q', imgSeña: '/general/games/memory/img/signs/Q.png' },
  { id: 'R', letra: 'R', imgSeña: '/general/games/memory/img/signs/R.png' },
  { id: 'S', letra: 'S', imgSeña: '/general/games/memory/img/signs/S.png' },
  { id: 'T', letra: 'T', imgSeña: '/general/games/memory/img/signs/T.png' },
  { id: 'U', letra: 'U', imgSeña: '/general/games/memory/img/signs/U.png' },
  { id: 'V', letra: 'V', imgSeña: '/general/games/memory/img/signs/V.png' },
  { id: 'W', letra: 'W', imgSeña: '/general/games/memory/img/signs/W.png' },
  { id: 'X', letra: 'X', imgSeña: '/general/games/memory/img/signs/X.png' },
  { id: 'Y', letra: 'Y', imgSeña: '/general/games/memory/img/signs/Y.png' },
  { id: 'Z', letra: 'Z', imgSeña: '/general/games/memory/img/signs/Z.png' },
  { id: 'CH', letra: 'CH', imgSeña: '/general/games/memory/img/signs/CH.png' },
  { id: 'LL', letra: 'LL', imgSeña: '/general/games/memory/img/signs/LL.png' },
  { id: 'RR', letra: 'RR', imgSeña: '/general/games/memory/img/signs/RR.png' }
];

// ========================================
// ESTADO DEL JUEGO
// ========================================
let gameState = {
  tiempo: 0,
  puntaje: 0,
  movimientos: 0,
  timerInterval: null,
  mazo: [],
  cartasVolteadas: [],
  inputBloqueado: false,
  paresEncontrados: 0,
  mejorPuntaje: parseInt(localStorage.getItem('mejorPuntaje')) || 0
};

// ========================================
// ELEMENTOS DEL DOM
// ========================================
let elementos = {};

// ========================================
// UTILIDADES
// ========================================

/**
 * Fisher-Yates shuffle
 */
function barajar(array) {
  const arr = [...array];
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
  return arr;
}

/**
 * Formatear tiempo en MM:SS
 */
function formatearTiempo(segundos) {
  const mins = Math.floor(segundos / 60);
  const secs = segundos % 60;
  return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
}

/**
 * Anunciar mensaje para screen readers
 */
function anunciar(mensaje) {
  if (elementos.srAnnouncer) {
    elementos.srAnnouncer.textContent = mensaje;
  }
}

/**
 * Actualizar HUD (tiempo, puntaje, intentos)
 */
function actualizarHUD() {
  if (elementos.tiempoDisplay) elementos.tiempoDisplay.textContent = formatearTiempo(gameState.tiempo);
  if (elementos.puntajeDisplay) elementos.puntajeDisplay.textContent = gameState.puntaje;
  if (elementos.intentosDisplay) elementos.intentosDisplay.textContent = gameState.movimientos; // ahora muestra movimientos
  if (elementos.mejorPuntajeDisplay) elementos.mejorPuntajeDisplay.textContent = gameState.mejorPuntaje;
}


/**
 * Calcula y suma el puntaje de un par correcto.
 * Cuanto menor el tiempo y los movimientos, mayor el valor.
 */
function sumarPuntajePar() {
  const base = 6000;

  // Penalización gradual: los movimientos pesan más que el tiempo
  const penalizacionMov = Math.pow(gameState.movimientos / 4, 1.5) * 350;
  const penalizacionTiempo = Math.pow(gameState.tiempo / 35, 1.15) * 120;

  // Resultado final con límite inferior
  const puntosPar = Math.max(Math.floor(base - penalizacionMov - penalizacionTiempo), 600);

  gameState.puntaje += puntosPar;
  mostrarGanancia(puntosPar);
  actualizarHUD();

  return puntosPar;
}

/**
 * Muestra una animación temporal sobre el puntaje (tipo +3560)
 * El color cambia según cuántos puntos se obtuvieron.
 */
function mostrarGanancia(puntos) {
  let color = '#E1A05B';
  if (puntos > 5000) color = '#4CAF50';
  else if (puntos > 3000) color = '#2F9E44';
  else if (puntos < 1500) color = '#E57373';

  const anim = document.createElement('div');
  anim.textContent = `+${puntos}`;
  anim.className = 'absolute opacity-100';
  anim.style.color = color;
  anim.style.fontFamily = "'Lexend', sans-serif"; // ← fuente Lexend
  anim.style.fontWeight = '500'; // ← peso Medium
  anim.style.fontSize = '2.2rem';
  anim.style.left = '50%';
  anim.style.top = '-3rem';
  anim.style.transform = 'translateX(-50%) scale(1)';
  anim.style.pointerEvents = 'none';
  anim.style.transition = 'all 0.8s ease-in-out';

  const parent = elementos.puntajeDisplay?.parentElement;
  if (parent) {
    parent.style.position = 'relative';
    parent.appendChild(anim);
    setTimeout(() => {
      anim.style.top = '-6rem';
      anim.style.opacity = '0';
      anim.style.transform = 'translateX(-50%) scale(1.4)';
    }, 10);
    setTimeout(() => anim.remove(), 1000);
  }
}

// ========================================
// CREACIÓN DEL MAZO
// ========================================

/**
 * Samplea 12 señas aleatorias del catálogo y crea 24 cartas:
 * 12 cartas tipo "letra" + 12 cartas tipo "seña"
 */
function crearMazo() {
  const catalogoBarajado = barajar(CATALOGO_SENAS);
  const seleccionadas = catalogoBarajado.slice(0, 12);

  const cartas = [];

  seleccionadas.forEach(seña => {
    // Carta tipo letra
    cartas.push({
      id: `${seña.id}-letra`,
      pairId: seña.id,
      tipo: 'letra',
      contenido: seña.letra
    });

    // Carta tipo seña
    cartas.push({
      id: `${seña.id}-seña`,
      pairId: seña.id,
      tipo: 'seña',
      contenido: seña.imgSeña
    });
  });

  return barajar(cartas);
}

// ========================================
// RENDER DEL TABLERO
// ========================================

/**
 * Renderiza el grid de 24 cartas (8×3)
 */
function renderTablero() {
  const tablero = document.getElementById('tablero');
  if (!tablero) return;

  tablero.innerHTML = ''; // Limpiar

  gameState.mazo.forEach((carta, index) => {
    const cardEl = crearElementoCarta(carta, index);
    tablero.appendChild(cardEl);
  });
}

/**
 * Crea el elemento DOM de una carta con estructura flip 3D
 */
function crearElementoCarta(carta, index) {
  const container = document.createElement('div');
  container.className = 'card-container aspect-[3/4] relative';
  container.dataset.cardId = carta.id;
  container.dataset.pairId = carta.pairId;
  container.dataset.index = index;
  container.tabIndex = 0;
  container.setAttribute('role', 'button');
  container.setAttribute('aria-label', `Carta ${index + 1}`);
  container.setAttribute('aria-pressed', 'false');

  const inner = document.createElement('div');
  inner.className = 'card-inner';

  // Dorso (boca abajo)
  const back = document.createElement('div');
  back.className = 'card-face card-back flex items-center justify-center';
  back.innerHTML = `
    <img 
      src="/general/games/memory/img/EskuaLogoWhite.webp" 
      alt="Dorso carta" 
      class="w-16 h-16 object-contain opacity-90"
    >
  `;

  // Frente (contenido)
  const front = document.createElement('div');
  front.className = 'card-face card-front flex items-center justify-center';

  if (carta.tipo === 'letra') {
    // Muestra la letra en grande
    front.innerHTML = `
      <div class="text-5xl font-bold select-none" style="color: #1B3B50;">
        ${carta.contenido}
      </div>
    `;
  } else {
    // Muestra la seña como imagen
    front.innerHTML = `
      <img 
        src="${carta.contenido}" 
        alt="Seña ${carta.pairId}" 
        class="w-30 h-30 object-contain select-none"
      >
    `;
  }

  inner.appendChild(back);
  inner.appendChild(front);
  container.appendChild(inner);

  aplicarEventos(container);
  aplicarHover(container);

  return container;
}

// ========================================
// EVENTOS DE CARTAS
// ========================================

/**
 * Aplica eventos click y teclado a una carta
 */
function aplicarEventos(cardEl) {
  // Click
  const clickHandler = (e) => {
    // Prevenir si está arrastrando
    if (cardEl.classList.contains('dragging')) return;

    if (!gameState.inputBloqueado &&
      !cardEl.classList.contains('flipped') &&
      !cardEl.classList.contains('matched')) {
      voltearCarta(cardEl);
    }
  };

  cardEl.addEventListener('click', clickHandler);

  // Teclado (Enter/Space)
  const keyHandler = (e) => {
    if ((e.key === 'Enter' || e.key === ' ') &&
      !gameState.inputBloqueado &&
      !cardEl.classList.contains('flipped') &&
      !cardEl.classList.contains('matched')) {
      e.preventDefault();
      voltearCarta(cardEl);
    }
  };

  cardEl.addEventListener('keydown', keyHandler);
}

/**
 * Voltea una carta y gestiona la lógica de comparación
 */
function voltearCarta(cardEl) {
  if (gameState.cartasVolteadas.includes(cardEl)) return;

  // Añadir clase de animación
  cardEl.classList.add('flipping');
  setTimeout(() => cardEl.classList.remove('flipping'), 200);

  // Forzar reflow antes del flip
  void cardEl.offsetWidth;
  cardEl.classList.add('flipped');
  cardEl.setAttribute('aria-pressed', 'true');
  gameState.cartasVolteadas.push(cardEl);

  if (gameState.cartasVolteadas.length === 2) {
    gameState.inputBloqueado = true;
    // Deshabilitar visualmente todas las cartas no volteadas
    deshabilitarCartasNoVolteadas();
    setTimeout(() => compararCartas(), 800);
  }
}

/**
 * Deshabilita visualmente las cartas que no están volteadas
 */
function deshabilitarCartasNoVolteadas() {
  document.querySelectorAll('.card-container:not(.flipped):not(.matched)').forEach(card => {
    card.classList.add('disabled');
  });
}

/**
 * Habilita todas las cartas
 */
function habilitarTodasLasCartas() {
  document.querySelectorAll('.card-container').forEach(card => {
    card.classList.remove('disabled');
  });
}


/**
 * Compara las dos cartas volteadas
 */
function compararCartas() {
  gameState.movimientos++;
  actualizarHUD();

  const [carta1, carta2] = gameState.cartasVolteadas;
  const pairId1 = carta1.dataset.pairId;
  const pairId2 = carta2.dataset.pairId;

  if (pairId1 === pairId2) {
    manejarMatch(carta1, carta2);
  } else {
    manejarFallo(carta1, carta2);
  }
}

/**
 * Maneja un match exitoso
 */
function manejarMatch(carta1, carta2) {
  setTimeout(() => {
    carta1.classList.add('matched');
    carta2.classList.add('matched');
  }, 150);

  gameState.paresEncontrados++;
  const puntosPar = sumarPuntajePar();

  gameState.cartasVolteadas = [];
  gameState.inputBloqueado = false;
  habilitarTodasLasCartas();

  if (gameState.paresEncontrados === 12) {
    setTimeout(() => chequearVictoria(), 500);
  }

  actualizarHUD();
}

/**
 * Maneja un fallo (cartas no coinciden)
 */
function manejarFallo(carta1, carta2) {
  carta1.classList.add('fail');
  carta2.classList.add('fail');

  anunciar("No coinciden.");

  if (gameState.rachaActual > 0) {
    gameState.rachaActual = 0;
  }

  setTimeout(() => {
    carta1.classList.remove('flipped', 'fail');
    carta2.classList.remove('flipped', 'fail');
    carta1.setAttribute('aria-pressed', 'false');
    carta2.setAttribute('aria-pressed', 'false');

    gameState.cartasVolteadas = [];
    gameState.inputBloqueado = false;
    habilitarTodasLasCartas();
  }, 1000);
}

function aplicarHover(cardEl) {
  const strength = 15; // intensidad del giro
  const depth = 4;     // profundidad del hundimiento
  const scale = 0.99;  // ligera reducción de tamaño para dar sensación de presión

  cardEl.addEventListener("mousemove", (e) => {
    const rect = cardEl.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const midX = rect.width / 2;
    const midY = rect.height / 2;

    // 🔄 Invertimos la dirección de los ángulos
    const rotateX = ((y - midY) / midY) * -strength;
    const rotateY = ((x - midX) / midX) * strength;

    cardEl.style.transform = `
      perspective(1000px)
      rotateX(${rotateX}deg)
      rotateY(${rotateY}deg)
      translateY(${depth}px)
      scale(${scale})
    `;
    cardEl.style.filter = "brightness(0.95)";
    cardEl.classList.add("hover-dynamic");
  });

  cardEl.addEventListener("mouseleave", () => {
    cardEl.style.transform = "rotateX(0deg) rotateY(0deg) translateY(0) scale(1)";
    cardEl.style.filter = "brightness(1)";
    cardEl.classList.remove("hover-dynamic");
  });
}

/**
 * Muestra el progreso visualmente
 */
function actualizarProgreso() {
  const progreso = (gameState.paresEncontrados / 12) * 100;

  // Puedes agregar una barra de progreso si quieres
  if (elementos.tablero) {
    elementos.tablero.style.opacity = 1 - (progreso * 0.001); // Sutil feedback
  }
}

// ========================================
// TIMER
// ========================================

/**
 * Inicia el contador de tiempo
 */
function iniciarTimer() {
  gameState.tiempo = 0;
  actualizarHUD();

  gameState.timerInterval = setInterval(() => {
    gameState.tiempo++;
    actualizarHUD();
  }, 1000);
}

/**
 * Detiene el contador de tiempo
 */
function detenerTimer() {
  if (gameState.timerInterval) {
    clearInterval(gameState.timerInterval);
    gameState.timerInterval = null;
  }
}

// ========================================
// VICTORIA Y DERROTA
// ========================================

/**
 * Verifica y muestra modal de victoria
 */
function chequearVictoria() {
  detenerTimer();

  let nuevoRecord = false;

  if (gameState.puntaje > gameState.mejorPuntaje) {
    gameState.mejorPuntaje = gameState.puntaje;
    localStorage.setItem('mejorPuntaje', gameState.mejorPuntaje);
    nuevoRecord = true;
  }

  mostrarModalEndgame(true, nuevoRecord);
}

/**
 * Verifica y muestra modal de derrota
 */
function chequearDerrota() {
  detenerTimer();
  mostrarModalEndgame(false);
}

/**
 * Muestra el modal de fin de juego
 */
function mostrarModalEndgame(victoria, nuevoRecord = false) {
  if (!elementos.modalEndgame) return;

  if (elementos.endgameScore) elementos.endgameScore.textContent = gameState.puntaje;
  if (elementos.endgameTime) elementos.endgameTime.textContent = `Tiempo: ${formatearTiempo(gameState.tiempo)}`;
  if (elementos.endgameMoves) elementos.endgameMoves.textContent = `Movimientos: ${gameState.movimientos}`;

  if (victoria) {
    if (elementos.endgameIcon) {
      elementos.endgameIcon.style.background = '#E1A05B';
      elementos.endgameIcon.innerHTML = `
        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
      `;
    }

    let titulo = '¡Felicitaciones!';
    let mensaje = '¡Has completado el juego con éxito!';

    if (nuevoRecord) {
      titulo = '🏆 ¡Nuevo Récord!';
      mensaje = 'Superaste tu mejor puntaje anterior, increíble trabajo.';
    }

    if (elementos.endgameTitle) elementos.endgameTitle.textContent = titulo;
    if (elementos.endgameMessage) elementos.endgameMessage.textContent = mensaje;

    anunciar('¡Victoria! Has encontrado todos los pares.');
  } else {
    if (elementos.endgameIcon) {
      elementos.endgameIcon.style.background = '#6A7282';
      elementos.endgameIcon.innerHTML = `
        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
      `;
    }
    if (elementos.endgameTitle) elementos.endgameTitle.textContent = 'Juego Terminado';
    if (elementos.endgameMessage) elementos.endgameMessage.textContent = 'Te has quedado sin intentos. ¡Inténtalo nuevamente!';

    anunciar('Derrota. Te has quedado sin intentos.');
  }

  elementos.modalEndgame.classList.remove('hidden');
}

/**
 * Oculta el modal de fin de juego
 */
function ocultarModalEndgame() {
  if (elementos.modalEndgame) {
    elementos.modalEndgame.classList.add('hidden');
  }
}

// ========================================
// MODAL TUTORIAL
// ========================================

/**
 * Muestra el modal de tutorial
 */
function mostrarTutorial() {
  if (elementos.modalTutorial) {
    elementos.modalTutorial.classList.remove('hidden');
  }
}

/**
 * Oculta el modal de tutorial
 */
function ocultarTutorial() {
  if (elementos.modalTutorial) {
    elementos.modalTutorial.classList.add('hidden');
  }
}

// ========================================
// REINICIAR JUEGO
// ========================================

/**
 * Reinicia completamente el juego
 */
function reiniciar() {
  detenerTimer();

  gameState.tiempo = 0;
  gameState.puntaje = 0;
  gameState.movimientos = 0;
  gameState.cartasVolteadas = [];
  gameState.inputBloqueado = false;
  gameState.paresEncontrados = 0;

  gameState.mazo = crearMazo();

  actualizarHUD();
  renderTablero();

  ocultarModalEndgame();
  ocultarTutorial();
  iniciarTimer();

  anunciar('Juego reiniciado. ¡Buena suerte!');
}

// ========================================
// INICIALIZACIÓN
// ========================================

/**
 * Inicializa el juego
 */
function inicializar() {
  // Inicializar elementos del DOM
  elementos = {
    tiempoDisplay: document.getElementById('tiempo-display'),
    puntajeDisplay: document.getElementById('puntaje-display'),
    intentosDisplay: document.getElementById('intentos-display'),
    mejorPuntajeDisplay: document.getElementById('mejor-puntaje'),
    tablero: document.getElementById('tablero'),
    modalTutorial: document.getElementById('modal-tutorial'),
    modalEndgame: document.getElementById('modal-endgame'),
    btnTutorial: document.getElementById('btn-tutorial'),
    btnReiniciar: document.getElementById('btn-reiniciar'),
    btnJugar: document.getElementById('btn-jugar'),
    btnJugarNuevamente: document.getElementById('btn-jugar-nuevamente'),
    closeTutorial: document.getElementById('close-tutorial'),
    srAnnouncer: document.getElementById('sr-announcer'),
    endgameIcon: document.getElementById('endgame-icon'),
    endgameTitle: document.getElementById('endgame-title'),
    endgameMessage: document.getElementById('endgame-message'),
    endgameScore: document.getElementById('endgame-score'),
    endgameTime: document.getElementById('endgame-time'),
    endgameMoves: document.getElementById('endgame-moves'),
  };

  // Resto del código igual...
  actualizarHUD();

  const primeraVez = !localStorage.getItem('tutorialVisto');
  if (primeraVez) {
    mostrarTutorial();
    localStorage.setItem('tutorialVisto', 'true');
  } else {
    reiniciar();
  }

  // Event Listeners...
  if (elementos.btnTutorial) {
    elementos.btnTutorial.addEventListener('click', mostrarTutorial);
  }
  if (elementos.btnReiniciar) {
    elementos.btnReiniciar.addEventListener('click', reiniciar);
  }
  if (elementos.btnJugar) {
    elementos.btnJugar.addEventListener('click', () => {
      ocultarTutorial();
      reiniciar();
    });
  }
  if (elementos.btnJugarNuevamente) {
    elementos.btnJugarNuevamente.addEventListener('click', reiniciar);
  }
  if (elementos.closeTutorial) {
    elementos.closeTutorial.addEventListener('click', () => {
      ocultarTutorial();
      if (!gameState.timerInterval) {
        reiniciar();
      }
    });
  }

  // Cerrar modal con ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (elementos.modalTutorial && !elementos.modalTutorial.classList.contains('hidden')) {
        ocultarTutorial();
        if (!gameState.timerInterval) {
          reiniciar();
        }
      }
    }
  });

  console.log('🎮 Juego Memoria LSU cargado');
  console.log('💡 Funciones debug disponibles: revelarTodo(), autowin(), autolose(), agregarTiempo(n), agregarIntentos(n), verEstado()');
}

// ========================================
// DEBUG FUNCTIONS (útiles para testing)
// ========================================

/**
 * Revela todas las cartas (debug)
 */
window.revelarTodo = function () {
  document.querySelectorAll('.card-container').forEach(card => {
    card.classList.add('flipped');
  });
  console.log('🔍 Todas las cartas reveladas');
};

/**
 * Victoria instantánea (debug)
 */
window.autowin = function () {
  gameState.paresEncontrados = 12;
  chequearVictoria();
  console.log('🏆 Victoria forzada');
};

/**
 * Derrota instantánea (debug)
 */
window.autolose = function () {
  gameState.intentos = 0;
  chequearDerrota();
  console.log('💀 Derrota forzada');
};

/**
 * Agregar tiempo (debug)
 */
window.agregarTiempo = function (segundos) {
  gameState.tiempo += segundos;
  actualizarHUD();
  console.log(`⏱️ +${segundos} segundos agregados`);
};

/**
 * Agregar intentos (debug)
 */
window.agregarIntentos = function (cantidad) {
  gameState.intentos += cantidad;
  actualizarHUD();
  console.log(`💪 +${cantidad} intentos agregados`);
};

/**
 * Ver estado completo (debug)
 */
window.verEstado = function () {
  console.log('📊 Estado actual:', {
    tiempo: gameState.tiempo,
    puntaje: gameState.puntaje,
    intentos: gameState.intentos,
    paresEncontrados: gameState.paresEncontrados,
    cartasVolteadas: gameState.cartasVolteadas.length,
    inputBloqueado: gameState.inputBloqueado
  });
};

// ========================================
// INICIO DE LA APLICACIÓN
// ========================================

// Esperar a que el DOM esté completamente cargado
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', inicializar);
} else {
  inicializar();
}