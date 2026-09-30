# SectionHeading

The overline, title and lead that open a section — the brand's most-repeated typographic unit.

The overline is the eyebrow above the title, set in `text-link` at `overline`: uppercase with +8% tracking in English, and in Arabic neither uppercased nor tracked, because both break letter joining. The component handles that switch from `lang`; never hand-style an Arabic overline.

Titles use `h2` sizing by default; pass `level` to place `h1`, `h3` or `h4` in the document outline without changing the visual step. The lead is `lead` / `ar-lead`, capped at 60 characters of measure in English and 52 in Arabic.

Set `onDark` on navy, indigo or `gradient-horizon` grounds: the title becomes `text-inverse`, the lead `text-on-dark-muted`, and the overline `gold-400` — Deep Orange is too dark to read on navy.

**You provide:** `title`, and optionally `overline` and `lead` as strings or nodes, plus the `level` that is correct for the page outline.

**Do**

- Keep one overline per section, and make it a label, not a sentence.
- Pair the English and Arabic headings at the same weight and colour when both appear.

**Don't**

- Use it for a hero — `display` / `ar-display` appears once per page and is set directly.
- Stack two headings without a section of content between them.
