# SiteHeader

The site header: 72px, white, brand at the reading start, navigation, the language switch, and one accent CTA.

Navigation links are `button`-styled in `text-heading`; the active item carries a 3px `accent` underline along the full width of the link and `aria-current="page"`. Hover moves a link to `text-link`.

The language switch — EN | عربي — is always visible, never hidden in a menu. It is the one control that must be findable by someone who cannot read the current language, which is why it sits beside the CTA rather than in an overflow.

Everything mirrors with `lang="ar"`: the brand moves to the right, the navigation order reverses, the CTA lands at the left. The logo corner follows the reading start — top-right in Arabic, top-left in English.

**You provide:** `brand` (a node — pass the Arc logo image once the vector originals are in the **Logos** asset group; until then it sets the name in display type), `items` as `{ label, href, active }`, and the `cta` label. Routing is yours.

**Do**

- Keep the CTA to one, and make it `accent` — this is the one place the orange button belongs by default.
- Give the header `shadow-lg` only once the page has scrolled.

**Don't**

- Add a second row of navigation; use a mega-menu panel at `radius-lg` instead.
- Translate the language switch labels; each is written in its own language.
