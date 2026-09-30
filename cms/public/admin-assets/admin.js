/* Al-Qawsan website dashboard. Vanilla JS, no build step.
   - Page section editor (blocks) and repeatable lists, stored as JSON in hidden inputs
   - Photo fields: upload, choose from the library, preview
   - Page pickers, icon previews, the mobile menu, unsaved-changes warning */

(function () {
  "use strict";

  var A = window.ADMIN || {};
  var ICONS = A.icons || {};

  /* ------------------------------------------------------------ helpers */
  function el(tag, attrs, kids) {
    var n = document.createElement(tag);
    if (attrs) Object.keys(attrs).forEach(function (k) {
      var v = attrs[k];
      if (v == null || v === false) return;
      if (k === "text") n.textContent = v;
      else if (k === "html") n.innerHTML = v;
      else if (k.slice(0, 2) === "on") n.addEventListener(k.slice(2), v);
      else n.setAttribute(k, v === true ? "" : v);
    });
    (kids || []).forEach(function (c) { if (c != null) n.appendChild(typeof c === "string" ? document.createTextNode(c) : c); });
    return n;
  }
  function svg(name) {
    return '<svg class="ai" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' + (ICONS[name] || ICONS.info || "") + "</svg>";
  }
  function mini(icon, label, onclick, extra) {
    return el("button", { type: "button", "class": "adm-mini" + (extra ? " " + extra : ""), "aria-label": label, title: label, html: svg(icon), onclick: onclick });
  }
  function btn(icon, label, onclick, variant) {
    return el("button", { type: "button", "class": "qs-btn qs-btn--" + (variant || "secondary") + " qs-btn--sm adm-add", html: svg(icon) + "<span>" + label + "</span>", onclick: onclick });
  }
  function assetUrl(p) { return p ? (/^(https?:)?\//.test(p) ? p : "/" + p) : ""; }
  var uid = 0;
  function nextId(prefix) { return (prefix || "x") + "-" + (++uid); }
  function markDirty(node) { var f = node && node.closest && node.closest("form[data-dirty]"); if (f) f._dirty = true; }

  /* ------------------------------------------------------------ uploads and the media library */
  function upload(file, done, fail) {
    var fd = new FormData();
    fd.append("files[]", file);
    fetch(A.mediaUpload, { method: "POST", body: fd, credentials: "same-origin", headers: { "X-CSRF-TOKEN": A.csrf, "Accept": "application/json" } })
      .then(function (r) { return r.json().then(function (j) { if (!r.ok) throw j; return j; }); })
      .then(function (list) { done(list[0]); })
      .catch(function (e) {
        var msg = (e && e.errors && Object.values(e.errors)[0] && Object.values(e.errors)[0][0]) || (e && e.message) || "Upload failed.";
        alert(msg); if (fail) fail();
      });
  }

  function openLibrary(kind, onPick) {
    var box = el("div", { "class": "adm-modal__grid", html: '<p class="adm-muted">Loading…</p>' });
    var close = function () { modal.remove(); document.removeEventListener("keydown", esc); if (opener) opener.focus(); };
    var esc = function (e) { if (e.key === "Escape") close(); };
    var opener = document.activeElement;
    var modal = el("div", { "class": "adm-modal", role: "dialog", "aria-modal": "true", "aria-label": "Photo library", onclick: function (e) { if (e.target === modal) close(); } }, [
      el("div", { "class": "adm-modal__box" }, [
        el("div", { "class": "adm-modal__head" }, [el("h2", { text: kind === "image" ? "Choose a photo" : "Choose a file" }), el("button", { type: "button", "class": "adm-iconbtn", "aria-label": "Close", html: svg("x"), onclick: close })]),
        box
      ])
    ]);
    document.body.appendChild(modal);
    document.addEventListener("keydown", esc);
    modal.querySelector(".adm-iconbtn").focus();
    fetch(A.mediaJson + "?type=" + (kind === "image" ? "image" : "all"), { credentials: "same-origin", headers: { "Accept": "application/json" } })
      .then(function (r) { return r.json(); })
      .then(function (list) {
        box.innerHTML = "";
        if (!list.length) { box.appendChild(el("p", { "class": "adm-muted", text: "The library is empty. Use Upload to add the first file." })); return; }
        list.forEach(function (m) {
          box.appendChild(el("button", { type: "button", title: m.name, onclick: function () { close(); onPick(m.path); } }, [
            m.image ? el("img", { src: assetUrl(m.path), alt: "", loading: "lazy" }) : el("div", { "class": "adm-lib__img", html: svg("file") + "<span>PDF</span>" }),
            el("span", { text: m.name })
          ]));
        });
      });
  }

  /* A photo or file field. input: the text input holding the path. */
  function mediaControl(kind, value, set) {
    var prev = el("div", { "class": "adm-media__prev" });
    var input = el("input", { "class": "qs-input", dir: "ltr", value: value || "", placeholder: kind === "image" ? "No photo yet" : "No file yet", "aria-label": "Path" });
    var wrap = el("div", { "class": "adm-media" });
    function show(p) {
      prev.innerHTML = "";
      if (!p) { prev.textContent = kind === "image" ? "No photo" : "No file"; return; }
      if (kind === "image" || /\.(jpe?g|png|webp|gif)$/i.test(p)) prev.appendChild(el("img", { src: assetUrl(p), alt: "" }));
      else prev.innerHTML = svg("file") + "<span>" + p.split("/").pop() + "</span>";
    }
    function setVal(p) { input.value = p || ""; show(p); set(p || ""); markDirty(wrap); }
    input.addEventListener("input", function () { show(input.value.trim()); set(input.value.trim()); });
    var file = el("input", { type: "file", hidden: true, accept: kind === "image" ? "image/jpeg,image/png,image/webp,image/gif" : ".pdf,image/*" });
    file.addEventListener("change", function () {
      if (!file.files[0]) return;
      wrap.classList.add("is-busy");
      upload(file.files[0], function (m) { wrap.classList.remove("is-busy"); setVal(m.path); }, function () { wrap.classList.remove("is-busy"); });
      file.value = "";
    });
    var up = el("label", { "class": "qs-btn qs-btn--secondary qs-btn--sm adm-upload", html: svg("upload") + "<span>Upload</span>" }); up.appendChild(file);
    wrap.appendChild(prev);
    wrap.appendChild(el("div", { "class": "adm-media__ctl" }, [input, el("div", { "class": "adm-row" }, [
      up,
      btn("image", "Choose from library", function () { openLibrary(kind, setVal); }, "ghost"),
      btn("x", "Remove", function () { setVal(""); }, "ghost")
    ])]));
    show(value);
    return wrap;
  }

  /* Server-rendered photo fields (resource forms, settings, page banner). */
  function bindMediaField(root) {
    var input = root.querySelector("[data-path]"), prev = root.querySelector("[data-prev]"), kind = root.getAttribute("data-kind");
    var fresh = mediaControl(kind, input.value, function () {});
    var newInput = fresh.querySelector("input.qs-input");
    newInput.name = input.name; newInput.id = input.id;
    if (input.getAttribute("aria-labelledby")) newInput.setAttribute("aria-labelledby", input.getAttribute("aria-labelledby"));
    root.replaceWith(fresh);
    void prev;
  }

  /* ------------------------------------------------------------ field editors (JSON) */
  function l10nEditor(f, v, set, area) {
    v = v && typeof v === "object" ? { en: v.en || "", ar: v.ar || "" } : { en: "", ar: "" };
    var box = el("div", { "class": "adm-l10n" });
    [["en", "English"], ["ar", "العربية"]].forEach(function (p) {
      var ctl = el(area ? "textarea" : "input", { "class": "qs-input", dir: p[0] === "ar" ? "rtl" : "ltr", rows: area ? 3 : null });
      ctl.value = v[p[0]];
      ctl.addEventListener("input", function () { v[p[0]] = ctl.value; set(v.en === "" && v.ar === "" ? null : { en: v.en, ar: v.ar }); markDirty(ctl); });
      box.appendChild(el("label", { "class": "adm-l10n__side", lang: p[0] === "ar" ? "ar" : null, dir: p[0] === "ar" ? "rtl" : null }, [el("span", { "class": "adm-l10n__tag", text: p[1] }), ctl]));
    });
    return box;
  }
  function textEditor(f, v, set, type) {
    var i = el("input", { "class": "qs-input", dir: type === "url" ? "ltr" : null, placeholder: type === "url" ? "https://…" : null });
    i.value = v || "";
    i.addEventListener("input", function () { set(i.value.trim() || null); markDirty(i); });
    return i;
  }
  function selectEditor(choices, v, set, none) {
    var s = el("select", { "class": "qs-input" });
    s.appendChild(el("option", { value: "", text: none || "Choose" }));
    Object.keys(choices).forEach(function (k) { var o = el("option", { value: k, text: choices[k] }); if (String(v) === k) o.selected = true; s.appendChild(o); });
    s.addEventListener("change", function () { set(s.value || null); markDirty(s); });
    return s;
  }
  function iconEditor(f, v, set) {
    var choices = {}; (A.pickable || Object.keys(ICONS)).forEach(function (k) { choices[k] = k; });
    var prev = el("span", { "class": "adm-iconprev", "aria-hidden": "true", html: v ? svg(v) : "" });
    var s = selectEditor(choices, v, function (x) { prev.innerHTML = x ? svg(x) : ""; set(x); }, "No icon");
    return el("div", { "class": "adm-selectwrap" }, [prev, s]);
  }

  /* A repeatable list. make(item, setItem) builds one row's editor. */
  function listEditor(arr, set, make, blank, addLabel, max) {
    arr = Array.isArray(arr) ? arr.slice() : [];
    var box = el("div", { "class": "adm-list" });
    function commit() { set(arr); markDirty(box); }
    function render() {
      box.innerHTML = "";
      arr.forEach(function (item, i) {
        var body = el("div", { "class": "adm-list__body" }, [make(item, function (nv) { arr[i] = nv; commit(); })]);
        var tools = el("div", { "class": "adm-list__tools" }, [
          mini("chevUp", "Move up", function () { if (i > 0) { arr.splice(i - 1, 0, arr.splice(i, 1)[0]); commit(); render(); } }),
          mini("chevDown", "Move down", function () { if (i < arr.length - 1) { arr.splice(i + 1, 0, arr.splice(i, 1)[0]); commit(); render(); } }),
          mini("trash", "Remove", function () { arr.splice(i, 1); commit(); render(); }, "adm-mini--del")
        ]);
        tools.children[0].disabled = i === 0; tools.children[1].disabled = i === arr.length - 1;
        box.appendChild(el("div", { "class": "adm-list__item" }, [body, tools]));
      });
      if (!max || arr.length < max) box.appendChild(btn("plus", addLabel, function () { arr.push(blank()); commit(); render(); var last = box.querySelectorAll(".adm-list__item"); last = last[last.length - 1]; if (last) { var f = last.querySelector("input, textarea, select"); if (f) f.focus(); } }));
    }
    render();
    return box;
  }

  function labelled(label, ctl) {
    var id = nextId("lbl");
    return el("div", { "class": "adm-field" }, [el("p", { "class": "qs-field__label", id: id, text: label }), (ctl.setAttribute && ctl.setAttribute("aria-labelledby", id), ctl)]);
  }

  function editor(f, v, set) {
    switch (f.t) {
      case "l10n": return l10nEditor(f, v, set, false);
      case "l10n_area": return l10nEditor(f, v, set, true);
      case "text": return textEditor(f, v, set);
      case "url": return textEditor(f, v, set, "url");
      case "icon": return iconEditor(f, v, set);
      case "select": return selectEditor(f.choices || {}, v, set);
      case "image": return mediaControl("image", v, function (p) { set(p || null); });
      case "paragraphs":
        return listEditor(v, set, function (item, s) { return l10nEditor(f, item, s, true); }, function () { return { en: "", ar: "" }; }, "Add paragraph");
      case "lines":
        return listEditor(v, set, function (item, s) { return l10nEditor(f, item, s, false); }, function () { return { en: "", ar: "" }; }, "Add point");
      case "items":
        return listEditor(v, set, function (item, s) {
          item = item && typeof item === "object" ? item : {};
          var wrap = el("div", { "class": "adm-list__body" });
          (f.sub || []).forEach(function (sf) {
            wrap.appendChild(labelled(sf.l, editor(sf, item[sf.n], function (x) { if (x == null) delete item[sf.n]; else item[sf.n] = x; s(item); })));
          });
          return wrap;
        }, function () { return {}; }, "Add " + (f.l || "item").toLowerCase().replace(/s$/, ""), f.max);
    }
    return el("span", { text: "Unsupported field" });
  }

  /* Hidden inputs holding JSON (news article paragraphs). */
  function bindJsonField(input) {
    var f = JSON.parse(input.getAttribute("data-json-field"));
    var v; try { v = JSON.parse(input.value || "[]"); } catch (e) { v = []; }
    input.parentNode.insertBefore(editor(f, v, function (nv) { input.value = JSON.stringify(nv || []); }), input.nextSibling);
  }

  /* ------------------------------------------------------------ page sections */
  function bindBlocks(input) {
    var S = A.schema || {};
    var blocks; try { blocks = JSON.parse(input.value || "[]"); } catch (e) { blocks = []; }
    var open = {};
    var box = el("div", { "class": "adm-blocks" });
    input.parentNode.insertBefore(box, input.nextSibling);
    function save() { input.value = JSON.stringify(blocks); markDirty(box); }
    function summary(b) {
      var h = b.h && (b.h.en || b.h.ar);
      if (h) return h;
      if (b.p && b.p[0]) return (b.p[0].en || b.p[0].ar || "").replace(/<[^>]*>/g, "").slice(0, 60) + "…";
      if (b.text) return (b.text.en || "").replace(/<[^>]*>/g, "").slice(0, 60);
      if (b.kind && S.form) return (S.form.fields[0].choices || {})[b.kind] || "";
      return "";
    }
    function render() {
      box.innerHTML = "";
      blocks.forEach(function (b, i) {
        var def = S[b.type] || { label: b.type, widget: "Unknown section type." };
        var key = i + ":" + b.type;
        var isOpen = open[key] !== false;
        var head = el("div", { "class": "adm-block__head" }, [
          el("span", { "class": "adm-block__n", text: String(i + 1) }),
          def.widget ? el("span", { "class": "adm-block__t" }, [def.label, el("small", { text: summary(b) })]) :
            el("button", { type: "button", "class": "adm-block__t adm-block__fold", "aria-expanded": String(isOpen), title: isOpen ? "Collapse" : "Expand", onclick: function () { open[key] = !isOpen; render(); } }, [def.label, el("small", { text: summary(b) })]),
          mini("chevUp", "Move up", function () { if (i > 0) { blocks.splice(i - 1, 0, blocks.splice(i, 1)[0]); save(); render(); } }),
          mini("chevDown", "Move down", function () { if (i < blocks.length - 1) { blocks.splice(i + 1, 0, blocks.splice(i, 1)[0]); save(); render(); } }),
          mini("trash", "Remove section", function () { if (confirm("Remove this " + def.label.toLowerCase() + " section?")) { blocks.splice(i, 1); save(); render(); } }, "adm-mini--del")
        ]);
        head.querySelectorAll(".adm-mini")[0].disabled = i === 0;
        head.querySelectorAll(".adm-mini")[1].disabled = i === blocks.length - 1;
        var body = el("div", { "class": "adm-block__body" });
        if (def.widget) body.appendChild(el("p", { "class": "adm-block__widget", html: svg("info") + "<span></span>" })).querySelector("span").textContent = def.widget;
        else (def.fields || []).forEach(function (f) {
          body.appendChild(labelled(f.l, editor(f, b[f.n], function (x) { if (x == null || (Array.isArray(x) && !x.length)) delete b[f.n]; else b[f.n] = x; save(); head.querySelector("small").textContent = summary(b); })));
        });
        box.appendChild(el("div", { "class": "adm-block" + (def.widget ? " is-widget" : "") + (isOpen || def.widget ? "" : " is-closed") }, [head, body]));
      });
      var choices = {}; Object.keys(S).forEach(function (k) { choices[k] = S[k].label + (S[k].widget ? " (automatic)" : ""); });
      var pick = selectEditor(choices, "", function () {}, "Choose a section type");
      pick.setAttribute("aria-label", "Section type");
      box.appendChild(el("div", { "class": "adm-addblock" }, [pick, btn("plus", "Add section", function () {
        if (!pick.value) { pick.focus(); return; }
        var nb = { type: pick.value };
        blocks.push(nb); save(); render();
        var all = box.querySelectorAll(".adm-block"); all[all.length - 1].scrollIntoView({ behavior: "smooth", block: "center" });
      }, "primary")]));
    }
    render();
  }

  /* ------------------------------------------------------------ ordered page picker */
  function bindPages(root) {
    var name = root.getAttribute("data-name");
    var list; try { list = JSON.parse(root.getAttribute("data-value") || "[]"); } catch (e) { list = []; }
    var choices = A.pages || {};
    function render() {
      root.innerHTML = "";
      var ol = el("ol");
      list.forEach(function (slug, i) {
        var li = el("li", {}, [el("span", { text: choices[slug] || slug }), el("input", { type: "hidden", name: name + "[]", value: slug }),
          mini("chevUp", "Move up", function () { if (i > 0) { list.splice(i - 1, 0, list.splice(i, 1)[0]); markDirty(root); render(); } }),
          mini("chevDown", "Move down", function () { if (i < list.length - 1) { list.splice(i + 1, 0, list.splice(i, 1)[0]); markDirty(root); render(); } }),
          mini("x", "Remove", function () { list.splice(i, 1); markDirty(root); render(); }, "adm-mini--del")]);
        ol.appendChild(li);
      });
      if (!list.length) root.appendChild(el("input", { type: "hidden", name: name, value: "" }));
      root.appendChild(ol);
      var avail = {}; Object.keys(choices).forEach(function (k) { if (list.indexOf(k) < 0) avail[k] = choices[k]; });
      var s = selectEditor(avail, "", function () {}, "Add a page…");
      s.setAttribute("aria-label", "Add a page");
      s.addEventListener("change", function () { if (s.value) { list.push(s.value); markDirty(root); render(); root.querySelector("select").focus(); } });
      root.appendChild(el("div", { "class": "adm-row" }, [s]));
    }
    render();
  }

  /* Textareas grow with their text, so long paragraphs are readable while editing. */
  function grow(t) { t.style.height = "auto"; t.style.height = Math.min(t.scrollHeight + 2, 640) + "px"; }
  document.addEventListener("input", function (e) { if (e.target.tagName === "TEXTAREA") grow(e.target); });
  function growAll() { document.querySelectorAll("textarea.qs-input").forEach(grow); }
  new MutationObserver(function () { requestAnimationFrame(growAll); }).observe(document.body, { childList: true, subtree: true });

  /* ------------------------------------------------------------ wiring */
  document.querySelectorAll("[data-media-field]").forEach(bindMediaField);
  document.querySelectorAll("[data-json-field]").forEach(bindJsonField);
  document.querySelectorAll("[data-blocks-field]").forEach(bindBlocks);
  document.querySelectorAll("[data-pages-field]").forEach(bindPages);
  document.querySelectorAll("[data-icon-select]").forEach(function (w) {
    var s = w.querySelector("select"), p = w.querySelector(".adm-iconprev");
    var show = function () { p.innerHTML = s.value ? svg(s.value) : ""; };
    s.addEventListener("change", show); show();
  });

  // Confirmations for deletes
  document.querySelectorAll("form[data-confirm]").forEach(function (f) {
    f.addEventListener("submit", function (e) { if (!confirm(f.getAttribute("data-confirm"))) e.preventDefault(); });
  });

  // Unsaved changes
  var forms = document.querySelectorAll("form[data-dirty]");
  forms.forEach(function (f) {
    f.addEventListener("input", function () { f._dirty = true; });
    f.addEventListener("change", function () { f._dirty = true; });
    f.addEventListener("submit", function () { f._saving = true; });
  });
  window.addEventListener("beforeunload", function (e) {
    for (var i = 0; i < forms.length; i++) if (forms[i]._dirty && !forms[i]._saving) { e.preventDefault(); e.returnValue = ""; return ""; }
  });

  // Library page: upload on choose, drag and drop, copy path
  document.querySelectorAll("[data-autosubmit]").forEach(function (i) { i.addEventListener("change", function () { if (i.files.length) i.form.submit(); }); });
  document.querySelectorAll("[data-dropzone]").forEach(function (z) {
    var input = z.querySelector("input[type=file]");
    ["dragenter", "dragover"].forEach(function (ev) { z.addEventListener(ev, function (e) { e.preventDefault(); z.classList.add("is-over"); }); });
    ["dragleave", "drop"].forEach(function (ev) { z.addEventListener(ev, function (e) { e.preventDefault(); z.classList.remove("is-over"); }); });
    z.addEventListener("drop", function (e) { if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; z.submit(); } });
  });
  document.addEventListener("click", function (e) {
    var c = e.target.closest("[data-copy]");
    if (c) {
      var v = c.getAttribute("data-copy"), s = c.querySelector("span"), old = s.textContent;
      (navigator.clipboard ? navigator.clipboard.writeText(v) : Promise.reject()).then(function () { s.textContent = "Copied"; setTimeout(function () { s.textContent = old; }, 1400); }, function () { prompt("Copy this path:", v); });
    }
    var side = e.target.closest("[data-side]");
    if (side) {
      var on = side.getAttribute("data-side") === "open";
      document.getElementById("adm-side").classList.toggle("is-open", on);
      document.querySelector(".adm-scrim").classList.toggle("is-open", on);
      if (on) document.querySelector(".adm-side__close").focus();
      else { var b = document.querySelector(".adm-burger"); if (b) b.focus(); }
    }
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && document.getElementById("adm-side") && document.getElementById("adm-side").classList.contains("is-open")) {
      document.getElementById("adm-side").classList.remove("is-open");
      document.querySelector(".adm-scrim").classList.remove("is-open");
    }
  });
  growAll();
})();
