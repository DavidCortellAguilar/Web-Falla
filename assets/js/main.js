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