/* Al-Qawsan Scientific Bureau - website app.
   Vanilla JS, hash-routed, bilingual (EN / AR with RTL). Markup uses the design
   system's component classes (qs-btn, qs-card, qs-badge, qs-timeline,
   qs-field, qs-table, qs-footer) so it renders exactly as components/bundle.css
   specifies, without a React dependency. HexPattern geometry is ported from
   components/bundle.js. */

(function () {
  "use strict";

  var Q = window.QS;
  var MAP = window.IRAQ_MAP;
  var root = document.documentElement;
  var app = document.getElementById("app");

  /* ------------------------------------------------------------ state */
  function store(k, v) {
    try { if (v === undefined) return localStorage.getItem(k); localStorage.setItem(k, v); } catch (e) { return null; }
  }
  var lang = store("qs-lang") === "ar" ? "ar" : "en";
  var route = "home";
  var productArea = "all";
  var productQuery = "";
  var slideIdx = 0;
  var slideTimer = null;
  var mapSel = "IQ-BG";
  var firstRender = true;

  function t(o) { if (o == null) return ""; if (typeof o === "string") return o; return o[lang] != null ? o[lang] : o.en; }
  function u(k) { return Q.UI[lang][k]; }
  function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]; }); }
  function fmt(s) { var a = [].slice.call(arguments, 1); return s.replace(/%s/g, function () { return a.shift(); }); }
  var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ------------------------------------------------------------ icons (Lucide, ISC) */
  var I = {
    search: '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
    menu: '<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/>',
    x: '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
    chevDown: '<path d="m6 9 6 6 6-6"/>',
    chevRight: '<path d="m9 18 6-6-6-6"/>',
    chevLeft: '<path d="m15 18-6-6 6-6"/>',
    arrowRight: '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
    arrowUp: '<path d="m5 12 7-7 7 7"/><path d="M12 19V5"/>',
    mapPin: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
    phone: '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
    mail: '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
    clock: '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    globe: '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>',
    linkedin: '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>',
    fileCheck: '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/>',
    chartUp: '<path d="M22 7 13.5 15.5 8.5 10.5 2 17"/><path d="M16 7h6v6"/>',
    stethoscope: '<path d="M11 2v2"/><path d="M5 2v2"/><path d="M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"/><path d="M8 15a6 6 0 0 0 12 0v-3"/><circle cx="20" cy="10" r="2"/>',
    thermometer: '<path d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"/>',
    truck: '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
    hospital: '<path d="M12 6v4"/><path d="M14 14h-4"/><path d="M14 18h-4"/><path d="M14 8h-4"/><path d="M18 12h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2h2"/><path d="M18 22V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v18"/>',
    handshake: '<path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"/><path d="m21 3 1 11h-2"/><path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3"/><path d="M3 4h8"/>',
    shieldCheck: '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
    shield: '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>',
    pill: '<path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/>',
    flask: '<path d="M10 2v7.527a2 2 0 0 1-.211.896L4.72 20.55a1 1 0 0 0 .9 1.45h12.76a1 1 0 0 0 .9-1.45l-5.069-10.127A2 2 0 0 1 14 9.527V2"/><path d="M8.5 2h7"/><path d="M7 16h10"/>',
    heart: '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
    droplet: '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>',
    wind: '<path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"/><path d="M9.6 4.6A2 2 0 1 1 11 8H2"/><path d="M12.6 19.4A2 2 0 1 0 14 16H2"/>',
    activity: '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
    sun: '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>',
    users: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    user: '<circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/>',
    warehouse: '<path d="M22 8.35V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8.35A2 2 0 0 1 3.26 6.5l8-3.2a2 2 0 0 1 1.48 0l8 3.2A2 2 0 0 1 22 8.35Z"/><path d="M6 18h12"/><path d="M6 14h12"/><rect width="12" height="12" x="6" y="10"/>',
    calendar: '<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>',
    image: '<rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
    info: '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
    check: '<path d="M20 6 9 17l-5-5"/>',
    checkCircle: '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
    copy: '<rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>',
    briefcase: '<path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/>',
    external: '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>'
  };
  var DIR_ICONS = { chevRight: 1, chevLeft: 1, arrowRight: 1 };
  function icon(name, cls) {
    return '<svg class="icon ' + (cls || "") + (DIR_ICONS[name] ? " icon--dir" : "") + '" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' + I[name] + "</svg>";
  }

  /* The supplied logo lockup (Arc + Arabic + English wordmarks). Never mirrored. */
  function logo(cls) {
    return '<img class="' + cls + '" src="assets/logos/alqawsan-horizontal-color.png" width="399" height="234" alt="' + esc(lang === "ar" ? "مكتب القوسان العلمي، Al-Qawsan Scientific Bureau" : "Al-Qawsan Scientific Bureau, مكتب القوسان العلمي") + '">';
  }

  /* ------------------------------------------------------------ design-system pieces */
  function badge(text, tone) {
    return '<span class="qs-badge qs-badge--' + (tone || "warning") + ' tbc"><span class="qs-badge__dot" aria-hidden="true"></span><span>' + text + "</span></span>";
  }
  function tbc() { return badge(u("tbc"), "warning"); }
  function val(v) { return v === Q.TBC ? tbc() : t(v); }
  function btn(label, href, variant, extra) {
    return '<a class="qs-btn qs-btn--' + (variant || "primary") + " " + (extra || "") + '" href="#' + href + '"><span>' + label + "</span></a>";
  }
  function overline(text) { return '<p class="qs-overline">' + text + "</p>"; }

  /* HexPattern, ported from design-system/components/bundle.js. */
  function hexPoints(x, y, r) {
    var p = [];
    for (var i = 0; i < 6; i++) { var a = Math.PI / 180 * (60 * i); p.push((x + r * Math.cos(a)).toFixed(1) + "," + (y + r * Math.sin(a)).toFixed(1)); }
    return p.join(" ");
  }
  var HEX_TONES = {
    navy: { frame: "var(--indigo-400)", node: "var(--indigo-400)", opacity: "var(--opacity-pattern-strong)" },
    orange: { frame: "var(--orange-200)", node: "var(--orange-200)", opacity: "0.35" },
    light: { frame: "var(--navy-800)", node: "var(--orange-500)", line: "var(--orange-500)", opacity: "1" },
    watermark: { frame: "var(--navy-800)", node: "var(--navy-800)", opacity: "var(--opacity-watermark)" }
  };
  function hexField(tone, w, h, cell, opacity) {
    var T = HEX_TONES[tone], s = "", rr = cell, sw = Math.max(1, rr * 0.05), sx = rr * 1.5, sy = rr * Math.sqrt(3), col = 0;
    for (var x = 0; x <= w + rr; x += sx) {
      var off = col % 2 ? sy / 2 : 0;
      for (var y = -sy; y <= h + sy; y += sy) {
        s += '<polygon points="' + hexPoints(x, y + off, rr) + '" fill="none" stroke="' + T.frame + '" stroke-width="' + sw + '" stroke-linejoin="round"/>';
        if ((col + Math.round(y / sy)) % 3 === 0) s += '<circle cx="' + (x + rr).toFixed(1) + '" cy="' + (y + off).toFixed(1) + '" r="' + (sw * 3) + '" fill="none" stroke="' + T.node + '" stroke-width="' + sw + '"/>';
      }
      col++;
    }
    return '<svg class="hexbg" viewBox="0 0 ' + w + " " + h + '" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false" style="opacity:' + (opacity || T.opacity) + '">' + s + "</svg>";
  }

  /* ------------------------------------------------------------ navigation helpers */
  var SEC = {}; Q.NAV.forEach(function (n) { SEC[n.id] = n; });
  function secOf(id) { var p = Q.PAGES[id]; return p ? p.sec : null; }
  function hrefOf(navId) { var n = SEC[navId]; return n.kids ? n.kids[0] : navId; }
  function titleOf(id) { return id === "home" ? u("home") : t(Q.PAGES[id].t); }

  /* ------------------------------------------------------------ header */
  function headerHTML() {
    var cur = route === "home" ? "home" : secOf(route);
    var nav = Q.NAV.map(function (n) {
      var active = n.id === cur;
      var link = '<a class="nav__link" href="#' + hrefOf(n.id) + '"' + (n.id === "home" && route === "home" ? ' aria-current="page"' : "") + ">" + t(n.t) + (n.kids ? icon("chevDown") : "") + "</a>";
      var mega = "";
      if (n.kids) {
        mega = '<div class="mega"><p class="mega__head">' + t(n.t) + "</p>" + n.kids.map(function (k) {
          return '<a href="#' + k + '"' + (k === route ? ' aria-current="page"' : "") + ">" + t(Q.PAGES[k].t) + "</a>";
        }).join("") + "</div>";
      }
      return '<li class="nav__item' + (active ? " is-active" : "") + '">' + link + mega + "</li>";
    }).join("");
    return '' +
      '<div class="mainbar"><div class="wrap mainbar__row">' +
        '<a class="brand" href="#home">' + logo("brand__logo") + "</a>" +
        '<nav class="nav" aria-label="' + u("mainNav") + '"><ul class="nav__list">' + nav + "</ul></nav>" +
        '<div class="hdr-tools">' +
          '<button class="iconbtn hdr-search" type="button" data-act="search" aria-label="' + u("searchLabel") + '">' + icon("search") + "</button>" +
          '<span class="lang" role="group" aria-label="Language / اللغة">' +
            '<button type="button" lang="en" data-lang="en" aria-pressed="' + (lang === "en") + '">EN</button>|' +
            '<button type="button" lang="ar" data-lang="ar" aria-pressed="' + (lang === "ar") + '">عربي</button>' +
          "</span>" +
          '<a class="qs-btn qs-btn--accent hdr-cta" href="#partners-join">' + icon("handshake", "icon--md") + "<span>" + u("cta") + "</span></a>" +
          '<button class="iconbtn burger" type="button" data-act="drawer" aria-label="' + u("menu") + '" aria-expanded="false">' + icon("menu") + "</button>" +
        "</div>" +
      "</div></div>";
  }

  function drawerHTML() {
    var items = Q.NAV.map(function (n) {
      if (!n.kids) return '<a href="#' + n.id + '"' + (route === n.id ? ' aria-current="page"' : "") + ">" + t(n.t) + "</a>";
      var open = secOf(route) === n.id ? " open" : "";
      return "<details" + open + "><summary>" + t(n.t) + icon("chevDown") + "</summary>" + n.kids.map(function (k) {
        return '<a href="#' + k + '"' + (k === route ? ' aria-current="page"' : "") + ">" + t(Q.PAGES[k].t) + "</a>";
      }).join("") + "</details>";
    }).join("");
    return '<div class="scrim" data-act="close"></div>' +
      '<div class="drawer" role="dialog" aria-modal="true" aria-label="' + u("menu") + '">' +
        '<div class="drawer__head"><span class="brand">' + logo("brand__logo brand__logo--sm") + '</span><button class="iconbtn" type="button" data-act="close" aria-label="' + u("close") + '">' + icon("x") + "</button></div>" +
        '<nav class="drawer__body" aria-label="' + u("mainNav") + '"><a href="#" class="drawer-search" data-act="search">' + u("searchLabel") + icon("search") + "</a>" + items + btn(u("cta"), "partners-join", "accent") + "</nav>" +
      "</div>";
  }

  function searchHTML() {
    return '<div class="scrim" data-act="close"></div>' +
      '<div class="search" role="dialog" aria-modal="true" aria-label="' + u("searchLabel") + '"><div class="wrap"><div class="search__panel">' +
        '<div class="search__row"><div class="qs-field"><label class="qs-field__label" for="q-input">' + u("searchLabel") + '</label>' +
        '<input class="qs-input" id="q-input" type="search" autocomplete="off" placeholder="' + esc(u("searchPh")) + '"></div>' +
        '<button class="iconbtn" type="button" data-act="close" aria-label="' + u("close") + '">' + icon("x") + "</button></div>" +
        '<div class="search__results" id="q-results" aria-live="polite"></div>' +
      "</div></div></div>";
  }

  /* ------------------------------------------------------------ footer */
  function footerHTML() {
    var quick = ["about-who", "partners-list", "products-areas", "media-news", "careers-why", "contact-office"];
    var ar = lang === "ar";
    var strip = [
      { ic: "mapPin", k: u("fLocation"), v: u("address") },
      { ic: "mail", k: u("fEmail"), v: '<bdi dir="ltr">info@alqawsangroup.com</bdi>' },
      { ic: "phone", k: u("fCall"), v: '<bdi dir="ltr">+964 XXX XXX XXXX</bdi>' }
    ].map(function (x) {
      return '<li><span class="ftr__ico">' + icon(x.ic) + '</span><span class="ftr__kv"><span class="ftr__k">' + x.k + '</span><span class="ftr__v">' + x.v + "</span></span></li>";
    }).join("");
    return '<footer class="ftr' + (ar ? " qs-ar" : "") + '">' +
      '<div class="ftr__arch">' + hexField("navy", 1280, 480, 56, "var(--opacity-pattern)") +
        '<div class="wrap ftr__stripwrap"><ul class="ftr__strip" aria-label="' + u("contact") + '">' + strip + "</ul></div>" +
        '<div class="ftr__panel">' + hexField("watermark", 1280, 520, 60) +
          '<div class="wrap ftr__cols">' +
            '<div class="ftr__about">' + logo("ftr__logo") +
              '<p class="ftr__text">' + u("footerAbout") + "</p>" +
              '<p class="ftr__meta"><strong>' + u("group") + ":</strong> CAS Development, " + (ar ? "مكتب لارا العلمي، مكتب سنايا العلمي" : "Lara Scientific Office, Sanaya Scientific Office") + "</p>" +
              '<p class="ftr__meta">' + icon("clock", "icon--sm") + u("hours") + "</p>" +
              '<div class="ftr__social"><a href="https://www.linkedin.com/company/al-qawsan-group" target="_blank" rel="noopener" aria-label="LinkedIn">' + icon("linkedin", "icon--md") + '</a><a href="https://alqawsangroup.com" target="_blank" rel="noopener" aria-label="www.alqawsangroup.com">' + icon("globe", "icon--md") + "</a></div></div>" +
            '<nav aria-labelledby="ftr-q"><h2 class="ftr__title" id="ftr-q">' + u("quick") + "</h2><ul>" + quick.map(function (k) { return '<li><a href="#' + k + '">' + t(Q.PAGES[k].t) + "</a></li>"; }).join("") + "</ul></nav>" +
            '<nav aria-labelledby="ftr-s"><h2 class="ftr__title" id="ftr-s">' + u("services") + "</h2><ul>" + Q.SERVICES.map(function (x) { return '<li><a href="#' + x.id + '">' + t(x.t) + "</a></li>"; }).join("") + "</ul></nav>" +
            '<div class="ftr__news"><h2 class="ftr__title">' + u("newsTitle") + '</h2><p class="ftr__text">' + u("newsText") + "</p>" +
              '<form class="ftr__form" data-news novalidate><label class="qs-field__label" for="f-news-email">' + u("fEmail") + '</label>' +
                '<input class="qs-input ftr__input" id="f-news-email" name="email" type="email" dir="ltr" autocomplete="email" required placeholder="' + u("newsPh") + '">' +
                '<button class="qs-btn qs-btn--primary ftr__btn" type="submit">' + u("subscribe") + "</button>" +
                '<p class="ftr__fine">' + u("newsPrivacy") + ' <a href="#privacy">' + u("privacy") + "</a></p></form>" +
              '<div class="ftr__done" hidden tabindex="-1"><p class="ftr__done-h">' + icon("checkCircle", "icon--md") + u("newsDone") + '</p><p class="ftr__fine">' + u("newsPreview") + "</p></div>" +
            "</div>" +
          "</div>" +
          '<div class="wrap ftr__bottom"><span aria-hidden="true"></span>' +
            '<div class="ftr__legal"><p>© <bdi dir="ltr">2026</bdi> ' + u("brand") + " | " + u("rights") + '</p><nav aria-label="' + u("sitemap") + '"><a href="#privacy">' + u("privacy") + '</a><a href="#terms">' + u("terms") + '</a><a href="#sitemap">' + u("sitemap") + "</a></nav></div>" +
            '<button class="ftr__top" type="button" data-act="top" aria-label="' + u("toTop") + '">' + icon("arrowUp") + "</button>" +
          "</div>" +
        "</div>" +
      "</div></footer>";
  }

  /* ------------------------------------------------------------ Iraq map */
  var GOV = {}; if (MAP) MAP.govs.forEach(function (g) { GOV[g.id] = g; });
  function isHub(id) { return Q.HUBS.indexOf(id) > -1; }
  function km(a, b) {
    var R = 6371, toR = Math.PI / 180;
    var dLat = (b[0] - a[0]) * toR, dLon = (b[1] - a[1]) * toR;
    var x = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(a[0] * toR) * Math.cos(b[0] * toR) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
    return Math.round(2 * R * Math.asin(Math.sqrt(x)) / 5) * 5;
  }

  function mapSVG(kind) {
    if (!MAP) return "";
    var bg = GOV["IQ-BG"], s = "";
    var interactive = kind !== "mini";
    MAP.govs.forEach(function (g) {
      var name = lang === "ar" ? g.ar : g.en;
      var cls = "gov" + (g.id === mapSel && interactive ? " is-on" : "") + (g.id === "IQ-BG" ? " is-hq" : "");
      s += '<path class="' + cls + '" data-gov="' + g.id + '" d="' + g.d + '"' +
        (interactive ? ' tabindex="0" role="button" aria-label="' + esc(name) + '" aria-pressed="' + (g.id === mapSel) + '"' : "") + "><title>" + esc(name) + "</title></path>";
    });
    if (kind !== "mini") {
      Q.HUBS.forEach(function (id) {
        if (id === "IQ-BG") return;
        var g = GOV[id], x1 = bg.c[0], y1 = bg.c[1], x2 = g.c[0], y2 = g.c[1];
        var mx = (x1 + x2) / 2, my = (y1 + y2) / 2, dx = x2 - x1, dy = y2 - y1;
        var bend = { "IQ-NI": 0.22, "IQ-AR": -0.2, "IQ-BA": 0.12, "IQ-NA": -0.18 }[id] || 0.15;
        var cx = mx - dy * bend, cy = my + dx * bend;
        s += '<path class="conn" pathLength="1" d="M' + x1 + " " + y1 + "Q" + cx.toFixed(1) + " " + cy.toFixed(1) + " " + x2 + " " + y2 + '"/>';
      });
      MAP.govs.forEach(function (g) { if (!isHub(g.id)) s += '<circle class="cap" cx="' + g.c[0] + '" cy="' + g.c[1] + '" r="2.6"/>'; });
      Q.HUBS.forEach(function (id) { var g = GOV[id]; s += '<circle class="hub" cx="' + g.c[0] + '" cy="' + g.c[1] + '" r="' + (id === "IQ-BG" ? 8 : 6.5) + '"/>'; });
      s += '<circle class="hq-ring" cx="' + bg.c[0] + '" cy="' + bg.c[1] + '" r="14"/>';
    } else {
      s += '<circle class="hub" cx="' + bg.c[0] + '" cy="' + bg.c[1] + '" r="9"/><circle class="hq-ring" cx="' + bg.c[0] + '" cy="' + bg.c[1] + '" r="16"/>';
    }
    var anim = kind === "hero" && firstRender ? " iraq--animate" : "";
    return '<svg class="iraq iraq--' + kind + anim + '" viewBox="-6 -6 ' + (MAP.w + 12) + " " + (MAP.h + 12) + '" role="group" aria-label="' + esc(u("mapTitle")) + '">' + s + "</svg>";
  }

  function readoutHTML(id) {
    var g = GOV[id]; if (!g) return "";
    var name = lang === "ar" ? g.ar : g.en, alt = lang === "ar" ? g.en : g.ar;
    var status = id === "IQ-BG" ? badge(u("mapHq"), "accent") : isHub(id) ? badge(u("mapHub"), "accent") : badge(u("mapServed"), "brand");
    var dist = id === "IQ-BG" ? '<span class="readout__hint">' + u("mapHere") + "</span>" :
      "<span><b>" + u("mapDistance") + ':</b> <span class="readout__km"><bdi dir="ltr">' + km(GOV["IQ-BG"].ll, g.ll) + '</bdi></span> <span class="muted">' + u("mapKm") + "</span></span>";
    return '<div class="readout__top"><span><span class="readout__name">' + name + '</span> <span class="readout__alt" lang="' + (lang === "ar" ? "en" : "ar") + '">' + alt + "</span></span>" + status + "</div>" +
      '<div class="readout__meta"><span><b>' + u("mapCapital") + ":</b> " + (lang === "ar" ? g.capAr : g.cap) + "</span>" + dist + "</div>";
  }

  function mapBlock(kind) {
    return '<div class="mapcard" data-map>' +
      '<figure class="mapfig">' + mapSVG(kind) + "</figure>" +
      '<div class="readout" aria-live="polite" data-readout>' + readoutHTML(mapSel) + "</div>" +
      '<div class="maplegend"><span><i></i><bdi dir="ltr">18</bdi> ' + u("mapGovs") + '</span><span><i class="pin"></i><bdi dir="ltr">5</bdi> ' + u("mapHubs") + "</span><span>" + u("mapHint") + "</span></div>" +
    "</div>";
  }

  function bindMaps(scope) {
    scope.querySelectorAll("[data-map]").forEach(function (card) {
      var out = card.querySelector("[data-readout]");
      function pick(id) {
        mapSel = id;
        card.querySelectorAll(".gov").forEach(function (p) { var on = p.getAttribute("data-gov") === id; p.classList.toggle("is-on", on); p.setAttribute("aria-pressed", on); });
        if (out) out.innerHTML = readoutHTML(id);
        document.querySelectorAll("[data-govbtn]").forEach(function (b) { b.setAttribute("aria-pressed", b.getAttribute("data-govbtn") === id); });
      }
      card.addEventListener("pointerover", function (e) { var p = e.target.closest(".gov"); if (p && e.pointerType === "mouse") pick(p.getAttribute("data-gov")); });
      card.addEventListener("click", function (e) { var p = e.target.closest(".gov"); if (p) pick(p.getAttribute("data-gov")); });
      card.addEventListener("focusin", function (e) { var p = e.target.closest(".gov"); if (p) pick(p.getAttribute("data-gov")); });
      card.addEventListener("keydown", function (e) { var p = e.target.closest(".gov"); if (p && (e.key === "Enter" || e.key === " ")) { e.preventDefault(); pick(p.getAttribute("data-gov")); } });
      card._pick = pick;
    });
    scope.querySelectorAll("[data-govbtn]").forEach(function (b) {
      b.addEventListener("click", function () { var c = scope.querySelector("[data-map]"); if (c && c._pick) c._pick(b.getAttribute("data-govbtn")); });
    });
  }

  /* ------------------------------------------------------------ home */
  function homeHTML() {
    var slides = Q.SLIDES.map(function (s, i) {
      return '<div class="slide" id="slide-' + i + '" role="group" aria-roledescription="slide" aria-label="' + u("slide") + " " + (i + 1) + ' / 3"' + (i === slideIdx ? "" : " hidden") + ">" +
        "<h2>" + t(s.h) + "</h2>" +
        "<p>" + t(s.p) + "</p>" +
        '<div class="slide__actions">' + btn(t(s.a.t), s.a.href, "primary") + btn(u("ctaContact"), "contact-inquiry", "secondary") + "</div></div>";
    }).join("");
    var dots = Q.SLIDES.map(function (s, i) { return '<button type="button" data-slide="' + i + '" aria-label="' + u("slide") + " " + (i + 1) + '" aria-current="' + (i === slideIdx) + '"></button>'; }).join("");

    var stats = Q.STATS.map(kpiCard).join("");

    var services = Q.SERVICES.map(function (s) {
      return '<a class="qs-card svc" href="#' + s.id + '"><div class="card-top"><h3 class="qs-heading qs-card__title">' + t(s.t) + '</h3><span class="icon-circle">' + icon(s.icon) + "</span></div>" + '<p class="qs-card__body">' + t(s.d) + '</p><span class="link-more">' + u("readMore") + icon("arrowRight", "icon--sm") + "</span></a>";
    }).join("");

    var partners = partnerTile(true) + [1, 2, 3, 4, 5, 6].map(function () { return '<div class="ptile ptile--slot">' + (lang === "ar" ? "شعار الشريك" : "Partner logo") + "</div>"; }).join("");

    var areas = Q.AREAS.map(function (a) { return '<button type="button" class="area" data-area="' + a.id + '"><span class="icon-circle">' + icon(a.icon, "icon--md") + "</span>" + t(a.t) + "</button>"; }).join("");

    return '' +
      '<section class="hero" aria-labelledby="home-h1">' + hexField("watermark", 1280, 640, 60) +
        '<h1 class="sr-only" id="home-h1" tabindex="-1">' + u("brand") + ": " + u("tagline") + "</h1>" +
        '<div class="wrap hero__grid" style="position:relative">' +
          '<div class="slides" aria-roledescription="carousel" id="slide-h">' + overline(u("brand")) + slides +
            '<div class="slider-ctl"><button class="iconbtn" type="button" data-slide-step="-1" aria-label="' + u("prev") + '">' + icon("chevLeft", "icon--md") + '</button><div class="dots">' + dots + '</div><button class="iconbtn" type="button" data-slide-step="1" aria-label="' + u("next") + '">' + icon("chevRight", "icon--md") + "</button></div>" +
          "</div>" +
          mapBlock("hero") +
        "</div>" +
      "</section>" +

      '<section class="band band--alt" aria-label="' + (lang === "ar" ? "أرقامنا" : "Key figures") + '"><div class="wrap"><div class="stats">' + stats + '</div><p class="stats-banner">' + t(Q.STATS_BANNER) + "</p></div></section>" +

      '<section class="band"><div class="wrap about-teaser"><div class="about-teaser__copy">' + overline(t(SEC.about.t)) +
        '<h2 class="' + (lang === "ar" ? "ar-h2" : "h2") + '">' + u("aboutTeaserH") + "</h2>" +
        '<p class="' + (lang === "ar" ? "ar-body-lg" : "body-lg") + '">' + t(Q.PAGES["about-who"].blocks[0].p[0]) + "</p>" +
        "<div>" + btn(u("readMore"), "about-who", "secondary") + "</div></div>" +
        photoSlot(lang === "ar" ? "صورة: المكتب الرئيسي، حي القادسية (تُرفق لاحقاً)" : "Photo: head office, Qadisiyah District (to be supplied)") +
      "</div></section>" +

      '<section class="band band--alt"><div class="wrap"><div class="shead shead--row"><div>' + overline(t(SEC.services.t)) + '<h2 class="' + (lang === "ar" ? "ar-h2" : "h2") + '">' + u("servicesIntro") + "</h2></div></div>" +
        '<div class="grid grid--3">' + services + "</div></div></section>" +

      '<section class="band band--navy">' + hexField("navy", 1280, 420, 56, "var(--opacity-pattern)") + '<div class="wrap">' +
        '<div class="shead shead--row qs-on-dark"><div>' + '<p class="qs-overline">' + t(SEC.partners.t) + '</p><h2 class="' + (lang === "ar" ? "ar-h2" : "h2") + '" style="color:var(--text-inverse)">' + u("partnersIntro") + "</h2></div>" +
        '<div class="strip-ctl"><button class="iconbtn" type="button" data-strip="-1" aria-label="' + u("prev") + '">' + icon("chevLeft", "icon--md") + '</button><button class="iconbtn" type="button" data-strip="1" aria-label="' + u("next") + '">' + icon("chevRight", "icon--md") + "</button></div></div>" +
        '<div class="partner-strip" id="pstrip" tabindex="0" aria-label="' + t(SEC.partners.t) + '">' + partners + "</div>" +
        '<p style="margin-top:var(--space-5)"><a class="link-more" href="#partners-list">' + t(Q.PAGES["partners-list"].t) + icon("arrowRight", "icon--sm") + "</a></p>" +
      "</div></section>" +

      '<section class="band band--alt"><div class="wrap"><div class="shead">' + overline(t(SEC.products.t)) + '<h2 class="' + (lang === "ar" ? "ar-h2" : "h2") + '">' + u("areasIntro") + "</h2></div>" +
        '<div class="grid grid--4">' + areas + "</div></div></section>" +

      '<section class="band"><div class="wrap"><div class="shead shead--row"><div>' + overline(t(SEC.media.t)) + '<h2 class="' + (lang === "ar" ? "ar-h2" : "h2") + '">' + u("newsIntro") + "</h2></div>" +
        '<a class="link-more" href="#media-news">' + u("newsAll") + icon("arrowRight", "icon--sm") + "</a></div>" +
        '<div class="grid grid--3">' + newsCards() + "</div></div></section>" +

      ctaBand();
  }

  /* Key-figure card: formal label and outlined icon ring on top, the figure, then a
     fact chip and a line of context. */
  function kpiCard(s) {
    var chip = s.chip ? '<span class="kpi__chip">' + icon(s.chip.icon, "icon--sm") + "<span>" + t(s.chip.t) + "</span></span>" : "";
    return '<div class="kpi"><div class="kpi__top"><p class="kpi__label">' + t(s.label) + '</p><span class="icon-circle icon-circle--lg">' + icon(s.icon) + "</span></div>" +
      '<p class="kpi__value"><bdi dir="ltr">' + s.v + '</bdi></p><p class="kpi__foot">' + chip + '<span class="kpi__sub">' + t(s.sub) + "</span></p></div>";
  }

  function photoSlot(caption, sq) {
    return '<figure class="photo' + (sq ? " photo--sq" : "") + '" style="margin:0">' + hexField("navy", 400, 300, 40, "var(--opacity-pattern)") +
      '<figcaption class="photo__cap">' + icon("image", "icon--sm") + caption + "</figcaption></figure>";
  }
  function partnerTile(link) {
    var inner = '<span><span class="ptile__name" dir="ltr">SIPHAT</span><br><span class="ptile__sub">' + (lang === "ar" ? "تونس، منذ 2025" : "Tunisia, since 2025") + "</span></span>";
    return link ? '<a class="ptile" href="#partners-siphat">' + inner + "</a>" : '<div class="ptile">' + inner + "</div>";
  }
  function newsCards() {
    var n = Q.NEWS[0];
    var real = '<a class="qs-card news-card" href="#' + n.id + '"><span class="news-date"><bdi dir="ltr">' + n.date + '</bdi></span><h3 class="qs-heading qs-card__title">' + t(n.t) + '</h3><p class="qs-card__body">' + t(n.d) + '</p><span class="link-more">' + u("readMore") + icon("arrowRight", "icon--sm") + "</span></a>";
    var slot = '<div class="qs-card news-card news-card--slot"><span class="news-date">' + tbc() + '</span><h3 class="qs-heading qs-card__title">' + u("draftSlot") + '</h3><p class="qs-card__body">' + u("draftSlotText") + "</p></div>";
    return real + slot + slot;
  }
  function ctaBand() {
    return '<section class="cta-band"><div class="wrap cta-band__row"><div><h2>' + u("ctaTitle") + "</h2><p>" + u("ctaText") + '</p></div><div class="cta-band__actions">' +
      btn(u("cta"), "partners-join", "primary") + btn(u("ctaContact"), "contact-inquiry", "secondary") + "</div></div></section>";
  }

  /* ------------------------------------------------------------ inner pages */
  function pageHTML(id) {
    var p = Q.PAGES[id];
    var sec = p.sec ? SEC[p.sec] : null;
    var crumbs = '<nav class="crumbs" aria-label="' + u("breadcrumb") + '"><ol><li><a href="#home">' + u("home") + "</a></li>" +
      (sec ? "<li>" + icon("chevRight") + '<a href="#' + hrefOf(sec.id) + '">' + t(sec.t) + "</a></li>" : "") +
      (p.parent ? "<li>" + icon("chevRight") + '<a href="#' + p.parent + '">' + t(Q.PAGES[p.parent].t) + "</a></li>" : "") +
      "<li>" + icon("chevRight") + '<span aria-current="page">' + t(p.t) + "</span></li></ol></nav>";
    var banner = '<section class="banner">' + hexField("navy", 1280, 360, 52, "var(--opacity-pattern)") + '<div class="wrap">' + crumbs +
      (p.icon ? '<span class="banner__icon">' + icon(p.icon) + "</span>" : "") +
      (p.date ? '<p class="news-date" style="color:var(--text-on-dark-muted)"><bdi dir="ltr">' + p.date + "</bdi></p>" : "") +
      '<h1 tabindex="-1">' + t(p.t) + '</h1><p class="banner__lead">' + t(p.lead) + "</p></div></section>";

    var rail = "";
    if (sec && sec.kids) {
      rail = '<aside class="rail" aria-label="' + u("inSection") + '"><p class="rail__title">' + t(sec.t) + "</p><ul>" + sec.kids.map(function (k) {
        return '<li><a href="#' + k + '"' + ((k === id || k === p.parent) ? ' aria-current="page"' : "") + ">" + t(Q.PAGES[k].t) + "</a></li>";
      }).join("") + "</ul></aside>";
    }
    var blocks = p.blocks.map(block).join("");
    var rel = relatedHTML(id, p);
    return banner + '<div class="wrap inner' + (rail ? "" : " inner--full") + '">' + rail + '<div class="content">' + blocks + rel + "</div></div>" + (p.noCta ? "" : ctaBand());
  }

  function relatedHTML(id, p) {
    var ids = p.rel;
    if (!ids) {
      var sec = p.sec ? SEC[p.sec] : null;
      if (!sec || !sec.kids) return "";
      ids = sec.kids.filter(function (k) { return k !== id; }).slice(0, 3);
    }
    return '<section class="related" aria-labelledby="rel-h"><h2 id="rel-h">' + u("related") + '</h2><div class="grid grid--3">' + ids.map(function (k) {
      return '<a class="qs-card" href="#' + k + '"><h3 class="qs-heading qs-card__title">' + t(Q.PAGES[k].t) + '</h3><span class="link-more">' + u("readMore") + icon("arrowRight", "icon--sm") + "</span></a>";
    }).join("") + "</div></section>";
  }

  function h2(o) { return o ? "<h2>" + t(o) + "</h2>" : ""; }

  function block(b) {
    switch (b.type) {
      case "text":
        return '<section class="blk">' + h2(b.h) + '<div class="prose">' + b.p.map(function (x) { return "<p>" + t(x) + "</p>"; }).join("") + "</div></section>";
      case "list":
        return '<section class="blk">' + h2(b.h) + '<ul class="checks">' + b.items.map(function (x) { return "<li>" + icon("check") + "<span>" + t(x) + "</span></li>"; }).join("") + "</ul></section>";
      case "features":
        return '<section class="blk">' + h2(b.h) + '<div class="grid grid--2">' + b.items.map(function (x) {
          return '<div class="qs-card"><div class="feat"><span class="icon-circle">' + icon(x.icon) + '</span><div><h3 class="qs-heading qs-card__title">' + t(x.t) + '</h3><p class="qs-card__body">' + t(x.d) + "</p></div></div></div>";
        }).join("") + "</div></section>";
      case "steps":
        return '<section class="blk">' + h2(b.h) + '<ol class="grid grid--4 steps" style="list-style:none;margin:0;padding:0">' + b.items.map(function (x, i) {
          return '<li class="qs-card qs-card--' + (i % 2 ? "accent" : "dark") + (i % 2 ? "" : " qs-on-dark") + '"><span class="qs-card__number">' + (i + 1) + '</span><h3 class="qs-heading qs-card__title">' + t(x.t) + '</h3><p class="qs-card__body">' + t(x.d) + "</p></li>";
        }).join("") + "</ol></section>";
      case "pair":
        return '<section class="blk pair"><div class="grid grid--2">' + b.items.map(function (x, i) {
          return '<div class="qs-card qs-card--' + (i ? "accent" : "dark") + (i ? "" : " qs-on-dark") + '"><h2 class="qs-heading qs-card__title">' + t(x.t) + '</h2><p class="qs-card__body">' + t(x.d) + "</p></div>";
        }).join("") + "</div></section>";
      case "values":
        return '<section class="blk">' + h2(b.h) + '<div class="grid grid--3">' + b.items.map(function (x) {
          return '<div class="qs-card"><h3 class="qs-heading qs-card__title">' + t(x.t) + '</h3><p class="qs-card__body">' + t(x.d) + "</p></div>";
        }).join("") + "</div></section>";
      case "stats":
        return '<section class="blk"><div class="stats">' + Q.STATS.map(kpiCard).join("") + '</div><p class="stats-banner">' + t(Q.STATS_BANNER) + "</p></section>";
      case "timeline":
        return '<section class="blk"><div class="qs-timeline qs-timeline--horizontal">' + b.items.map(function (x) {
          return '<div class="qs-timeline__item"><span class="qs-timeline__rule" aria-hidden="true"></span><span class="qs-timeline__node" aria-hidden="true"></span>' +
            '<p class="qs-timeline__year">' + (typeof x.y === "string" ? '<bdi dir="ltr">' + x.y + "</bdi>" : t(x.y)) + '</p><p class="qs-heading qs-timeline__title">' + t(x.t) + '</p><p class="qs-timeline__text">' + t(x.d) + "</p></div>";
        }).join("") + "</div></section>";
      case "people":
        return '<section class="blk"><div class="grid grid--4">' + b.items.map(function (r) {
          return '<div class="qs-card person"><span class="person__ph">' + icon("user") + '</span><h3 class="qs-heading qs-card__title">' + t(r) + "</h3>" + tbc() + "</div>";
        }).join("") + "</div></section>";
      case "note":
        return '<p class="note">' + icon("info", "icon--md") + "<span>" + t(b.text) + "</span></p>";
      case "source":
        return '<p class="source">' + t(b.text) + ' <a href="' + b.href + '" target="_blank" rel="noopener">' + (lang === "ar" ? "رابط المصدر" : "Open source") + "</a></p>";
      case "facts":
        return '<dl class="facts">' + b.items.map(function (x) { return "<div><dt>" + t(x.k) + "</dt><dd>" + val(x.v) + "</dd></div>"; }).join("") + "</dl>";
      case "temps": return tempsHTML();
      case "map":
        return '<section class="blk"><h2>' + u("mapTitle") + "</h2>" + mapBlock("full") + '<div class="filters" role="group" aria-label="' + u("mapTitle") + '">' +
          MAP.govs.slice().sort(function (a, c) { return (lang === "ar" ? a.ar.localeCompare(c.ar, "ar") : a.en.localeCompare(c.en)); }).map(function (g) {
            return '<button type="button" class="chip" data-govbtn="' + g.id + '" aria-pressed="' + (g.id === mapSel) + '">' + (lang === "ar" ? g.ar : g.en) + (isHub(g.id) ? " ●" : "") + "</button>";
          }).join("") + "</div></section>";
      case "hubs":
        return '<section class="blk"><h2>' + u("mapHubs").replace(/^./, function (c) { return c.toUpperCase(); }) + '</h2><div class="grid grid--3">' + Q.HUBS.map(function (id) {
          var g = GOV[id];
          return '<div class="qs-card"><span class="icon-circle">' + icon(id === "IQ-BG" ? "hospital" : "warehouse") + '</span><h3 class="qs-heading qs-card__title">' + (lang === "ar" ? g.capAr : g.cap) + '</h3><p class="qs-card__body">' +
            (id === "IQ-BG" ? u("mapHq") : u("mapHub")) + "</p>" + (id === "IQ-BG" ? '<p class="qs-card__body">' + u("address") + "</p>" : '<p class="qs-card__body">' + (lang === "ar" ? "العنوان وأرقام التواصل: " : "Address and contacts: ") + tbc() + "</p>") + "</div>";
        }).join("") + "</div></section>";
      case "partners": return partnersHTML();
      case "areas":
        return '<section class="blk"><div class="grid grid--4">' + Q.AREAS.map(function (a) { return '<button type="button" class="area" data-area="' + a.id + '"><span class="icon-circle">' + icon(a.icon, "icon--md") + "</span>" + t(a.t) + "</button>"; }).join("") + "</div></section>";
      case "products": return productsHTML();
      case "productDetail": return productDetailHTML();
      case "news":
        return '<section class="blk"><div class="grid grid--3">' + newsCards() + "</div></section>";
      case "events":
        return '<section class="blk"><div class="empty"><span class="icon-circle">' + icon("calendar") + "</span><h2>" + (lang === "ar" ? "لا توجد فعاليات قادمة منشورة حالياً" : "No upcoming events are published yet") + '</h2><p class="muted">' +
          (lang === "ar" ? "ننشر هنا مواعيد اللقاءات العلمية وجلسات التعليم الطبي المستمر والمؤتمرات فور تأكيدها." : "Scientific meetings, CME sessions and conferences are listed here as soon as their dates are confirmed.") + "</p>" + btn(t(Q.PAGES["media-news"].t), "media-news", "secondary") + "</div></section>";
      case "gallery":
        var caps = lang === "ar" ? ["المكتب الرئيسي، بغداد", "المستودع", "غرفة التبريد 2 إلى 8 °م", "أسطول التوزيع", "لقاء علمي", "توقيع اتفاقية SIPHAT"] :
          ["Head office, Baghdad", "Warehouse", "Cold room, 2 to 8 °C", "Delivery fleet", "Scientific meeting", "SIPHAT signing"];
        return '<section class="blk"><div class="grid grid--3">' + caps.map(function (c) { return photoSlot(c, true); }).join("") + '</div><p class="note">' + icon("info", "icon--md") + "<span>" +
          (lang === "ar" ? "صور حقيقية لمستودعاتنا وأسطولنا وفرقنا تُرفق من فريق التسويق، مع طبقة كحلية عند وضع نص عليها." : "Real photographs of our warehouses, fleet and teams are supplied by marketing, with a navy overlay wherever text sits on them.") + "</span></p></section>";
      case "jobs":
        return '<section class="blk"><div class="empty"><span class="icon-circle">' + icon("briefcase") + "</span><h2>" + (lang === "ar" ? "لا توجد وظائف شاغرة منشورة حالياً" : "No open positions are published right now") + '</h2><p class="muted">' +
          (lang === "ar" ? "أرسل سيرتك الذاتية، وسيتواصل معك فريق الموارد البشرية عند توفر وظيفة تناسبك في أحد مراكزنا الخمسة." : "Send your CV and Human Resources will contact you when a matching role opens in one of our five hubs.") + "</p>" + btn(t(Q.PAGES["careers-apply"].t), "careers-apply", "primary") + "</div></section>";
      case "form": return formHTML(b.kind);
      case "office": return officeHTML();
      case "group": return groupHTML();
      case "sitemap":
        return '<section class="blk"><div class="smap"><div><h3><a href="#home">' + u("home") + "</a></h3></div>" + Q.NAV.filter(function (n) { return n.kids; }).map(function (n) {
          return "<div><h3>" + t(n.t) + "</h3><ul>" + n.kids.map(function (k) { return '<li><a href="#' + k + '">' + t(Q.PAGES[k].t) + "</a></li>"; }).join("") + "</ul></div>";
        }).join("") + "<div><h3>" + (lang === "ar" ? "صفحات عامة" : "General") + '</h3><ul><li><a href="#privacy">' + u("privacy") + '</a></li><li><a href="#terms">' + u("terms") + "</a></li></ul></div></div></section>";
    }
    return "";
  }

  function tempsHTML() {
    var W = 640, x0 = 30, x1 = W - 20, max = 30;
    function X(c) { return x0 + (x1 - x0) * c / max; }
    var ticks = "";
    for (var c = 0; c <= max; c += 5) ticks += '<line class="axis" x1="' + X(c) + '" x2="' + X(c) + '" y1="92" y2="98"/><text class="tick" x="' + X(c) + '" y="114" text-anchor="middle">' + c + "</text>";
    var cold = lang === "ar" ? "مبرّد: 2 إلى 8 °م" : "Refrigerated: 2 to 8 °C";
    var room = lang === "ar" ? "حرارة الغرفة: 15 إلى 25 °م" : "Room temperature: 15 to 25 °C";
    return '<section class="blk"><h2>' + (lang === "ar" ? "نطاقات التخزين" : "Storage ranges") + '</h2><figure class="temps" style="margin:0" dir="ltr">' +
      '<svg viewBox="0 0 ' + W + ' 124" role="img" aria-label="' + cold + "; " + room + '">' +
        '<line class="axis" x1="' + x0 + '" x2="' + x1 + '" y1="92" y2="92"/>' + ticks +
        '<rect class="rng-cold" x="' + X(2) + '" y="52" width="' + (X(8) - X(2)) + '" height="28" rx="6"/>' +
        '<rect class="rng-room" x="' + X(15) + '" y="52" width="' + (X(25) - X(15)) + '" height="28" rx="6"/>' +
        '<text class="rng-lbl" x="' + X(2) + '" y="40">' + cold + "</text>" +
        '<text class="rng-lbl" x="' + X(15) + '" y="40">' + room + "</text>" +
      '</svg><figcaption class="temps__cap" dir="' + (lang === "ar" ? "rtl" : "ltr") + '">' + (lang === "ar" ? "درجة الحرارة بالمئوي. المصدر: متطلبات التخزين في ممارسات التوزيع الجيد." : "Temperature in °C. Source: GDP storage requirements.") + "</figcaption></figure></section>";
  }

  function partnersHTML() {
    var slot = function () { return '<div class="ptile ptile--slot">' + (lang === "ar" ? "شعار الشريك" : "Partner logo") + "</div>"; };
    var groups = [
      { t: L2("North Africa", "شمال أفريقيا"), tiles: partnerTile(true) },
      { t: L2("Europe", "أوروبا"), tiles: slot() + slot() + slot() },
      { t: L2("Middle East", "الشرق الأوسط"), tiles: slot() + slot() },
      { t: L2("Asia", "آسيا"), tiles: slot() + slot() }
    ];
    return '<section class="blk">' + groups.map(function (g) { return '<div class="pgroup"><h3>' + t(g.t) + '</h3><div class="ptiles">' + g.tiles + "</div></div>"; }).join("") +
      '<p class="note">' + icon("info", "icon--md") + "<span>" + (lang === "ar" ? "تُعرض شعارات الشركاء بعد موافقتهم، في مربعات بيضاء متساوية الحجم." : "Partner logos are shown with each partner’s approval, in equal white tiles.") + "</span></p></section>";
  }
  function L2(en, ar) { return { en: en, ar: ar }; }

  var PRODUCT_ROWS = ["cardio", "diabetes", "anti-infectives", "respiratory", "gastro", "cns", "oncology", "derma"];
  function productsHTML() {
    var chips = '<button type="button" class="chip" data-parea="all" aria-pressed="' + (productArea === "all") + '">' + u("allAreas") + "</button>" +
      Q.AREAS.map(function (a) { return '<button type="button" class="chip" data-parea="' + a.id + '" aria-pressed="' + (productArea === a.id) + '">' + t(a.t) + "</button>"; }).join("");
    return '<section class="blk">' +
      '<div class="qs-field" style="max-width:420px"><label class="qs-field__label" for="p-search">' + (lang === "ar" ? "ابحث في المنتجات" : "Search products") + '</label><input class="qs-input" id="p-search" type="search" value="' + esc(productQuery) + '" placeholder="' + (lang === "ar" ? "الاسم التجاري أو العلمي" : "Brand or generic name") + '"></div>' +
      '<div class="filters" role="group" aria-label="' + t(Q.PAGES["products-areas"].t) + '">' + chips + "</div>" +
      '<div id="p-table">' + productTable() + "</div></section>";
  }
  function productTable() {
    var areaName = {}; Q.AREAS.forEach(function (a) { areaName[a.id] = t(a.t); });
    var q = productQuery.trim().toLowerCase();
    var rows = PRODUCT_ROWS.filter(function (a) { return (productArea === "all" || productArea === a) && (!q || areaName[a].toLowerCase().indexOf(q) > -1); });
    var ex = lang === "ar";
    var head = ex ? ["المنتج", "الاسم العلمي", "الشكل والتركيز", "الشريك", "المجال العلاجي", "الحالة"] : ["Product", "Generic name (INN)", "Form & strength", "Partner", "Therapeutic area", "Status"];
    var body = rows.length ? rows.map(function (a) {
      return '<tr><td><a href="#products-detail">' + (ex ? "اسم المنتج" : "Product name") + '</a></td><td class="muted">' + (ex ? "الاسم العلمي" : "Generic name") + '</td><td class="muted">' + (ex ? "الشكل الصيدلاني والتركيز" : "Dosage form, strength") + '</td><td class="muted">' + (ex ? "الشريك" : "Partner") + "</td><td>" + areaName[a] + "</td><td>" + badge(u("example"), "info") + "</td></tr>";
    }).join("") : '<tr><td colspan="6">' + (ex ? "لا توجد منتجات مطابقة. امسح البحث أو اختر «جميع المجالات»." : "No products match. Clear the search or choose “All areas”.") + "</td></tr>";
    return '<p class="qs-field__hint" style="margin-bottom:var(--space-2)">' + (ex ? "المصدر: قائمة المنتجات المسجّلة، تُحمَّل عند رفع المحتوى. الصفوف أدناه أمثلة للتصميم." : "Source: the registered product list, loaded at content upload. The rows below are layout examples.") + "</p>" +
      '<div class="table-scroll"><table class="qs-table"><thead><tr>' + head.map(function (h) { return '<th scope="col">' + h + "</th>"; }).join("") + "</tr></thead><tbody>" + body + "</tbody></table></div>";
  }
  function productDetailHTML() {
    var ar = lang === "ar";
    var rows = ar ? ["الاسم التجاري", "الاسم العلمي", "الشكل الصيدلاني والتركيز", "حجم العبوة", "المجال العلاجي", "الشركة المصنّعة", "رقم التسجيل في وزارة الصحة", "ظروف الحفظ", "النشرة الداخلية (PDF)"] :
      ["Brand name", "Generic name (INN)", "Dosage form and strength", "Pack size", "Therapeutic area", "Manufacturer", "Ministry of Health registration no.", "Storage conditions", "Patient leaflet (PDF)"];
    return '<section class="blk"><h2>' + (ar ? "حقول صفحة المنتج" : "Product page fields") + '</h2><div class="table-scroll"><table class="qs-table pd-table"><tbody>' + rows.map(function (r) {
      return '<tr><th scope="row">' + r + '</th><td class="muted">' + (ar ? "من ملف التسجيل المعتمد" : "From the approved registration file") + "</td></tr>";
    }).join("") + "</tbody></table></div></section>";
  }

  function officeHTML() {
    var ar = lang === "ar";
    var copy = function (v) { return '<button type="button" class="copybtn" data-copy="' + v + '">' + icon("copy", "icon--sm") + "<span>" + u("copy") + "</span></button>"; };
    return '<section class="blk office"><div class="qs-card"><ul class="cinfo">' +
      "<li>" + '<span class="icon-circle">' + icon("mapPin") + '</span><div><div class="cinfo__k">' + (ar ? "العنوان" : "Address") + '</div><div class="cinfo__v">' + u("address") + "</div></div></li>" +
      "<li>" + '<span class="icon-circle">' + icon("phone") + '</span><div><div class="cinfo__k">' + (ar ? "الهاتف" : "Phone") + '</div><div class="cinfo__v"><bdi dir="ltr">+964 XXX XXX XXXX</bdi> ' + tbc() + "</div></div></li>" +
      "<li>" + '<span class="icon-circle">' + icon("mail") + '</span><div><div class="cinfo__k">' + (ar ? "البريد الإلكتروني" : "Email") + '</div><div class="cinfo__v"><bdi dir="ltr">info@alqawsangroup.com</bdi> ' + copy("info@alqawsangroup.com") + tbc() + "</div></div></li>" +
      "<li>" + '<span class="icon-circle">' + icon("clock") + '</span><div><div class="cinfo__k">' + (ar ? "ساعات العمل" : "Working hours") + '</div><div class="cinfo__v">' + u("hours") + " " + tbc() + "</div></div></li>" +
      "<li>" + '<span class="icon-circle">' + icon("globe") + '</span><div><div class="cinfo__k">' + (ar ? "الموقع الإلكتروني" : "Website") + '</div><div class="cinfo__v"><a href="https://alqawsangroup.com" target="_blank" rel="noopener" dir="ltr">www.alqawsangroup.com</a></div></div></li>' +
      "</ul></div>" +
      '<div class="mapcard"><figure class="mapfig" style="max-width:360px;margin-inline:auto">' + mapSVG("mini") + "</figure>" +
      '<a class="qs-btn qs-btn--secondary" href="https://www.google.com/maps/search/?api=1&query=Qadisiyah+District+Baghdad+Iraq" target="_blank" rel="noopener">' + icon("external", "icon--md") + "<span>" + (ar ? "افتح في خرائط Google" : "Open in Google Maps") + "</span></a></div></section>" +
      '<section class="blk">' + btn(t(Q.PAGES["contact-inquiry"].t), "contact-inquiry", "primary") + "</section>";
  }

  function groupHTML() {
    var W = 720, H = 360, rtl = lang === "ar";
    function X(x) { return rtl ? W - x : x; }
    var r = 78, sw = Math.max(2, r * 0.07);
    var A = [X(W * 0.3), H * 0.55], B = [X(W * 0.64), H * 0.27], C = [X(W * 0.74), H * 0.73];
    var s = '<line x1="' + A[0] + '" y1="' + A[1] + '" x2="' + B[0] + '" y2="' + B[1] + '" stroke="var(--orange-500)" stroke-width="' + sw * 0.6 + '" stroke-linecap="round"/>' +
      '<line x1="' + A[0] + '" y1="' + A[1] + '" x2="' + C[0] + '" y2="' + C[1] + '" stroke="var(--orange-500)" stroke-width="' + sw * 0.6 + '" stroke-linecap="round"/>' +
      '<polygon points="' + hexPoints(A[0], A[1], r) + '" fill="var(--surface)" stroke="var(--navy-800)" stroke-width="' + sw + '" stroke-linejoin="round"/>' +
      '<polygon points="' + hexPoints(B[0], B[1], r * 0.6) + '" fill="var(--surface)" stroke="var(--navy-800)" stroke-width="' + sw * 0.8 + '" stroke-linejoin="round"/>' +
      '<polygon points="' + hexPoints(C[0], C[1], r * 0.6) + '" fill="var(--surface)" stroke="var(--navy-800)" stroke-width="' + sw * 0.8 + '" stroke-linejoin="round"/>';
    var lbl = function (p, dy, txt, size) { return '<text class="glbl" x="' + p[0] + '" y="' + (p[1] + dy) + '" text-anchor="middle" font-size="' + size + '">' + txt + "</text>"; };
    s += lbl(A, -4, rtl ? "مكتب القوسان" : "Al-Qawsan", 17) + lbl(A, 18, rtl ? "العلمي" : "Scientific Bureau", 13);
    s += lbl(B, 5, "CAS", 14) + lbl(C, 5, rtl ? "شريك التصنيع" : "Manufacturing", 11) + lbl(C, 19, rtl ? "" : "partner", 11);
    var cards = window.QS.GROUP.map(function (g) {
      return '<div class="qs-card"><h3 class="qs-heading qs-card__title"' + (g.t.en === "CAS Development" ? ' dir="ltr"' : "") + ">" + t(g.t) + '</h3><p class="qs-card__body">' + t(g.d) + "</p></div>";
    }).join("");
    return '<section class="blk"><figure class="group-fig" style="margin:0"><svg viewBox="0 0 ' + W + " " + H + '" role="img" aria-label="' + (rtl ? "مجموعة القوسان: مكتب القوسان العلمي مع CAS Development وشريك التصنيع" : "Al-Qawsan Group: the Bureau with CAS Development and the manufacturing partner") + '">' + s + "</svg></figure></section>" +
      '<section class="blk"><h2>' + (rtl ? "شركات المجموعة" : "Group companies") + '</h2><div class="grid grid--3">' + cards + "</div></section>";
  }

  /* ------------------------------------------------------------ forms */
  function F(name, label, type, opts) { return { name: name, label: label, type: type || "text", req: opts && opts.req, span: opts && opts.span, options: opts && opts.options, accept: opts && opts.accept }; }
  function formDef(kind) {
    var ar = lang === "ar";
    var areas = Q.AREAS.map(function (a) { return t(a.t); });
    var hubs = Q.HUBS.map(function (id) { return ar ? GOV[id].capAr : GOV[id].cap; });
    var defs = {
      partner: { h: L2("Partnership request", "طلب شراكة"), f: [
        F("company", L2("Company name", "اسم الشركة"), "text", { req: 1 }),
        F("country", L2("Country", "البلد"), "text", { req: 1 }),
        F("name", L2("Your name", "اسمك"), "text", { req: 1 }),
        F("email", L2("Email", "البريد الإلكتروني"), "email", { req: 1 }),
        F("phone", L2("Phone", "الهاتف"), "tel"),
        F("area", L2("Main therapeutic area", "المجال العلاجي الرئيسي"), "select", { options: areas.concat([ar ? "أخرى" : "Other"]) }),
        F("portfolio", L2("Tell us about your portfolio", "حدّثنا عن محفظة منتجاتك"), "textarea", { req: 1, span: 1 })
      ] },
      inquiry: { h: L2("Send an inquiry", "أرسل استفساراً"), f: [
        F("name", L2("Your name", "اسمك"), "text", { req: 1 }),
        F("org", L2("Organisation", "الجهة"), "text"),
        F("email", L2("Email", "البريد الإلكتروني"), "email", { req: 1 }),
        F("phone", L2("Phone", "الهاتف"), "tel"),
        F("subject", L2("Subject", "الموضوع"), "select", { req: 1, span: 1, options: ar ? ["المبيعات والطلبات", "الشراكة", "المعلومات الطبية", "الوظائف", "أخرى"] : ["Sales and orders", "Partnership", "Medical information", "Careers", "Other"] }),
        F("message", L2("Message", "الرسالة"), "textarea", { req: 1, span: 1 })
      ] },
      medical: { h: L2("Medical information request", "طلب معلومات طبية"), f: [
        F("name", L2("Your name", "اسمك"), "text", { req: 1 }),
        F("role", L2("Profession", "المهنة"), "select", { req: 1, options: ar ? ["طبيب", "صيدلاني", "كادر صحي آخر"] : ["Doctor", "Pharmacist", "Other healthcare professional"] }),
        F("email", L2("Email", "البريد الإلكتروني"), "email", { req: 1 }),
        F("product", L2("Product", "المنتج"), "text", { req: 1 }),
        F("question", L2("Your question", "سؤالك"), "textarea", { req: 1, span: 1 })
      ] },
      apply: { h: L2("Job application", "طلب توظيف"), f: [
        F("name", L2("Full name", "الاسم الكامل"), "text", { req: 1 }),
        F("email", L2("Email", "البريد الإلكتروني"), "email", { req: 1 }),
        F("phone", L2("Phone", "الهاتف"), "tel", { req: 1 }),
        F("role", L2("Area of interest", "المجال المطلوب"), "select", { req: 1, options: ar ? ["مندوب علمي", "صيدلاني", "الشؤون التنظيمية", "المستودعات وسلسلة الإمداد", "أخرى"] : ["Medical representative", "Pharmacist", "Regulatory affairs", "Warehouse and supply chain", "Other"] }),
        F("city", L2("Preferred hub", "المركز المفضّل"), "select", { options: hubs }),
        F("cv", L2("CV (PDF or Word)", "السيرة الذاتية (PDF أو Word)"), "file", { req: 1, accept: ".pdf,.doc,.docx" }),
        F("note", L2("Anything else we should know", "معلومات إضافية"), "textarea", { span: 1 })
      ] }
    };
    return defs[kind];
  }
  function formHTML(kind) {
    var d = formDef(kind), m = Q.FORMS[kind];
    var fields = d.f.map(function (f) {
      var id = "f-" + kind + "-" + f.name;
      var ltr = { email: 1, tel: 1 }[f.type] ? ' dir="ltr"' : "";
      var req = f.req ? " required" : "";
      var ctl;
      if (f.type === "textarea") ctl = '<textarea class="qs-input" id="' + id + '" name="' + f.name + '"' + req + "></textarea>";
      else if (f.type === "select") ctl = '<select class="qs-input" id="' + id + '" name="' + f.name + '"' + req + '><option value="">' + (lang === "ar" ? "اختر" : "Choose") + "</option>" + f.options.map(function (o) { return "<option>" + o + "</option>"; }).join("") + "</select>";
      else ctl = '<input class="qs-input" id="' + id + '" name="' + f.name + '" type="' + f.type + '"' + ltr + req + (f.accept ? ' accept="' + f.accept + '"' : "") + (f.type === "email" ? ' autocomplete="email"' : f.type === "tel" ? ' autocomplete="tel"' : f.name === "name" ? ' autocomplete="name"' : "") + ">";
      return '<div class="qs-field' + (f.span ? " span-2" : "") + '"><label class="qs-field__label" for="' + id + '">' + t(f.label) + (f.req ? '<span class="qs-field__req" aria-hidden="true">*</span>' : "") + "</label>" + ctl + "</div>";
    }).join("");
    return '<section class="blk" data-formwrap>' +
      '<form class="form" data-form="' + kind + '" novalidate><div class="form__head"><h2>' + t(d.h) + '</h2><p class="muted">' + u("formRequired") + "</p></div>" +
        '<div class="form__grid">' + fields + "</div>" +
        '<div class="form__foot"><p class="form__fine">' + fmt(u("formPreview"), t(m.team)) + '</p><button class="qs-btn qs-btn--primary" type="submit">' + u("send") + "</button></div></form>" +
      '<div class="form form-done" hidden><span class="icon-circle">' + icon("checkCircle") + "</span><h2>" + u("formSent") + "</h2><p>" + fmt(u("formReply"), t(m.team), t(m.when)) + '</p><p class="form__fine">' + fmt(u("formPreview"), t(m.team)) + '</p><button class="qs-btn qs-btn--secondary" type="button" data-form-again>' + u("formAgain") + "</button></div>" +
      "</section>";
  }

  /* ------------------------------------------------------------ 404 */
  function notFoundHTML() {
    return '<div class="wrap nf"><p class="display-num" aria-hidden="true">404</p><h1 tabindex="-1">' + u("notFound") + '</h1><p class="muted">' + u("notFoundText") + "</p>" + btn(u("backHome"), "home", "primary") + "</div>";
  }

  /* ------------------------------------------------------------ search */
  function searchIndex() {
    var out = [];
    Object.keys(Q.PAGES).forEach(function (id) {
      var p = Q.PAGES[id];
      out.push({ id: id, title: t(p.t), sec: p.sec ? t(SEC[p.sec].t) : "", hay: (p.t.en + " " + p.t.ar + " " + p.lead.en + " " + p.lead.ar + " " + (p.sec ? SEC[p.sec].t.en + " " + SEC[p.sec].t.ar : "")).toLowerCase() });
    });
    return out;
  }
  function runSearch(q) {
    var box = document.getElementById("q-results"); if (!box) return;
    q = q.trim().toLowerCase();
    if (!q) { box.innerHTML = ""; return; }
    var words = q.split(/\s+/);
    var hits = searchIndex().filter(function (r) { return words.every(function (w) { return r.hay.indexOf(w) > -1; }); }).slice(0, 8);
    box.innerHTML = hits.length ? hits.map(function (r) { return '<a href="#' + r.id + '"><strong>' + r.title + "</strong><small>" + r.sec + "</small></a>"; }).join("") :
      '<p class="muted" style="padding:var(--space-3)">' + fmt(u("searchNone"), esc(q)) + "</p>";
  }

  /* ------------------------------------------------------------ overlays */
  var overlay = document.getElementById("overlay");
  var lastFocus = null;
  function openOverlay(kind) {
    lastFocus = document.activeElement;
    overlay.innerHTML = kind === "drawer" ? drawerHTML() : searchHTML();
    overlay.hidden = false;
    document.body.style.overflow = "hidden";
    var burger = document.querySelector(".burger"); if (burger && kind === "drawer") burger.setAttribute("aria-expanded", "true");
    var f = kind === "search" ? document.getElementById("q-input") : overlay.querySelector(".drawer [data-act=close]");
    if (f) f.focus();
    if (kind === "search") document.getElementById("q-input").addEventListener("input", function (e) { runSearch(e.target.value); });
  }
  function closeOverlay() {
    if (overlay.hidden) return;
    overlay.hidden = true; overlay.innerHTML = "";
    document.body.style.overflow = "";
    var burger = document.querySelector(".burger"); if (burger) burger.setAttribute("aria-expanded", "false");
    if (lastFocus && document.contains(lastFocus)) lastFocus.focus();
  }

  /* ------------------------------------------------------------ slider */
  function showSlide(i) {
    slideIdx = (i + Q.SLIDES.length) % Q.SLIDES.length;
    var box = document.getElementById("slide-h"); if (!box) return;
    box.querySelectorAll(".slide").forEach(function (s, k) { s.hidden = k !== slideIdx; });
    box.querySelectorAll("[data-slide]").forEach(function (d) { d.setAttribute("aria-current", String(+d.getAttribute("data-slide") === slideIdx)); });
  }
  function startSlider() {
    stopSlider();
    if (reduceMotion || route !== "home") return;
    slideTimer = setInterval(function () { showSlide(slideIdx + 1); }, 8000);
  }
  function stopSlider() { if (slideTimer) { clearInterval(slideTimer); slideTimer = null; } }

  /* ------------------------------------------------------------ render */
  function render(focus) {
    root.lang = lang;
    root.dir = lang === "ar" ? "rtl" : "ltr";
    app.className = "qs" + (lang === "ar" ? " qs-ar" : "") + (route === "home" ? " route-home" : "");
    var skip = app.querySelector(".skip"); if (skip) skip.textContent = u("skip");
    document.getElementById("hdr").innerHTML = headerHTML();
    var main = document.getElementById("main");
    if (route === "home") main.innerHTML = homeHTML();
    else if (Q.PAGES[route]) main.innerHTML = pageHTML(route);
    else main.innerHTML = notFoundHTML();
    document.getElementById("ftr").innerHTML = footerHTML();
    bindNewsletter();
    document.title = (route === "home" ? u("brand") : (Q.PAGES[route] ? t(Q.PAGES[route].t) + " | " + u("brand") : u("notFound")));
    bindMaps(main);
    bindPage(main);
    if (focus) { var h = main.querySelector("h1"); if (h) h.focus({ preventScroll: true }); }
    firstRender = false;
    startSlider();
    requestAnimationFrame(setupReveal);
  }

  /* Simple movement: blocks that start below the first screen ease up when they
     scroll into view; anything already visible is left alone. */
  var revealObs = null;
  var REVEAL_SEL = ".band .shead, .band .grid > *, .band .stats > *, .stats-banner, .about-teaser > *, .partner-strip, " +
    ".blk, .related, .cta-band__row, .ftr__strip, .ftr__cols > *";
  var STAGGER_PARENTS = { grid: 1, stats: 1, ftr__cols: 1 };
  function setupReveal() {
    if (revealObs) revealObs.disconnect();
    if (reduceMotion || !("IntersectionObserver" in window)) return;
    revealObs = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add("is-in"); revealObs.unobserve(en.target); } });
    }, { rootMargin: "0px 0px -8% 0px" });
    var vh = window.innerHeight;
    document.querySelectorAll(REVEAL_SEL).forEach(function (el) {
      if (el.getBoundingClientRect().top < vh) return;
      var par = el.parentElement, i = Array.prototype.indexOf.call(par.children, el);
      if ([].some.call(par.classList, function (c) { return STAGGER_PARENTS[c]; })) el.style.setProperty("--rv-delay", Math.min(i, 4) * 80 + "ms");
      el.classList.add("rv");
      revealObs.observe(el);
    });
  }

  function bindNewsletter() {
    var f = document.querySelector("form[data-news]"); if (!f) return;
    f.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!f.checkValidity()) { f.reportValidity(); return; }
      var done = f.parentNode.querySelector(".ftr__done");
      f.hidden = true; done.hidden = false; done.focus();
    });
  }

  function bindPage(main) {
    var ps = main.querySelector("#p-search");
    if (ps) ps.addEventListener("input", function () { productQuery = ps.value; document.getElementById("p-table").innerHTML = productTable(); });
    var hero = main.querySelector(".hero");
    if (hero) {
      hero.addEventListener("mouseenter", stopSlider);
      hero.addEventListener("mouseleave", startSlider);
      hero.addEventListener("focusin", stopSlider);
    }
    main.querySelectorAll("form[data-form]").forEach(function (f) {
      f.addEventListener("submit", function (e) {
        e.preventDefault();
        if (!f.checkValidity()) { f.reportValidity(); return; }
        var done = f.parentNode.querySelector(".form-done");
        f.hidden = true; done.hidden = false;
        done.setAttribute("tabindex", "-1"); done.focus();
      });
    });
  }

  /* ------------------------------------------------------------ events */
  document.addEventListener("click", function (e) {
    var a;
    if (e.target.closest(".skip")) { e.preventDefault(); var m = document.getElementById("main"); m.tabIndex = -1; m.focus(); return; }
    if ((a = e.target.closest("[data-lang]"))) {
      var nl = a.getAttribute("data-lang");
      if (nl !== lang) { lang = nl; store("qs-lang", lang); render(false); var b = document.querySelector('[data-lang="' + lang + '"]'); if (b) b.focus(); }
      return;
    }
    if ((a = e.target.closest("[data-act]"))) {
      e.preventDefault();
      var act = a.getAttribute("data-act");
      if (act === "drawer" || act === "search") openOverlay(act);
      else if (act === "close") closeOverlay();
      else if (act === "top") { window.scrollTo({ top: 0, behavior: reduceMotion ? "auto" : "smooth" }); var br = document.querySelector(".brand"); if (br) br.focus({ preventScroll: true }); }
      return;
    }
    if ((a = e.target.closest("[data-slide]"))) { showSlide(+a.getAttribute("data-slide")); stopSlider(); return; }
    if ((a = e.target.closest("[data-slide-step]"))) { showSlide(slideIdx + +a.getAttribute("data-slide-step")); stopSlider(); return; }
    if ((a = e.target.closest("[data-strip]"))) {
      var strip = document.getElementById("pstrip");
      if (strip) { var dir = +a.getAttribute("data-strip") * (lang === "ar" ? -1 : 1); strip.scrollBy({ left: dir * 220, behavior: reduceMotion ? "auto" : "smooth" }); }
      return;
    }
    if ((a = e.target.closest("[data-area]"))) { productArea = a.getAttribute("data-area"); productQuery = ""; location.hash = "products-list"; return; }
    if ((a = e.target.closest("[data-parea]"))) {
      productArea = a.getAttribute("data-parea");
      document.querySelectorAll("[data-parea]").forEach(function (c) { c.setAttribute("aria-pressed", String(c === a)); });
      document.getElementById("p-table").innerHTML = productTable();
      return;
    }
    if ((a = e.target.closest("[data-form-again]"))) {
      var wrap = a.closest("[data-formwrap]"), form = wrap.querySelector("form");
      form.reset(); form.hidden = false; a.closest(".form-done").hidden = true; form.querySelector(".qs-input").focus();
      return;
    }
    if ((a = e.target.closest("[data-copy]"))) {
      var v = a.getAttribute("data-copy"), lbl = a.querySelector("span");
      var ok = function () { lbl.textContent = u("copied"); setTimeout(function () { lbl.textContent = u("copy"); }, 1600); };
      try { navigator.clipboard.writeText(v).then(ok, function () { selectText(a.previousElementSibling); }); } catch (err) { selectText(a.previousElementSibling); }
      return;
    }
    if (e.target.closest("#overlay a[href^='#']")) closeOverlay();
  });
  function selectText(el) { if (!el) return; var r = document.createRange(); r.selectNodeContents(el); var s = getSelection(); s.removeAllRanges(); s.addRange(r); }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") { closeOverlay(); if (document.activeElement && document.activeElement.closest(".mega")) document.activeElement.blur(); }
    if (e.key === "/" && !/INPUT|TEXTAREA|SELECT/.test((document.activeElement || {}).tagName || "") && overlay.hidden) { e.preventDefault(); openOverlay("search"); }
  });


  function fromHash() {
    var h = (location.hash || "").replace(/^#/, "");
    return h || "home";
  }
  window.addEventListener("hashchange", function () {
    var next = fromHash();
    if (next === "main") return;
    route = next;
    if (route !== "products-list") productArea = "all";
    closeOverlay();
    render(true);
    window.scrollTo(0, 0);
  });

  route = fromHash();
  render(false);
})();
