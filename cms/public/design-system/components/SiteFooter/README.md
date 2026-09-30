# SiteFooter

The navy footer: the gold-to-orange accent bar on its top edge, the hexagon pattern behind, contact and group links.

The bar is `accent-bar` tall in `gradient-arc` — the echo of the notebook's gold head rule, and the one place a gradient touches a functional surface. The pattern layer sits at `opacity-pattern`; it is decorative, marked `aria-hidden`, and never rises above `opacity-pattern-max`.

Links are `text-inverse` and move to `accent` on hover. Phone numbers, email addresses and URLs are marked `ltr: true` so they stay left-to-right inside an Arabic footer. Legal text is `caption` in `text-on-dark-muted` above a `border-inverse` rule.

**You provide:** `columns` as `{ title, lines }`, where a line is `{ text, href, ltr }`, and the `legal` line. The footer should carry the address (Qadisiyah District, Baghdad, Iraq), the contact details, the group entities, social links and the QR code used on the notebook covers.

**Do**

- Name the group entities exactly: CAS Development, Lara Scientific Office, Sanaya Scientific Office.
- Keep the same three columns across both languages so the pages stay comparable.

**Don't**

- Put the full-colour logo here; the footer is navy, so the reversed white version is the only correct one.
- Add a newsletter form without a stated purpose and a privacy line.
