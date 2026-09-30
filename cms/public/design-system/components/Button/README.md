# Button

The action control, in five variants that map to how much weight an action carries.

Use `primary` for the single main action in a view — Contact Us, Submit request. Use `accent` for marketing calls to action, at most one per section: it is the orange surface, and orange stops meaning "act" the moment there are two of them on screen. `secondary` is the outlined navy button for the action beside the main one, `ghost` for tertiary actions and toolbars, and `danger` only for destructive work — never for "Cancel", never for emphasis.

Heights come from `control-height-sm` (36px), `control-height` (44px) and `control-height-lg` (52px), with `radius-md` corners and 22px of inline padding. Hover darkens one step; the `accent` hover goes to `orange-600`, where white text becomes legible again.

Focus is deliberately two things, because Qawsan Orange alone is 2.3:1 on white and would not be a visible indicator: the control draws a `focus-ring-width` ring in `focus-ring` outside a 2px offset, **and** darkens its own border to `navy-900`. Do not remove either half.

**You provide:** the label as children, and optionally `iconStart` / `iconEnd` as 20px icon nodes. Directional icons are mirrored automatically when `lang="ar"`; wrap an icon that must not flip — a checkmark, a play triangle, a logo — in a span with `qs-btn__icon--noflip`.

**Do**

- Write the label as a verb phrase: "Become a Partner", "كن شريكاً".
- Pass `href` when the button navigates; it renders an `<a>` and stays keyboard-accessible.
- Put buttons on navy inside a `qs-on-dark` container so the secondary and ghost variants invert.

**Don't**

- Put two `accent` buttons in one section.
- Use `accent` with white text below 24px — the component sets `on-accent` navy for you.
- Communicate a disabled state by opacity alone; the component swaps to `gray-100` and `text-placeholder`.
