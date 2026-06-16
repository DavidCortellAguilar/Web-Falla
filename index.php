<?php
$apiActosUrl = 'https://app.fssaf.es/api/ultimos-actos.php';

$actosApi = [];
$proximosActos = [];
$ultimosActos = [];

$response = @file_get_contents($apiActosUrl);

if ($response !== false) {
    $json = json_decode($response, true);

    if (!empty($json['success']) && !empty($json['actos'])) {
        $actosApi = $json['actos'];
    }
}

$hoy = date('Y-m-d');

foreach ($actosApi as $acto) {
    if (empty($acto['fecha'])) {
        continue;
    }

    $fechaActo = date('Y-m-d', strtotime($acto['fecha']));

    if ($fechaActo >= $hoy) {
        $proximosActos[] = $acto;
    } else {
        $ultimosActos[] = $acto;
    }
}

usort($proximosActos, function($a, $b) {
    return strtotime($a['fecha']) <=> strtotime($b['fecha']);
});

usort($ultimosActos, function($a, $b) {
    return strtotime($b['fecha']) <=> strtotime($a['fecha']);
});

$proximosActos = array_slice($proximosActos, 0, 3);
$ultimosActos = array_slice($ultimosActos, 0, 3);

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function actoDataAttrs(array $acto): string {
    $fecha = !empty($acto['fecha']) ? date('d/m/Y', strtotime($acto['fecha'])) : '';
    $hora = '';
    if (!empty($acto['hora'])) {
        $hora = substr((string)$acto['hora'], 0, 5);
    }

    $datos = [
        'titulo' => (string)($acto['titulo'] ?? 'Acto'),
        'descripcion' => (string)($acto['descripcion'] ?? 'Acto de la comisión fallera.'),
        'fecha' => $fecha,
        'hora' => $hora,
        'ubicacion' => (string)($acto['ubicacion'] ?? ($acto['lugar'] ?? '')),
        'imagen' => (string)($acto['imagen'] ?? 'assets/img/icon-192.png'),
    ];

    return ' data-act-title="' . e($datos['titulo']) . '"'
        . ' data-act-description="' . e($datos['descripcion']) . '"'
        . ' data-act-date="' . e($datos['fecha']) . '"'
        . ' data-act-time="' . e($datos['hora']) . '"'
        . ' data-act-location="' . e($datos['ubicacion']) . '"'
        . ' data-act-image="' . e($datos['imagen']) . '"';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Falla San Sebastián Arzobispo Fuero | Falla en Godella</title>

  <meta name="description" content="Falla San Sebastián Arzobispo Fuero de Godella. Consulta actos falleros, representantes, galería, ubicación del casal y actualidad de la comisión.">
  <meta name="keywords" content="Falla San Sebastián Arzobispo Fuero, Falla Godella, fallas Godella, comisión fallera Godella, actos falleros Godella, casal fallero Godella, representantes falleros, fallas Valencia">
  <meta name="author" content="Falla San Sebastián Arzobispo Fuero">

  <link rel="canonical" href="https://fssaf.es/">

  <meta property="og:title" content="Falla San Sebastián Arzobispo Fuero | Falla en Godella">
  <meta property="og:description" content="Comisión fallera de Godella. Consulta nuestros actos, representantes, galería, ubicación del casal y actualidad fallera.">
  <meta property="og:image" content="https://fssaf.es/assets/img/hero2.webp">
  <meta property="og:url" content="https://fssaf.es/">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Falla San Sebastián Arzobispo Fuero">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Falla San Sebastián Arzobispo Fuero | Falla en Godella">
  <meta name="twitter:description" content="Actos, representantes, galería y actualidad de la Falla San Sebastián Arzobispo Fuero de Godella.">
  <meta name="twitter:image" content="https://fssaf.es/assets/img/hero2.webp">

  <link rel="icon" type="image/png" href="assets/img/icon-192.png?v=4">
  <link rel="preload" as="image" href="assets/img/hero2.webp" fetchpriority="high">
  <link rel="preload" as="image" href="assets/img/icon-192.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime('assets/css/style.css') ?>">
</head>

<body>

<div class="cursor-glow"></div>

<header class="site-header" id="siteHeader">
  <div class="container nav">
    <a href="#inicio" class="logo">
      <img src="assets/img/icon-192.png" alt="Logo Falla San Sebastián Arzobispo Fuero" width="58" height="58">
      <strong>Falla San Sebastián<br>Arzobispo Fuero</strong>
    </a>

    <button class="menu-btn" id="menuBtn" aria-label="Abrir menú">☰</button>

    <nav id="mainNav">
      <a href="#inicio">Inicio</a>
      <a href="#quienes-somos">¿Quiénes somos?</a>
      <a href="#actos">Actos</a>
      <a href="#representantes">Representantes</a>
      <a href="#galeria">Galería</a>
      <a href="https://app.fssaf.es/" class="btn-login">Área fallera</a>
    </nav>
  </div>
</header>

<section class="hero" id="inicio">
  <div class="container hero-content reveal">
    <span class="hero-kicker">Tradición · Cultura · Germanor</span>

    <h1>Falla San Sebastián<br>Arzobispo Fuero</h1>

    <p>
      Una comisión viva, cercana y participativa donde la tradición fallera,
      la música, la pólvora y la convivencia se viven durante todo el año.
    </p>

    <div class="hero-actions">
      <a href="#actos" class="btn-primary">Ver Actos</a>

      <div class="nav-socials hero-socials" aria-label="Redes sociales">
        <a href="https://www.instagram.com/fallasansebastian?igsh=N2N3NGp5NzRsZjZw" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.76 3.76 0 0 0 4 7.75v8.5A3.76 3.76 0 0 0 7.75 20h8.5A3.76 3.76 0 0 0 20 16.25v-8.5A3.76 3.76 0 0 0 16.25 4h-8.5Zm8.75 2.05a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
          </svg>
        </a>

        <a href="https://www.instagram.com/juntajovefssaf?igsh=MTAwMXJzbWpxdjlmcw==" target="_blank" rel="noopener noreferrer" aria-label="Instagram Junta Jove">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.76 3.76 0 0 0 4 7.75v8.5A3.76 3.76 0 0 0 7.75 20h8.5A3.76 3.76 0 0 0 20 16.25v-8.5A3.76 3.76 0 0 0 16.25 4h-8.5Zm8.75 2.05a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
          </svg>
        </a>

        <a href="https://www.facebook.com/p/Asociaci%C3%B3n-Cultural-Falla-San-Sebasti%C3%A1n-Arzobispo-Fuero-100064805763681/?locale=es_ES" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M14 8.5V6.75c0-.5.4-.75.86-.75H17V2.2A24.3 24.3 0 0 0 13.89 2C10.8 2 8.7 3.88 8.7 7.27V8.5H5.25v4.25H8.7V22H13v-9.25h3.37L17 8.5h-3Z"/>
          </svg>
        </a>

        <a href="https://www.tiktok.com/@fallasansebastian?_r=1&_t=ZN-96jZAeTTSiM" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M16.6 2c.28 2.36 1.62 3.77 3.9 3.92v3.7a7.8 7.8 0 0 1-3.86-.96v6.86c0 3.47-2.08 6.48-6.17 6.48-3.27 0-5.97-2.12-5.97-5.46 0-3.82 3.4-6.04 7.18-5.32v3.9c-1.6-.5-3.28.18-3.28 1.42 0 1.06.92 1.72 1.98 1.72 1.55 0 2.1-.95 2.1-2.7V2h4.12Z"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <div class="container hero-glass-cards hero-stats">
  <div class="hero-glass-card hero-stat-card reveal">
    <span>👥</span>
    <div>
      <h3><small>+</small><strong class="counter" data-target="180">0</strong></h3>
      <p>Falleros en la comisión</p>
    </div>
  </div>

  <div class="hero-glass-card hero-stat-card reveal">
    <span>🏛️</span>
    <div>
      <h3><strong class="counter" data-target="15">0</strong></h3>
      <p>Años de historia</p>
    </div>
  </div>

  <div class="hero-glass-card hero-stat-card reveal">
    <span>🎉</span>
    <div>
      <h3><strong class="counter" data-target="100">0</strong><small>%</small></h3>
      <p>Germanor</p>
    </div>
  </div>
</div>
</section>

<section class="section about-section" id="quienes-somos">
  <div class="container about-grid">
    <div class="about-content reveal">
      <div class="about-top">
        <div class="about-text">
          <span class="section-label">Quiénes somos</span>

          <h2>Falla San Sebastián Arzobispo Fuero</h2>

          <p>
            Somos una comisión fallera de Godella que vive la fiesta durante todo el año,
            manteniendo viva la tradición, la cultura valenciana, la música, la pólvora,
            la convivencia y la germanor entre falleros, familias y vecinos.
          </p>

          <p>
            Nuestra falla es un punto de encuentro donde cada acto, cena, presentación,
            monumento y celebración forma parte de una historia compartida por toda la comisión.
          </p>
        </div>

        <div class="about-logo">
          <img src="assets/img/icon-192.png" alt="Logo Falla San Sebastián Arzobispo Fuero">
        </div>
      </div>
    </div>
  </div>

  <div class="container map-wrapper reveal">
    <div class="map-info">
      <span class="section-label">Dónde estamos</span>
      <h2>Ubicación de nuestro Casal</h2>
      <p>
        Encuéntranos en Godella y ven a disfrutar de nuestros actos, celebraciones
        y actividades falleras.
      </p>
    </div>

    <div class="map-card">
      <iframe
        src="https://www.google.com/maps?q=Falla%20San%20Sebasti%C3%A1n%20Arzobispo%20Fuero%20Godella&output=embed"
        width="100%"
        height="100%"
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>
</section>

<section class="section" id="actos">
  <div class="container">
    <div class="section-title reveal">
      <span>Agenda fallera</span>
      <h2>Próximos actos</h2>
      <p>Consulta los próximos eventos y actividades de nuestra comisión.</p>
    </div>

    <div class="cards-grid actos-grid <?= count($proximosActos) < 3 ? 'actos-grid-center' : '' ?>">
      <?php if (!empty($proximosActos)): ?>
        <?php foreach ($proximosActos as $acto): ?>
          <article class="event-card reveal" tabindex="0" role="button" aria-label="Ver detalles de <?= e($acto['titulo'] ?? 'Acto') ?>" <?= actoDataAttrs($acto) ?>>
            <div class="event-image-wrap">
            <?php if (!empty($acto['imagen'])): ?>
              <img class="event-img" src="<?= e($acto['imagen']) ?>" alt="<?= e($acto['titulo']) ?>" loading="lazy">
            <?php else: ?>
              <img class="event-img event-img-contain" src="assets/img/icon-192.png" alt="<?= e($acto['titulo']) ?>" loading="lazy">
            <?php endif; ?>
            </div>

            <div class="event-content">
              <span class="event-date">
                <?= e(date('d/m/Y', strtotime($acto['fecha']))) ?>
              </span>

              <h3><?= e($acto['titulo']) ?></h3>

              <p>
                <?= e(mb_strimwidth($acto['descripcion'] ?? 'Acto de la comisión fallera.', 0, 120, '...')) ?>
              </p>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-actos reveal">
          Nuevos Actos Próximamente...
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section light" id="ultimos-actos">
  <div class="container">
    <div class="section-title reveal">
      <span>Actualidad</span>
      <h2>Últimos actos</h2>
      <p>Revive los últimos actos y momentos importantes de nuestra comisión.</p>
    </div>

    <div class="cards-grid">
      <?php if (!empty($ultimosActos)): ?>
        <?php foreach ($ultimosActos as $acto): ?>
          <article class="event-card reveal" tabindex="0" role="button" aria-label="Ver detalles de <?= e($acto['titulo'] ?? 'Acto') ?>" <?= actoDataAttrs($acto) ?>>
            <div class="event-image-wrap">
            <?php if (!empty($acto['imagen'])): ?>
              <img class="event-img" src="<?= e($acto['imagen']) ?>" alt="<?= e($acto['titulo']) ?>" loading="lazy">
            <?php else: ?>
              <img class="event-img event-img-contain" src="assets/img/icon-192.png" alt="<?= e($acto['titulo']) ?>" loading="lazy">
            <?php endif; ?>
            </div>

            <div class="event-content">
              <span class="event-date">
                <?= e(date('d/m/Y', strtotime($acto['fecha']))) ?>
              </span>

              <h3><?= e($acto['titulo']) ?></h3>

              <p>
                <?= e(mb_strimwidth($acto['descripcion'] ?? 'Acto de la comisión fallera.', 0, 120, '...')) ?>
              </p>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="sin-actos no-actos reveal">
          No hay actos anteriores
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section light" id="representantes">
  <div class="container">
    <div class="section-title reveal">
      <span>Representantes</span>
      <h2>Representantes del ejercicio 2025/2026</h2>
      <p>Las personas que representan con orgullo a nuestra comisión.</p>
    </div>

    <div class="representantes-cards-grid reveal">
      <article class="representante-persona-card">
        <img src="assets/img/ramon.webp" alt="Presidente Falla San Sebastián Arzobispo Fuero Ramón Andreu Tamarit">
        <span>Presidente</span>
        <h3>Ramón Andreu Tamarit</h3>
      </article>

      <article class="representante-persona-card">
        <img src="assets/img/cristina.webp" alt="Fallera Mayor Falla San Sebastián Arzobispo Fuero Cristina Menchón Sánchez">
        <span>Fallera Mayor</span>
        <h3>Cristina Menchón Sánchez</h3>
      </article>

      <article class="representante-persona-card">
        <img src="assets/img/natxo.webp" alt="Presidente Infantil Falla San Sebastián Arzobispo Fuero Natxo Sánchez Contreras">
        <span>Presidente Infantil</span>
        <h3>Natxo Sánchez Contreras</h3>
      </article>

      <article class="representante-persona-card">
        <img src="assets/img/nerea.webp" alt="Fallera Mayor Infantil Falla San Sebastián Arzobispo Fuero Nerea Álamo Montero">
        <span>Fallera Mayor Infantil</span>
        <h3>Nerea Álamo<br>Montero</h3>
      </article>
    </div>
  </div>
</section>

<section class="section" id="galeria">
  <div class="container">
    <div class="section-title reveal">
      <span>Momentos</span>
      <h2>Galería fallera</h2>
      <p>Imágenes que resumen nuestra historia, actos y celebraciones.</p>
    </div>

    <div class="gallery-grid">
      <img class="reveal" src="assets/img/nereanacho.webp" alt="Presidente Infantil y Fallera Mayor Infantil Falla San Sebastián Arzobispo Fuero" loading="lazy">
      <img class="reveal" src="assets/img/ramoncristina.webp" alt="Presidente y Fallera Mayor Falla San Sebastián Arzobispo Fuero" loading="lazy">
      <img class="reveal" src="assets/img/juveniles.webp" alt="Juveniles Falla San Sebastián Arzobispo Fuero" loading="lazy">
      <img class="reveal" src="assets/img/carroza.webp" alt="Carroza Falla San Sebastián Arzobispo Fuero" loading="lazy">
      <img class="reveal" src="assets/img/falleros.webp" alt="Premios Falla San Sebastián Arzobispo Fuero" loading="lazy">
      <img class="reveal" src="assets/img/falleras.webp" alt="Falleras Falla San Sebastián Arzobispo Fuero" loading="lazy">
      <img class="reveal" src="assets/img/premiosfalla.webp" alt="Premios Falla San Sebastián Arzobispo Fuero" loading="lazy">
      <img class="reveal" src="assets/img/cenafalla.webp" alt="Cena Representantes Falla San Sebastián Arzobispo Fuero" loading="lazy">
    </div>
  </div>
</section>

<section class="section fallas-countdown-section" id="fallas-countdown-section">
  <div class="container">
    <div class="fallas-countdown-card reveal">
      <div class="fallas-countdown-info">
        <span class="section-label">Cuenta atrás fallera</span>
        <h2>¡Ya queda menos para Fallas!🔥</h2>
        <p id="fallas-countdown-subtitle">La ilusión, la pólvora y la germanor están cada día más cerca.</p>
        <p>¡Apúntate ya y no te las pierdas!</p>
      </div>

      <div class="fallas-countdown-box" aria-live="polite">
        <div id="fallas-countdown" class="fallas-countdown-grid">
          <div class="fallas-time-item">
            <strong id="fallas-days">--</strong>
            <span>Días</span>
          </div>
          <div class="fallas-time-item">
            <strong id="fallas-hours">--</strong>
            <span>Horas</span>
          </div>
          <div class="fallas-time-item">
            <strong id="fallas-minutes">--</strong>
            <span>Min</span>
          </div>
          <div class="fallas-time-item">
            <strong id="fallas-seconds">--</strong>
            <span>Seg</span>
          </div>
        </div>
        <div id="fallas-current-day" class="fallas-current-day" hidden></div>
      </div>
    </div>
  </div>
</section>

<section class="section cuotas-section" id="tarifas">
  <div class="container">
    <div class="section-title reveal">
      <span>Hazte fallero/a</span>
      <h2>Cuotas Faller@s</h2>
      <p>Forma parte de nuestra comisión y elige la modalidad que mejor se adapte a ti: sin lotería, con lotería semanal, Navidad/Niño o toda la lotería.</p>
    </div>

    <div class="cuotas-hero reveal">
      <div class="cuota-destacada">
        <h3>Comisión Mayor</h3>
        <div class="precio">Desde 21,70€/mes</div>
        <p>Importe anual desde 260,40€ con toda la lotería incluida.</p>
      </div>

      <div class="cuota-destacada">
        <h3>Comisión Infantil</h3>
        <div class="precio">Desde 10,40€/mes</div>
        <p>Importe anual desde 124,80€ con toda la lotería incluida.</p>
      </div>
    </div>

    <div class="cuotas-grid reveal">
      <div class="cuota-card">
        <h3>Sin lotería</h3>
        <div class="fila"><span>Adultos</span><strong style="color: var(--orange);">29,00€/mes</strong></div>
        <div class="fila"><span>Importe anual adultos</span><strong>348,00€</strong></div>
        <div class="fila"><span>Infantiles</span><strong style="color: var(--orange);">13,50€/mes</strong></div>
        <div class="fila"><span>Importe anual infantiles</span><strong>162,00€</strong></div>
        <p class="cuota-info">Sin papeletas mensuales ni papeletas de Navidad/Niño.</p>
      </div>

      <div class="cuota-card">
        <h3>Lotería Navidad/Niño</h3>
        <div class="fila"><span>Adultos</span><strong style="color: var(--orange);">25,00€/mes</strong></div>
        <div class="fila"><span>Papeletas Navidad/Niño adultos</span><strong>40</strong></div>
        <div class="fila"><span>Importe anual adultos</span><strong>300,00€</strong></div>
        <div class="fila"><span>Infantiles</span><strong style="color: var(--orange);">11,50€/mes</strong></div>
        <div class="fila"><span>Papeletas Navidad/Niño infantiles</span><strong>20</strong></div>
        <div class="fila"><span>Importe anual infantiles</span><strong>138,00€</strong></div>
      </div>

      <div class="cuota-card">
        <h3>Lotería semanal</h3>
        <div class="fila"><span>Adultos</span><strong style="color: var(--orange);">25,70€/mes</strong></div>
        <div class="fila"><span>Papeletas mensuales adultos</span><strong>3</strong></div>
        <div class="fila"><span>Importe anual adultos</span><strong>308,40€</strong></div>
        <div class="fila"><span>Infantiles</span><strong style="color: var(--orange);">12,40€/mes</strong></div>
        <div class="fila"><span>Papeletas mensuales infantiles</span><strong>1</strong></div>
        <div class="fila"><span>Importe anual infantiles</span><strong>148,80€</strong></div>
      </div>

      <div class="cuota-card cuota-premium">
        <h3>Toda la lotería</h3>
        <div class="fila"><span>Adultos</span><strong style="color: var(--orange);">21,70€/mes</strong></div>
        <div class="fila"><span>Papeletas mensuales adultos</span><strong>3</strong></div>
        <div class="fila"><span>Papeletas Navidad/Niño adultos</span><strong>40</strong></div>
        <div class="fila"><span>Importe anual adultos</span><strong>260,40€</strong></div>
        <div class="fila"><span>Infantiles</span><strong style="color: var(--orange);">10,40€/mes</strong></div>
        <div class="fila"><span>Papeletas mensuales infantiles</span><strong>1</strong></div>
        <div class="fila"><span>Papeletas Navidad/Niño infantiles</span><strong>20</strong></div>
        <div class="fila"><span>Importe anual infantiles</span><strong>124,80€</strong></div>
      </div>
    </div>

    <div class="descuentos-familia reveal">
      <h3>Descuentos para familias</h3>
      <p>Cuantos más integrantes formen parte de la comisión, mayor será el descuento aplicado.</p>

      <div class="familia-grid">
        <div>
          <strong>1 Faller@</strong>
          <span>0€/mes por persona</span>
          <small>0€/año por familia</small>
        </div>

        <div>
          <strong>2 Faller@s</strong>
          <span>1€/mes por persona</span>
          <small>12€/año por familia</small>
        </div>

        <div>
          <strong>3 Faller@s</strong>
          <span>1,50€/mes por persona</span>
          <small>30€/año por familia</small>
        </div>

        <div>
          <strong>4 Faller@s</strong>
          <span>2€/mes por persona</span>
          <small>54€/año por familia</small>
        </div>

        <div>
          <strong>5 Faller@s</strong>
          <span>2,50€/mes por persona</span>
          <small>84€/año por familia</small>
        </div>
      </div>
    </div>
    
    <div class="descuentos-familia reveal">
      <h3>Información Adicional</h3>
      <div class="notas-cuotas">
        <small>* Los infantiles menores de 4 años no pagan cuotas.</small><br>
        <small>* Si cumplen 4 años durante el ejercicio actual pagarán cuota de infantil.</small><br>
        <small>* Los infantiles pasan a comisión mayor si cumplen 15 años durante el ejercicio.</small>
      </div>
    </div>
  </div>
</section>

<section class="section contact-section" id="contacto">
  <div class="container contact-card reveal">
    <div>
      <h2>¿Quieres saber más de la falla?</h2>
      <p>Visítanos o accede al área fallera para consultar actos, avisos y actividades de la comisión.</p>
    </div>

    <a href="https://app.fssaf.es/" class="btn-primary">Ir al área fallera</a>
  </div>
</section>

<footer class="footer">
  <div class="container footer-content">
    <p>© <?= date('Y') ?> Falla San Sebastián Arzobispo Fuero</p>
    <div class="nav-socials hero-socials" aria-label="Redes sociales">
        <a href="https://www.instagram.com/fallasansebastian?igsh=N2N3NGp5NzRsZjZw" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.76 3.76 0 0 0 4 7.75v8.5A3.76 3.76 0 0 0 7.75 20h8.5A3.76 3.76 0 0 0 20 16.25v-8.5A3.76 3.76 0 0 0 16.25 4h-8.5Zm8.75 2.05a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
          </svg>
        </a>

        <a href="https://www.instagram.com/juntajovefssaf?igsh=MTAwMXJzbWpxdjlmcw==" target="_blank" rel="noopener noreferrer" aria-label="Instagram Junta Jove">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.76 3.76 0 0 0 4 7.75v8.5A3.76 3.76 0 0 0 7.75 20h8.5A3.76 3.76 0 0 0 20 16.25v-8.5A3.76 3.76 0 0 0 16.25 4h-8.5Zm8.75 2.05a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
          </svg>
        </a>

        <a href="https://www.facebook.com/p/Asociaci%C3%B3n-Cultural-Falla-San-Sebasti%C3%A1n-Arzobispo-Fuero-100064805763681/?locale=es_ES" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M14 8.5V6.75c0-.5.4-.75.86-.75H17V2.2A24.3 24.3 0 0 0 13.89 2C10.8 2 8.7 3.88 8.7 7.27V8.5H5.25v4.25H8.7V22H13v-9.25h3.37L17 8.5h-3Z"/>
          </svg>
        </a>

        <a href="https://www.tiktok.com/@fallasansebastian?_r=1&_t=ZN-96jZAeTTSiM" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M16.6 2c.28 2.36 1.62 3.77 3.9 3.92v3.7a7.8 7.8 0 0 1-3.86-.96v6.86c0 3.47-2.08 6.48-6.17 6.48-3.27 0-5.97-2.12-5.97-5.46 0-3.82 3.4-6.04 7.18-5.32v3.9c-1.6-.5-3.28.18-3.28 1.42 0 1.06.92 1.72 1.98 1.72 1.55 0 2.1-.95 2.1-2.7V2h4.12Z"/>
          </svg>
        </a>
      </div>
  </div>
</footer>

<script src="assets/js/main.js?v=9" defer></script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Falla San Sebastián Arzobispo Fuero",
  "url": "https://fssaf.es/",
  "logo": "https://fssaf.es/assets/img/icon-192.png",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Godella",
    "addressRegion": "Valencia",
    "addressCountry": "ES"
  },
  "sameAs": [
    "https://www.instagram.com/fallasansebastian",
    "https://www.facebook.com/p/Asociaci%C3%B3n-Cultural-Falla-San-Sebasti%C3%A1n-Arzobispo-Fuero-100064805763681/"
  ]
}
</script>
</body>
</html>