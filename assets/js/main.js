const menuBtn = document.getElementById('menuBtn');
const mainNav = document.getElementById('mainNav');
const siteHeader = document.getElementById('siteHeader');

menuBtn?.addEventListener('click', () => {
  mainNav.classList.toggle('active');
  document.body.classList.toggle('menu-open');
  menuBtn.textContent = mainNav.classList.contains('active') ? '✕' : '☰';
});

document.querySelectorAll('#mainNav a').forEach(link => {
  link.addEventListener('click', () => {
    mainNav.classList.remove('active');
    document.body.classList.remove('menu-open');
    menuBtn.textContent = '☰';
  });
});

function handleHeaderScroll(){
  if (!siteHeader) return;

  if (window.scrollY > 40) {
    siteHeader.classList.add('scrolled');
  } else {
    siteHeader.classList.remove('scrolled');
  }
}

window.addEventListener('scroll', handleHeaderScroll);
handleHeaderScroll();

const revealElements = document.querySelectorAll('.reveal');

const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      revealObserver.unobserve(entry.target);
    }
  });
}, {
  threshold: 0.15
});

revealElements.forEach(element => revealObserver.observe(element));

const glow = document.querySelector('.cursor-glow');

window.addEventListener('mousemove', (e) => {
  if (!glow) return;

  glow.style.left = `${e.clientX}px`;
  glow.style.top = `${e.clientY}px`;
});

document.querySelectorAll('.event-card, .representante-card, .intro-card, .hero-glass-card').forEach(card => {
  card.addEventListener('mousemove', (e) => {
    if (window.innerWidth < 900) return;

    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    const rotateX = ((y / rect.height) - 0.5) * -7;
    const rotateY = ((x / rect.width) - 0.5) * 7;

    card.style.transform = `perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
  });

  card.addEventListener('mouseleave', () => {
    card.style.transform = 'perspective(900px) rotateX(0deg) rotateY(0deg) translateY(0)';
  });
});

document.querySelectorAll('.gallery-grid img').forEach(img => {
  img.addEventListener('click', () => {
    const overlay = document.createElement('div');
    overlay.className = 'image-lightbox';
    overlay.innerHTML = `
      <div class="image-lightbox-content">
        <button class="image-lightbox-close" aria-label="Cerrar imagen">✕</button>
        <img src="${img.src}" alt="${img.alt}">
      </div>
    `;

    document.body.appendChild(overlay);

    const close = () => {
      overlay.remove();
      document.removeEventListener('keydown', escClose);
    };

    const escClose = (event) => {
      if (event.key === 'Escape') close();
    };

    overlay.addEventListener('click', (event) => {
      if (event.target === overlay || event.target.classList.contains('image-lightbox-close')) {
        close();
      }
    });

    document.addEventListener('keydown', escClose);
  });
});

const lightboxStyles = document.createElement('style');
lightboxStyles.textContent = `
  .image-lightbox{
    position:fixed;
    inset:0;
    z-index:9999;
    display:grid;
    place-items:center;
    padding:24px;
    background:rgba(31,24,51,.78);
    backdrop-filter:blur(12px);
    animation:fadeIn .25s ease;
  }

  .image-lightbox-content{
    position:relative;
    max-width:min(960px,100%);
  }

  .image-lightbox-content img{
    width:100%;
    max-height:82vh;
    object-fit:contain;
    border-radius:26px;
    box-shadow:0 30px 90px rgba(0,0,0,.35);
  }

  .image-lightbox-close{
    position:absolute;
    top:-18px;
    right:-18px;
    width:44px;
    height:44px;
    border:0;
    border-radius:50%;
    background:#ff7a18;
    color:white;
    font-size:20px;
    cursor:pointer;
    box-shadow:0 12px 30px rgba(0,0,0,.25);
  }

  @keyframes fadeIn{
    from{opacity:0}
    to{opacity:1}
  }
`;
document.head.appendChild(lightboxStyles);

// Detecta las fotos verticales de los actos para que no se recorten.
document.querySelectorAll('.event-img').forEach(img => {
  const applyVerticalClass = () => {
    if (img.naturalHeight > img.naturalWidth) {
      img.classList.add('event-img-vertical');
    }
  };

  if (img.complete) {
    applyVerticalClass();
  } else {
    img.addEventListener('load', applyVerticalClass, { once: true });
  }
});

function escapeHtml(value) {
  return String(value || '').replace(/[&<>'"]/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    "'": '&#039;',
    '"': '&quot;'
  }[char]));
}

function openEventLightbox(card) {
  const data = card.dataset;
  const title = data.actTitle || 'Acto';
  const description = data.actDescription || 'Acto de la comisión fallera.';
  const image = data.actImage || 'assets/img/icon-192.png';
  const date = data.actDate || '';
  const time = data.actTime || '';
  const location = data.actLocation || '';

  const meta = [
    date ? `📅 ${escapeHtml(date)}` : '',
    time ? `🕒 ${escapeHtml(time)}` : '',
    location ? `📍 ${escapeHtml(location)}` : ''
  ].filter(Boolean).map(item => `<span>${item}</span>`).join('');

  const overlay = document.createElement('div');
  overlay.className = 'event-lightbox';
  overlay.innerHTML = `
    <div class="event-lightbox-card" role="dialog" aria-modal="true" aria-label="Detalles del acto">
      <button class="event-lightbox-close" aria-label="Cerrar detalles">✕</button>
      <div class="event-lightbox-inner">
        <div class="event-lightbox-image">
          <img src="${escapeHtml(image)}" alt="${escapeHtml(title)}">
        </div>
        <div class="event-lightbox-info">
          ${date ? `<span class="event-lightbox-date">${escapeHtml(date)}</span>` : ''}
          <h3>${escapeHtml(title)}</h3>
          ${meta ? `<div class="event-lightbox-meta">${meta}</div>` : ''}
          <p>${escapeHtml(description)}</p>
        </div>
      </div>
    </div>
  `;

  document.body.appendChild(overlay);
  document.body.style.overflow = 'hidden';

  const inner = overlay.querySelector('.event-lightbox-inner');
  const modalImg = overlay.querySelector('.event-lightbox-image img');

  const applyLayout = () => {
    if (!modalImg || !inner) return;
    if (modalImg.naturalWidth >= modalImg.naturalHeight) {
      inner.classList.add('is-horizontal');
    }
  };

  if (modalImg.complete) {
    applyLayout();
  } else {
    modalImg.addEventListener('load', applyLayout, { once: true });
  }

  const close = () => {
    overlay.remove();
    document.body.style.overflow = '';
    document.removeEventListener('keydown', escClose);
  };

  const escClose = (event) => {
    if (event.key === 'Escape') close();
  };

  overlay.addEventListener('click', (event) => {
    if (event.target === overlay || event.target.classList.contains('event-lightbox-close')) {
      close();
    }
  });

  document.addEventListener('keydown', escClose);
}

document.querySelectorAll('.event-card').forEach(card => {
  card.addEventListener('click', () => openEventLightbox(card));
  card.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      openEventLightbox(card);
    }
  });
});


function actualizarCuentaAtrasFallas() {
  const countdown = document.getElementById('fallas-countdown');
  const currentDay = document.getElementById('fallas-current-day');
  const subtitle = document.getElementById('fallas-countdown-subtitle');

  if (!countdown || !currentDay) return;

  const ahora = new Date();
  const year = ahora.getFullYear();
  const inicioFallas = new Date(year, 2, 15, 0, 0, 0);
  const finFallas = new Date(year, 2, 20, 0, 0, 0);
  const objetivo = ahora >= finFallas ? new Date(year + 1, 2, 15, 0, 0, 0) : inicioFallas;

  if (ahora >= inicioFallas && ahora < finFallas) {
    const textos = {
      15: 'Primer día de Fallas',
      16: 'Segundo día de Fallas',
      17: 'Tercer día de Fallas',
      18: 'Cuarto día de Fallas',
      19: 'Último día de Fallas'
    };

    countdown.hidden = true;
    currentDay.hidden = false;
    currentDay.textContent = textos[ahora.getDate()] || 'Estamos en Fallas';

    if (subtitle) {
      subtitle.textContent = 'Disfruta de los días grandes de nuestra fiesta.';
    }

    return;
  }

  countdown.hidden = false;
  currentDay.hidden = true;

  if (subtitle) {
    subtitle.textContent = 'La ilusión, la pólvora y la germanor están cada día más cerca.';
  }

  const diferencia = Math.max(0, objetivo - ahora);
  const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
  const horas = Math.floor((diferencia / (1000 * 60 * 60)) % 24);
  const minutos = Math.floor((diferencia / (1000 * 60)) % 60);
  const segundos = Math.floor((diferencia / 1000) % 60);

  document.getElementById('fallas-days').textContent = String(dias);
  document.getElementById('fallas-hours').textContent = String(horas).padStart(2, '0');
  document.getElementById('fallas-minutes').textContent = String(minutos).padStart(2, '0');
  document.getElementById('fallas-seconds').textContent = String(segundos).padStart(2, '0');
}

actualizarCuentaAtrasFallas();
setInterval(actualizarCuentaAtrasFallas, 1000);


function initHeroCounters() {
  const counters = document.querySelectorAll(".counter");

  if (!counters.length) return;

  counters.forEach((counter) => {
    const target = Number(counter.dataset.target || 0);
    const duration = 1400;
    const startTime = performance.now();

    function updateCounter(currentTime) {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);

      const easeOut = 1 - Math.pow(1 - progress, 3);
      const currentValue = Math.floor(easeOut * target);

      counter.textContent = currentValue;

      if (progress < 1) {
        requestAnimationFrame(updateCounter);
      } else {
        counter.textContent = target;
      }
    }

    requestAnimationFrame(updateCounter);
  });
}

document.addEventListener("DOMContentLoaded", initHeroCounters);