# Bilingual & RTL

Arabic is the primary language for Iraqi audiences; English is primary for international partners. Both are written as originals — a page that reads as a translation has failed, whichever direction it was translated in.

## Setting Arabic

Arabic runs 10–15% larger than English at the same visual weight, and it needs more leading: dots and descenders collide at tight line-height. The `ar-` type styles already carry both corrections, so pair `h2` with `ar-h2`, never with `h2` itself.

| Rule | Why |
|---|---|
| No `letter-spacing`, ever | Tracking breaks the joins between letters |
| No italics, no synthetic oblique | Arabic has no italic; emphasise with weight 600/700 or `text-heading` |
| No uppercase styling | There is no case in Arabic; `ar-overline` is set sentence-case and untracked |
| Line-height 1.8 body, 1.4 headings | Baked into the `ar-` styles |
| Diacritics only to disambiguate | Tashkeel on ambiguous medical terms; never decoratively |

Use Arabic punctuation: `،` `؛` `؟`. Periods and closing quotes belong at the **end** of the line, which in RTL is the left. If a full stop appears at the *start* of a line — `.والاحترافية`, as in the current onboarding deck — the paragraph direction is wrong. Fix the direction of the text frame; right-aligning an LTR paragraph is not the same thing and will keep producing the bug.

## Numerals

Digits are Western (0–9) by default in both languages — the convention in Iraqi pharmaceutical practice, and unambiguous on a price list or a dosage. Arabic-Indic numerals (٠١٢٣٤٥٦٧٨٩) are the exception, for formal correspondence to government bodies that require them.

Both Arabic faces carry both sets, so this is a content decision, not a font one: type the digits you mean. There is no feature setting to flip between them — `font-feature-settings` does not convert 4 into ٤, and a page that shows the wrong set is a page whose source text has the wrong characters.

| | Western | Arabic-Indic |
|---|---|---|
| Web, app, price list, dosage, batch, date, phone | 0 1 2 3 4 5 6 7 8 9 | — |
| Formal letter to a ministry, where required | — | ٠ ١ ٢ ٣ ٤ ٥ ٦ ٧ ٨ ٩ |

Set every figure with tabular figures, so columns of quantities and batch numbers align:

```css
.ar-data, .data, .stat { font-variant-numeric: tabular-nums; }
```

A figure that carries a symbol — 1,500+ · 24/7 · 100% · +964 770 … — is a left-to-right run inside right-to-left text, and the bidi algorithm will reorder the parts it treats as neutral. Isolate it so it always reads the way it was written:

```html
<bdi dir="ltr">1,500+</bdi>
```

`StatTile` does this for you, and `Field` sets phone, email, URL and number inputs to `dir="ltr"` for the same reason. Anywhere else you paste a figure into Arabic copy — a table cell, a headline, a caption — wrap it.

Dates are ISO-style (2026-11) in both languages. Never mix the two digit sets in one document.

## Pairing the two languages

Side by side, Arabic takes the right column and English the left, titles sharing a baseline, Arabic one size step up. Stacked, Arabic goes first in Iraq-facing material and English first in partner-facing material. Both languages carry the same colour and weight hierarchy — the moment one is set in `text-muted` or a size smaller than its counterpart, it reads as an afterthought.

Do not mix scripts within a line, except for brand and product names: توزيع منتجات Bioderma is correct.

## Direction in code

```html
<html lang="ar" dir="rtl">
```

Lay out with logical properties so one stylesheet serves both directions:

```css
.card { padding-inline-start: var(--space-5); border-inline-start: var(--border-width) solid var(--border-default); }
```

**Mirror:** layout direction, navigation order, arrows and chevrons, progress bars, breadcrumbs, timeline direction, the logo corner, table numeric alignment.

**Do not mirror:** the logo artwork itself, numerals, a chart's time axis (chronology stays left→right unless the whole chart is Arabic-only), media play icons, checkmarks, clocks, phone numbers, URLs, email addresses.

Inside an Arabic form, phone, email and URL fields are set `dir="ltr"` while their labels stay RTL. The `Field` component does this for you.
