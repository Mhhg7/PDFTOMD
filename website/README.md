# Al-Qawsan Scientific Bureau website

Bilingual (English / Arabic, RTL) website built on the Al-Qawsan design system and the
structure in `proposals/alqawsan-website-proposal.pdf`: 8 tabs, 27 sub-pages, the homepage
section order, the linking rules and the four-column footer.

## Run it

Serve this folder over HTTP (fonts do not load from `file://`):

```sh
cd website
python3 -m http.server 8000
# open http://localhost:8000
```

## Files

| Path | What it is |
|---|---|
| `index.html` | Page shell: loads fonts, design-system CSS, map data, content and app |
| `content.js` | All copy in English and Arabic, navigation, pages and shared data |
| `site.js` | Hash router, header / mega menu / mobile drawer, search, Iraq map, slider, forms |
| `site.css` | Website layer on top of the design system; tokens only |
| `assets/iraq-map.js` | Iraq's 18 governorates as SVG paths (generated) |
| `tools/build_map.py` | Regenerates the map from geoBoundaries (CC BY 4.0) |
| `design-system/` | Snapshot of the Al-Qawsan design system (tokens, components, guidelines, fonts) |

## Design decisions

- Colours, radii, spacing, shadows, focus treatment and component markup come from
  `design-system/` (`qs-btn`, `qs-card`, `qs-stat`, `qs-badge`, `qs-timeline`, `qs-field`,
  `qs-table`, `qs-footer`, HexPattern geometry).
- Fonts, at the client's request: **Tajarib** for Arabic headings, **Alexandria** for
  English headings, **Cairo** for body text in both languages. This overrides the design
  system's Montserrat / Inter / IBM Plex Sans Arabic through the `--font-*` tokens in
  `site.css`.
- The map follows the brand rule: served governorates in `navy-800`, the five hubs
  (Baghdad, Basra, Erbil, Mosul, Najaf) as `orange-500` pins, connector lines drawn once.
- The logo is the client-supplied lockup at `assets/logos/alqawsan-horizontal-color.png`
  (a vector SVG from marketing would be sharper).

## Tajarib font files (not in this repository)

Tajarib is by Harf Type / Harf Library (abdulmalik@harflibrary.com). Its licence is free
and allows commercial use on websites, but forbids making the font files available for
download, modifying them or converting them. This repository is public, so the files are
**not committed** (`website/fonts/tajarib/` is in `.gitignore`).

To use them, copy these original files, unchanged, into `website/fonts/tajarib/`:

- `Tajarib_Typeface_Medium.otf`
- `Tajarib_Typeface_Bold.otf`
- `Tajarib_Typeface_Black.otf`

Without them, Arabic headings fall back to Alexandria and everything else still works.

## Still to supply

Everything marked **To be confirmed** on the site: phone number, confirmed email and
working hours, hub addresses, leadership names, partner logos and list, the registered
product list, photography, and legal wording. Forms are front-end only; connect them to
email or a CRM before launch.
