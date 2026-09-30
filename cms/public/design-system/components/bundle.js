/* @ds-bundle: {"format":4,"namespace":"AlQawsan","components":[{"name":"Button"},{"name":"SectionHeading"},{"name":"Card"},{"name":"StatTile"},{"name":"Badge"},{"name":"Timeline"},{"name":"Field"},{"name":"DataTable"},{"name":"SiteHeader"},{"name":"SiteFooter"},{"name":"HexPattern"}]} */
/* Al-Qawsan Scientific Bureau — component bundle.
   One classic script. Reads window.React / window.ReactDOM, assigns window.AlQawsan.
   Every component takes lang: "en" | "ar". "ar" sets dir="rtl" and the Arabic type
   styles; layout uses logical properties so one stylesheet serves both directions. */
(function () {
  "use strict";

  var React = window.React;
  var h = React.createElement;

  function cx() {
    var out = [];
    for (var i = 0; i < arguments.length; i++) {
      if (arguments[i]) out.push(arguments[i]);
    }
    return out.join(" ");
  }

  function isAr(lang) { return lang === "ar"; }

  /* Root attributes every component shares. */
  function rootProps(lang, className, rest) {
    var props = { className: cx("qs", isAr(lang) ? "qs-ar" : null, className) };
    if (isAr(lang)) { props.dir = "rtl"; props.lang = "ar"; }
    else { props.lang = "en"; }
    if (rest) {
      for (var k in rest) {
        if (Object.prototype.hasOwnProperty.call(rest, k)) props[k] = rest[k];
      }
    }
    return props;
  }

  /* ------------------------------------------------------------- Button */

  function Button(props) {
    props = props || {};
    var variant = props.variant || "primary";
    var size = props.size || "md";
    var lang = props.lang || "en";
    var Tag = props.href ? "a" : "button";
    var attrs = {
      className: cx("qs", isAr(lang) ? "qs-ar" : null, "qs-btn", "qs-btn--" + variant,
        size !== "md" ? "qs-btn--" + size : null, props.className),
      onClick: props.onClick,
      type: props.href ? undefined : (props.type || "button"),
      href: props.href,
      disabled: props.href ? undefined : props.disabled,
      "aria-disabled": props.disabled ? "true" : undefined,
      dir: isAr(lang) ? "rtl" : undefined
    };
    var kids = [];
    if (props.iconStart) kids.push(h("span", { className: "qs-btn__icon", key: "s", "aria-hidden": "true" }, props.iconStart));
    kids.push(h("span", { key: "l" }, props.children));
    if (props.iconEnd) kids.push(h("span", { className: "qs-btn__icon", key: "e", "aria-hidden": "true" }, props.iconEnd));
    return h(Tag, attrs, kids);
  }

  /* ----------------------------------------------------- SectionHeading */

  function SectionHeading(props) {
    props = props || {};
    var lang = props.lang || "en";
    var level = props.level || "h2";
    var kids = [];
    if (props.overline) kids.push(h("p", { className: "qs-overline", key: "o" }, props.overline));
    kids.push(h(level, { className: "qs-heading qs-sectionhead__title", key: "t" }, props.title));
    if (props.lead) kids.push(h("p", { className: "qs-sectionhead__lead", key: "l" }, props.lead));
    return h("div", rootProps(lang, cx("qs-sectionhead", props.onDark ? "qs-on-dark" : null, props.className)), kids);
  }

  /* --------------------------------------------------------------- Card */

  function Card(props) {
    props = props || {};
    var lang = props.lang || "en";
    var variant = props.variant || "content";
    var kids = [];
    if (props.number != null) kids.push(h("span", { className: "qs-card__number", key: "n" }, props.number));
    if (props.overline) kids.push(h("p", { className: "qs-overline", key: "o" }, props.overline));
    if (props.title) kids.push(h("h3", { className: "qs-heading qs-card__title", key: "t" }, props.title));
    if (props.body) kids.push(h("p", { className: "qs-card__body", key: "b" }, props.body));
    if (props.children) kids.push(h("div", { key: "c" }, props.children));
    var onDark = variant === "dark";
    return h("div", rootProps(lang, cx("qs-card", "qs-card--" + variant, onDark ? "qs-on-dark" : null, props.className)), kids);
  }

  /* ----------------------------------------------------------- StatTile */

  function StatTile(props) {
    props = props || {};
    var lang = props.lang || "en";
    var tone = props.tone || "navy";
    var kids = [];
    if (props.pattern !== false) {
      kids.push(h("div", { className: "qs-stat__pattern", key: "p", "aria-hidden": "true" },
        h(HexPattern, {
          variant: "field",
          tone: tone === "navy" ? "navy" : "orange",
          width: 320, height: 160, cell: 34
        })));
    }
    /* Isolate the figure so "1,500+" and "24/7" keep their order inside an RTL block. */
    kids.push(h("p", { className: "qs-stat__value", key: "v" },
      h("bdi", { dir: "ltr" }, props.value)));
    kids.push(h("p", { className: "qs-stat__label", key: "l" }, props.label));
    return h("div", rootProps(lang, cx("qs-stat", "qs-stat--" + tone, props.className)), kids);
  }

  /* -------------------------------------------------------------- Badge */

  function Badge(props) {
    props = props || {};
    var lang = props.lang || "en";
    var tone = props.tone || "brand";
    var kids = [];
    if (props.dot !== false) kids.push(h("span", { className: "qs-badge__dot", key: "d", "aria-hidden": "true" }));
    kids.push(h("span", { key: "t" }, props.children));
    return h("span", rootProps(lang, cx("qs-badge", "qs-badge--" + tone, props.className)), kids);
  }

  /* ----------------------------------------------------------- Timeline */

  function Timeline(props) {
    props = props || {};
    var lang = props.lang || "en";
    var orientation = props.orientation || "horizontal";
    var items = props.items || [];
    var kids = items.map(function (item, i) {
      return h("div", { className: "qs-timeline__item", key: i },
        h("span", { className: "qs-timeline__rule", "aria-hidden": "true" }),
        h("span", { className: "qs-timeline__node", "aria-hidden": "true" }),
        h("p", { className: "qs-timeline__year" }, item.year),
        item.title ? h("p", { className: "qs-heading qs-timeline__title" }, item.title) : null,
        item.description ? h("p", { className: "qs-timeline__text" }, item.description) : null
      );
    });
    return h("div", rootProps(lang, cx("qs-timeline", "qs-timeline--" + orientation, props.className)), kids);
  }

  /* -------------------------------------------------------------- Field */

  /* Phone, email and URL stay LTR inside an Arabic form. */
  var LTR_TYPES = { tel: 1, email: 1, url: 1, number: 1 };

  function Field(props) {
    props = props || {};
    var lang = props.lang || "en";
    var type = props.type || "text";
    var id = props.id || ("qs-field-" + Math.random().toString(36).slice(2, 8));
    var describedBy = [];
    if (props.hint) describedBy.push(id + "-hint");
    if (props.error) describedBy.push(id + "-err");
    var inputDir = props.dir || (LTR_TYPES[type] ? "ltr" : undefined);
    var kids = [
      h("label", { className: "qs-field__label", htmlFor: id, key: "l" },
        props.label,
        props.required ? h("span", { className: "qs-field__req", "aria-hidden": "true" }, "*") : null),
      h("input", {
        key: "i",
        id: id,
        type: type,
        dir: inputDir,
        className: cx("qs-input", props.error ? "qs-input--error" : null),
        placeholder: props.placeholder,
        defaultValue: props.defaultValue,
        value: props.value,
        onChange: props.onChange,
        disabled: props.disabled,
        required: props.required,
        "aria-invalid": props.error ? "true" : undefined,
        "aria-describedby": describedBy.length ? describedBy.join(" ") : undefined
      })
    ];
    if (props.hint) kids.push(h("span", { className: "qs-field__hint", id: id + "-hint", key: "h" }, props.hint));
    if (props.error) kids.push(h("span", { className: "qs-field__error", id: id + "-err", key: "e" }, props.error));
    return h("div", rootProps(lang, cx("qs-field", props.className)), kids);
  }

  /* ---------------------------------------------------------- DataTable */

  function DataTable(props) {
    props = props || {};
    var lang = props.lang || "en";
    var columns = props.columns || [];
    var rows = props.rows || [];
    var head = h("thead", { key: "h" }, h("tr", null, columns.map(function (c, i) {
      return h("th", { key: i, scope: "col", className: c.align === "end" ? "qs-num" : null }, c.label);
    })));
    var body = h("tbody", { key: "b" }, rows.map(function (row, ri) {
      return h("tr", { key: ri }, columns.map(function (c, ci) {
        /* Numeric cells are isolated: "1,500+" and "2026-11" must not be reordered by RTL. */
        var cell = c.align === "end" ? h("bdi", { dir: "ltr" }, row[c.key]) : row[c.key];
        return h("td", { key: ci, className: c.align === "end" ? "qs-num" : null }, cell);
      }));
    }));
    return h("div", rootProps(lang, props.className),
      props.caption ? h("p", { className: "qs-field__hint", key: "c" }, props.caption) : null,
      h("table", { className: cx("qs-table", props.dense ? "qs-table--dense" : null), key: "t" }, head, body));
  }

  /* --------------------------------------------------------- SiteHeader */

  function SiteHeader(props) {
    props = props || {};
    var lang = props.lang || "en";
    var items = props.items || [];
    return h("header", rootProps(lang, cx("qs-header", props.className)),
      h("span", { className: "qs-header__brand", key: "b" }, props.brand),
      h("nav", { className: "qs-header__nav", key: "n", "aria-label": isAr(lang) ? "التنقل الرئيسي" : "Main" },
        items.map(function (item, i) {
          return h("a", {
            key: i,
            href: item.href || "#",
            "aria-current": item.active ? "page" : undefined,
            className: cx("qs-navlink", item.active ? "qs-navlink--active" : null)
          }, item.label);
        })),
      h("span", { className: "qs-langswitch", key: "l" },
        h("button", { type: "button", "aria-current": !isAr(lang) ? "true" : "false" }, "EN"),
        "|",
        h("button", { type: "button", "aria-current": isAr(lang) ? "true" : "false", lang: "ar" }, "عربي")),
      props.cta ? h(Button, { variant: "accent", lang: lang, key: "c" }, props.cta) : null);
  }

  /* --------------------------------------------------------- SiteFooter */

  function SiteFooter(props) {
    props = props || {};
    var lang = props.lang || "en";
    var cols = props.columns || [];
    return h("footer", rootProps(lang, cx("qs-footer", "qs-on-dark", props.className)),
      h("div", { className: "qs-footer__pattern", key: "p", "aria-hidden": "true" },
        h(HexPattern, { variant: "field", tone: "navy", width: 960, height: 360, cell: 56, opacity: "var(--opacity-pattern)" })),
      h("div", { className: "qs-footer__inner", key: "i" },
        h("div", { className: "qs-footer__cols" }, cols.map(function (col, i) {
          return h("div", { key: i },
            h("h3", { className: "qs-footer__title" }, col.title),
            (col.lines || []).map(function (line, j) {
              return line.href
                ? h("p", { key: j, style: { margin: "0 0 4px" } }, h("a", { href: line.href, dir: line.ltr ? "ltr" : undefined }, line.text))
                : h("p", { key: j, className: "qs-footer__text", dir: line.ltr ? "ltr" : undefined }, line.text);
            }));
        })),
        h("p", { className: "qs-footer__legal" }, props.legal)));
  }

  /* --------------------------------------------------------- HexPattern */

  /* Flat-top hexagon. r = circumradius; width 2r, height 2r*sin(60). */
  function hexPoints(cx0, cy0, r) {
    var pts = [];
    for (var i = 0; i < 6; i++) {
      var a = Math.PI / 180 * (60 * i);
      pts.push((cx0 + r * Math.cos(a)).toFixed(2) + "," + (cy0 + r * Math.sin(a)).toFixed(2));
    }
    return pts.join(" ");
  }

  var TONES = {
    navy: { frame: "var(--indigo-400)", node: "var(--indigo-400)", line: "var(--indigo-400)", opacity: "var(--opacity-pattern-strong)" },
    orange: { frame: "var(--orange-200)", node: "var(--orange-200)", line: "var(--orange-200)", opacity: "0.35" },
    light: { frame: "var(--navy-800)", node: "var(--orange-500)", line: "var(--orange-500)", opacity: "var(--opacity-pattern)" },
    watermark: { frame: "var(--navy-800)", node: "var(--navy-800)", line: "var(--navy-800)", opacity: "var(--opacity-watermark)" }
  };

  function HexPattern(props) {
    props = props || {};
    var tone = TONES[props.tone || "light"] || TONES.light;
    var width = props.width || 480;
    var height = props.height || 240;
    var variant = props.variant || "field";
    var opacity = props.opacity != null ? String(props.opacity) : tone.opacity;

    var shapes = [];
    var key = 0;

    if (variant === "cluster") {
      /* One focal cluster: the large hexagon is the Bureau, the two satellites the
         group entities, joined by the connector network. Max one per layout. */
      var r = props.cell || 64;
      var stroke = Math.max(2, r * 0.07);
      var cxA = width * 0.32, cyA = height * 0.56;
      var cxB = width * 0.62, cyB = height * 0.28;
      var cxC = width * 0.72, cyC = height * 0.74;
      shapes.push(h("line", { key: key++, className: "qs-hex__line", x1: cxA, y1: cyA, x2: cxB, y2: cyB, stroke: tone.line, strokeWidth: stroke * 0.6 }));
      shapes.push(h("line", { key: key++, className: "qs-hex__line", x1: cxA, y1: cyA, x2: cxC, y2: cyC, stroke: tone.line, strokeWidth: stroke * 0.6 }));
      shapes.push(h("polygon", { key: key++, className: "qs-hex__frame", points: hexPoints(cxA, cyA, r), stroke: tone.frame, strokeWidth: stroke }));
      shapes.push(h("polygon", { key: key++, className: "qs-hex__frame", points: hexPoints(cxB, cyB, r * 0.6), stroke: tone.frame, strokeWidth: stroke * 0.8 }));
      shapes.push(h("polygon", { key: key++, className: "qs-hex__frame", points: hexPoints(cxC, cyC, r * 0.6), stroke: tone.frame, strokeWidth: stroke * 0.8 }));
      shapes.push(h("circle", { key: key++, className: "qs-hex__node", cx: cxA, cy: cyA, r: stroke * 2.4, stroke: tone.node, strokeWidth: stroke * 0.6 }));
      shapes.push(h("circle", { key: key++, className: "qs-hex__node", cx: cxB, cy: cyB, r: stroke * 1.5, stroke: tone.node, strokeWidth: stroke * 0.6 }));
      shapes.push(h("circle", { key: key++, className: "qs-hex__node", cx: cxC, cy: cyC, r: stroke * 1.5, stroke: tone.node, strokeWidth: stroke * 0.6 }));
    } else {
      /* Background layer: an even honeycomb with nodes on the joins. */
      var rr = props.cell || 44;
      var sw = Math.max(1, rr * 0.05);
      var stepX = rr * 1.5;
      var stepY = rr * Math.sqrt(3);
      var col = 0;
      for (var x = 0; x <= width + rr; x += stepX) {
        var offset = col % 2 ? stepY / 2 : 0;
        for (var y = -stepY; y <= height + stepY; y += stepY) {
          shapes.push(h("polygon", {
            key: key++, className: "qs-hex__frame",
            points: hexPoints(x, y + offset, rr),
            stroke: tone.frame, strokeWidth: sw
          }));
          if ((col + Math.round(y / stepY)) % 3 === 0) {
            shapes.push(h("circle", {
              key: key++, className: "qs-hex__node",
              cx: x + rr, cy: y + offset, r: sw * 3,
              stroke: tone.node, strokeWidth: sw
            }));
          }
        }
        col++;
      }
    }

    return h("svg", {
      className: cx("qs-hex", props.className),
      width: "100%",
      height: "100%",
      viewBox: "0 0 " + width + " " + height,
      preserveAspectRatio: props.preserveAspectRatio || "xMidYMid slice",
      role: "presentation",
      "aria-hidden": "true",
      focusable: "false",
      style: { opacity: opacity }
    }, shapes);
  }

  window.AlQawsan = {
    Button: Button,
    SectionHeading: SectionHeading,
    Card: Card,
    StatTile: StatTile,
    Badge: Badge,
    Timeline: Timeline,
    Field: Field,
    DataTable: DataTable,
    SiteHeader: SiteHeader,
    SiteFooter: SiteFooter,
    HexPattern: HexPattern
  };
})();
