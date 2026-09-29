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
- Fonts are **Alexandria** (headings) and **Cairo** (body) in both languages, at the
  client's request. This overrides the design system's Montserrat / Inter / IBM Plex Sans
  Arabic through the `--font-*` tokens in `site.css`.
- The map follows the brand rule: served governorates in `navy-800`, the five hubs
  (Baghdad, Basra, Erbil, Mosul, Najaf) as `orange-500` pins, connector lines drawn once.
- There is no logo file yet (the design system ships none), so the name is set in type.

## Still to supply

Everything marked **To be confirmed** on the site: phone number, confirmed email and
working hours, hub addresses, leadership names, partner logos and list, the registered
product list, photography, and legal wording. Forms are front-end only; connect them to
email or a CRM before launch.
