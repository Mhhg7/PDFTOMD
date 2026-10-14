(() => {
  const DESIGN_W = 390;
  const DESIGN_H = 844;

  const app = document.getElementById('app');
  const track = document.getElementById('track');
  const slides = [...track.children];
  const dotsEl = document.getElementById('dots');
  const menu = document.getElementById('menu');
  const menuBtn = document.getElementById('menuBtn');
  const menuList = document.getElementById('menuList');
  const nextBtn = document.getElementById('nextBtn');
  const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)');
  let current = 0;

  /* ── Fit the 390×844 stage to the real screen ── */
  function fit() {
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    let w = vw, h = vh, s;
    const framed = vw > 520; // desktop preview: show a phone-sized frame
    if (framed) {
      s = Math.min(1, (vh - 32) / DESIGN_H);
      w = Math.round(DESIGN_W * s);
      h = Math.round(DESIGN_H * s);
    } else {
      s = Math.min(vw / DESIGN_W, vh / DESIGN_H);
    }
    app.style.setProperty('--w', w + 'px');
    app.style.setProperty('--h', h + 'px');
    app.style.setProperty('--s', s);
    app.classList.toggle('is-framed', framed);
    // keep the current page aligned after a resize / rotation
    slides[current].scrollIntoView({ behavior: 'auto', inline: 'start', block: 'nearest' });
  }

  /* ── Pager dots + menu list ── */
  slides.forEach((slide, i) => {
    const title = slide.dataset.title;

    const li = document.createElement('li');
    const dot = document.createElement('button');
    dot.type = 'button';
    dot.className = 'dot';
    dot.setAttribute('aria-label', title);
    dot.addEventListener('click', () => go(i));
    li.appendChild(dot);
    dotsEl.appendChild(li);

    const item = document.createElement('li');
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'menu__item';
    btn.innerHTML = `<small>${String(i + 1).padStart(2, '0')}</small><span></span>`;
    btn.lastChild.textContent = title;
    btn.addEventListener('click', () => { closeMenu(); go(i); });
    item.appendChild(btn);
    menuList.appendChild(item);
  });
  const dots = [...dotsEl.querySelectorAll('.dot')];
  const menuItems = [...menuList.querySelectorAll('.menu__item')];

  /* ── Navigation ── */
  function go(i) {
    i = Math.max(0, Math.min(slides.length - 1, i));
    slides[i].scrollIntoView({
      behavior: reduceMotion.matches ? 'auto' : 'smooth',
      inline: 'start',
      block: 'nearest',
    });
  }

  function setActive(i) {
    current = i;
    slides.forEach((s, n) => s.classList.toggle('is-active', n === i));
    dots.forEach((d, n) => d.setAttribute('aria-current', String(n === i)));
    menuItems.forEach((m, n) => m.setAttribute('aria-current', String(n === i)));
    nextBtn.classList.toggle('is-last', i === slides.length - 1);
    nextBtn.setAttribute('aria-label', i === slides.length - 1 ? 'العودة إلى الرئيسية' : 'الصفحة التالية');
    const id = slides[i].id;
    if (id) history.replaceState(null, '', '#' + id);
  }

  const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting && e.intersectionRatio >= 0.6) setActive(slides.indexOf(e.target));
    });
  }, { root: track, threshold: [0.6] });
  slides.forEach((s) => io.observe(s));

  nextBtn.addEventListener('click', () => go(current === slides.length - 1 ? 0 : current + 1));

  // buttons / links that jump to a page
  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-go]');
    if (!t) return;
    e.preventDefault();
    closeMenu();
    go(Number(t.dataset.go));
  });

  /* ── Menu ── */
  function openMenu() {
    menu.hidden = false;
    menuBtn.setAttribute('aria-expanded', 'true');
  }
  function closeMenu() {
    menu.hidden = true;
    menuBtn.setAttribute('aria-expanded', 'false');
  }
  menuBtn.addEventListener('click', () => (menu.hidden ? openMenu() : closeMenu()));

  /* ── Keyboard + desktop wheel (RTL: visual left = next) ── */
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
    else if (e.key === 'ArrowLeft') go(current + 1);
    else if (e.key === 'ArrowRight') go(current - 1);
    else if (e.key === 'Home') go(0);
    else if (e.key === 'End') go(slides.length - 1);
  });

  let wheelLock = false;
  track.addEventListener('wheel', (e) => {
    const d = Math.abs(e.deltaY) > Math.abs(e.deltaX) ? e.deltaY : e.deltaX;
    if (!d) return;
    e.preventDefault();
    if (wheelLock || Math.abs(d) < 8) return;
    wheelLock = true;
    go(current + (d > 0 ? 1 : -1));
    setTimeout(() => { wheelLock = false; }, 650);
  }, { passive: false });

  /* ── Init ── */
  window.addEventListener('resize', fit);
  window.addEventListener('orientationchange', fit);
  fit();

  const start = slides.findIndex((s) => '#' + s.id === location.hash);
  if (start > 0) slides[start].scrollIntoView({ behavior: 'auto', inline: 'start', block: 'nearest' });
  setActive(Math.max(0, start));
})();
