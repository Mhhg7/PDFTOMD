Al-Qawsan Scientific Bureau — **مكتب القوسان العلمي** — distributes certified medicines and healthcare products across all 18 governorates of Iraq. This system is the single source of truth for anything built in that name: web, digital products, presentations, print, stationery, signage and social. Arabic and English are equal here; neither is a translation of the other.

Where the older materials disagree with this system, this system wins. Build from the tokens — never from a screenshot of a slide.

**Tone:** Precise · Trustworthy · Growing — *الدقة • الثقة • النمو*

---

## Naming, before anything else

Write the brand as **Al-Qawsan Scientific Bureau** in English and **مكتب القوسان العلمي** in Arabic. Never "Office", never "Alkawsan", never "Al-Qawsan Scientific Office". Short form: Al-Qawsan / القوسان. The parent is Al-Qawsan Group (www.alqawsangroup.com); the group entities are **CAS Development** (capitalised, not "Cas"), **Lara Scientific Office** and **Sanaya Scientific Office**. The address is **Qadisiyah District, Baghdad, Iraq** — one spelling, always.

Facts that may be stated without further sourcing: founded 2009 in Baghdad · among Iraq's top five private pharmaceutical companies · all 18 governorates · 5 strategic hubs (Baghdad, Basra, Erbil, Mosul, Najaf) · 1,500+ professionals · GDP-certified · partner of the Ministry of Health · 25+ international manufacturing partners.

## Colour

`navy-800` is the brand. It carries headings, primary buttons, headers, footers and dark panels. `orange-500` is action: one accent surface per section, never a field of them. `gold-400` is a line, never a word — it is 1.7:1 on white and drops out of any text it is used for. `indigo-600` is the secondary, for sub-brand and chart work.

Hold the 60–30–10 proportion: about 60% `white` and `surface-alt`, 30% navy and indigo, 10% orange and gold. A page that reads as orange has broken the system.

Pairings to build from, each measured:

| Set this | On this | Ratio | Good for |
|---|---|---|---|
| `text-body` (navy-900) | `surface` | 16.8 : 1 | all text |
| `text-heading` (navy-800) | `surface` | 12.1 : 1 | all text |
| `text-muted` (gray-600) | `surface` | 6.0 : 1 | all text |
| `text-link` (orange-700) | `surface` | 4.7 : 1 | all text |
| `text-inverse` (white) | `surface-inverse` | 12.1 : 1 | all text |
| `text-on-dark-muted` (navy-200) | `surface-inverse` | 6.5 : 1 | all text |
| `on-accent` (navy-800) | `surface-accent` | 5.3 : 1 | all text — the default for orange |
| `on-accent` (navy-800) | `gold-400` | 7.0 : 1 | all text |
| `text-inverse` (white) | `indigo-600` | 6.0 : 1 | all text |
| `accent` (orange-500) | `surface-inverse` | 5.3 : 1 | all text — the stat-number pair |
| `text-inverse` (white) | `surface-accent` | 2.3 : 1 | **only** bold text at 24px and above |
| `orange-500` | `surface` | 2.3 : 1 | **only** display numerals and icons — never words |

Three pairs from the source miss the floor and are kept exact rather than re-tinted; work around them instead of changing the colours. `success` on `surface` is 4.1:1, so green text goes at 24px, or 19px bold, and small "In stock" badge text is set in `navy-800` with a green dot beside it. `warning` on `warning-bg` is 2.3:1 — same treatment, navy text and a gold dot. `border-strong` (gray-200) is 1.5:1 on white, so an input is never identified by its border alone: it always carries a visible label above it and the focus treatment below.

Status never travels on hue alone. Every status carries its word — Delivered / تم التسليم, Low stock / مخزون منخفض, Recall / سحب — and `danger` is reserved for real alerts: recalls, cold-chain breaks, errors. Nothing decorative is ever red.

Focus is two things at once, because orange alone is 2.3:1 on white: a `focus-ring-width` ring in `focus-ring` outside a 2px offset of the surface, **and** the control's own border darkening to `navy-800`.

Only three gradients exist — `gradient-arc` for the accent bar and icon fills, `gradient-horizon` for hero and cover surfaces, `gradient-depth` for dark cards. Nothing else is gradated, and type is never gradated at all.

## Type

English sets **Montserrat** for display and headings and **Inter** for body and UI. Arabic sets **Cairo** for display and headings and **IBM Plex Sans Arabic** for body and UI. All four are OFL-licensed and ship with this system as woff2 files under `fonts/`, at the weights the scale actually uses — Cairo 600/700/800, IBM Plex Sans Arabic 400/500/600, Montserrat 600/700/800, Inter 400/500/600. Load those files rather than a web font service, so Arabic renders in Cairo on the first paint instead of flashing through Tahoma.

The two Arabic files carry Latin as well as Arabic, so a figure or a Latin brand name inside an Arabic line — 18 محافظة, توزيع منتجات Bioderma — is set in the same face as the Arabic around it instead of dropping to a system font with different proportions. They carry the Arabic-Indic digits too. The English stacks name the matching Arabic face second for the reverse case, an Arabic phrase inside an English line. For PowerPoint and Word, where web fonts cannot be embedded, install all four on company machines; the fallback is Segoe UI for English and Segoe UI or Tahoma for Arabic. Use `display` … `h5`, `lead`, `body`, `body-sm`, `caption`, `label`, `button`, `stat` and `data` for English; the matching `ar-` styles for Arabic — they already carry the one-step size increase and the looser leading Arabic needs, so never take an English size and set Arabic in it.

Numbers are Western digits (0–9) in both languages — prices, dosages, dates, phone numbers, batch codes — set in `data` or `stat` with tabular figures (`font-variant-numeric: tabular-nums`). Arabic-Indic numerals (٠–٩) are for formal correspondence to government bodies that require them, and both Arabic faces carry them; the full rules, including how to isolate a figure like 1,500+ or 24/7 so RTL does not reorder it, are in *Bilingual & RTL*.

Nothing below 12px in English or 13px in Arabic appears on screen. Body measure is 60–75 characters in English, 50–65 in Arabic. `display` appears once per page.

`overline` is uppercase with +8% tracking and set in `text-link`; `ar-overline` is neither uppercase nor tracked, because both break Arabic letter joining. Arabic is never italicised and never letter-spaced — emphasis is weight 600/700, or colour.

## Space, shape and elevation

Everything measures in multiples of 4 (`space-1` … `space-16`); layout uses the multiples of 8. `space-5` is card padding and the desktop gutter, `space-12` is desktop section padding, `space-4` is the mobile gutter and side margin.

The grid is 4 columns below `bp-tablet`, 8 to `bp-desktop`, 12 above, capped at `container-max` and centred beyond `bp-wide`. Slides use 12 columns with 80px outer margins.

Radii are fixed by role, not by taste: `radius-md` on buttons and inputs, `radius-lg` on cards, panels and partner tiles, `radius-xl` on hero media, `radius-full` on pills and number circles, `radius-sm` on small tags.

Separation comes from `border-default` first and elevation second. `shadow-md` is for cards, `shadow-lg` for things that float over the page. Shadows are navy-tinted; there is no black in this system.

## States

Hover darkens one step — `navy-800` → `navy-900`, `orange-500` → `orange-600`, `indigo-600` → `indigo-700` — over `duration-fast`. Pressed goes one step further. Disabled is `gray-100` ground with `text-placeholder`, and never announces anything by opacity alone. Motion is 150ms for hover, 250ms for panels, 400ms for a hero, all on `cubic-bezier(0.2, 0.8, 0.2, 1)`; the signature move is the connector lines drawing in and the nodes popping, used once per page. Everything respects `prefers-reduced-motion`, and nothing in this system bounces or loops.

## Bilingual and RTL

Set `dir="rtl"` and `lang="ar"` on Arabic pages and lay out with logical properties (`margin-inline-start`, `padding-inline-end`, `inset-inline-start`) so one stylesheet serves both directions. Mirror layout, navigation order, chevrons, progress, breadcrumbs, timelines and the logo corner. Do not mirror the logo artwork, numerals, a chart's time axis, media icons, checkmarks, phone numbers or URLs. Phone and email inputs stay `dir="ltr"` inside an Arabic form. The full rules, including the punctuation bug to fix in the existing decks, are in *Bilingual & RTL*.

## Iconography

Outlined icons, 1.75px stroke at 24px, rounded caps and joins — **Lucide** or **Phosphor Regular**. Filled style only inside a coloured circle or hexagon. Sizes: 16 inline, 20 in buttons, 24 default, 32 for features, 48 in feature cards. `navy-800` on light, `white` on navy, `orange-500` inside a white circle on navy. Keep one icon per concept across everything: pill, capsule, flask, truck, warehouse, map-pin, shield-check (GDP), thermometer (cold chain), handshake, hospital, stethoscope, chart-up.

## The signature graphic

The hexagon-and-connector network — benzene ring, connected cells, one hexagon per group entity — is the most recognisable thing the brand owns. One focal cluster per layout; as a background layer it stays at `opacity-pattern` and never exceeds `opacity-pattern-max`, dropping to `opacity-watermark` behind reading matter. Use the `HexPattern` component rather than redrawing it. *Logo & the hexagon motif* has the construction and the group lockup.

## What this system does not ship

**The Arc logo is not in this system.** The source is a written specification, not artwork, and a real mark is never redrawn from a description — so no arc, wordmark or hexagon lockup file is included here. Until the marketing team supplies the vector originals, set the name in `h3`/`ar-h3` type in `navy-800` and leave the logo area clear at the specified corner; every placement, clear-space and minimum-size rule is in *Logo & the hexagon motif*, ready for the files when they arrive. Drop them into the **Logos** asset group.

Photography is likewise supplied, not generated: real, documentary images of Al-Qawsan's own warehouses, fleet, cold-chain rooms and teams, under a `navy-800` overlay at 60–80% wherever text sits on them.
