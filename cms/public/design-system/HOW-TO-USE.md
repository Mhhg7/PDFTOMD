# Using these files

This folder is the Al-Qawsan Scientific Bureau design system, exactly as it stands in the artifact.

## What is here

| Path | What it is |
|---|---|
| `README.md` | The brand book. Start here. |
| `guidelines/` | Bilingual and RTL rules, logo and hexagon motif, imagery and charts, application templates, voice, and the pre-publish checklist. |
| `tokens.json` | The source of truth for every colour, type style, space, radius, shadow, gradient and layout value. |
| `tokens.css` | The same tokens compiled to CSS custom properties, `@font-face` rules and a class per type style. **Generated — edit `tokens.json` and regenerate, never this file.** |
| `fonts/` | Cairo 600/700/800 and IBM Plex Sans Arabic 400/500/600 (Arabic + Latin + both digit sets), Montserrat 600/700/800 and Inter 400/500/600. All OFL-licensed. |
| `components/bundle.js` | Eleven React components on `window.AlQawsan`. A classic script — no build step. |
| `components/bundle.css` | Their stylesheet. Loads after `tokens.css`. |
| `components/index.d.ts` | Types, as documentation. |
| `components/<Name>/README.md` | What each component is for, what you pass it, and the do/don'ts. |
| `components/<Name>/preview.html` | A live bilingual example of that component. |
| `assets/Logos/README.md` | Which logo files marketing still needs to supply, and under what names. |

## In a web page

```html
<link rel="stylesheet" href="tokens.css">
<link rel="stylesheet" href="components/bundle.css">
<script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="components/bundle.js"></script>
```

Then:

```js
const { Button, StatTile } = window.AlQawsan;
ReactDOM.createRoot(el).render(
  React.createElement(Button, { variant: 'accent', lang: 'ar' }, 'كن شريكاً')
);
```

Serve the folder over HTTP rather than opening the files directly — fonts do not load from `file://` in most browsers. Any static server will do: `npx serve .` or `python3 -m http.server` in this folder.

For an Arabic page, set `<html lang="ar" dir="rtl">` and pass `lang="ar"` to the components.

## Without React

Everything except `components/bundle.js` is framework-free. `tokens.css` alone gives you every colour as a CSS variable and every type style as a class — `.h2`, `.ar-body`, `.stat` — which is enough to build the whole brand by hand.

## Opening the previews

Each `components/<Name>/preview.html` is a fragment, not a whole page: it expects `tokens.css`, `bundle.css` and the bundle to be loaded already. To view one, wrap it:

```html
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="../../tokens.css">
  <link rel="stylesheet" href="../bundle.css">
  <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="../bundle.js"></script>
</head>
<body>
  <!-- paste the preview file's contents here -->
</body>
</html>
```

## Keeping this copy current

The artifact is the live version. These files are a snapshot taken on 20 September 2026. If the system changes there, export again rather than editing both.
