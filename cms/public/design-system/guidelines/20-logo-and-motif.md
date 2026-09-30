# Logo & the hexagon motif

## The Arc logo — rules, pending artwork

The vector originals are not part of this system (see the last section of the brand book). These are the rules that apply the moment they are added to the **Logos** asset group; until then, set the name in `h3` / `ar-h3` in `text-heading` and keep the logo zone empty.

The mark has three components: the **Arc symbol** (three sweeping gold→orange curves with an upward arrow — the bow, قوس, launching forward), the Arabic wordmark مكتب القوسان العلمي, and the English wordmark Al-Qawsan Scientific Bureau.

| Version | Where it goes |
|---|---|
| Full colour, horizontal | Default, on `surface` and other light grounds |
| Reversed (white) | On navy, indigo, orange, or a photo under a navy overlay |
| Arc symbol only | Favicon, app icon, slide corner, social avatar, watermark |
| Mono navy | Fax, stamps, single-colour print, embossing |
| Hexagon lockup | Stationery, notebooks, co-branding with the group marks |

Clear space is at least the height of the Arabic letter ك — about 25% of the logo height — on all four sides. Minimum size is 32mm or 120px wide for the full logo, 8mm or 24px for the Arc alone.

Placement follows the reading start: top-right in Arabic layouts, top-left in English layouts, top-right in bilingual ones. On slides the Arc sits top-right at 80–100px, in the same place on every slide.

Never recolour the Arc — it is the `gradient-arc` ramp, or solid white, or solid navy. Never put the full-colour logo on orange or gold; use the white version. Never stretch, rotate, outline, glow or shadow it. Never place it on a busy photograph without a `navy-800` overlay at 60% or more. The yellow "wave" decoration on the current office door sign is not part of the identity and does not appear beside the logo or anywhere else.

## The molecular hexagon

A hexagon frame with a connector line, a hollow circle node at its end, and a larger hollow hub circle where connectors meet. It reads three ways at once: the benzene ring of chemistry, connected cells as teamwork, and one hexagon per group entity.

**Construction.** Flat-top or pointy-top, consistently within a layout. Stroke is 3–4% of the hexagon's width, with rounded joins. A node circle is about three times the stroke in diameter; the hub is larger than the nodes.

**Colour by ground:**

| Ground | Frame | Connectors | Pattern layer |
|---|---|---|---|
| `navy-800` / `indigo-600` | `white` | `orange-500` | `indigo-400` at `opacity-pattern-strong` |
| `orange-500` | `white` | `navy-800` | `orange-300` at 0.35 |
| `surface` | `navy-800` or `ink` | `orange-500` | `gray-100`, or navy at `opacity-watermark` |

**Use it on** covers, hero backgrounds, section dividers, slide backgrounds, page watermarks, group lockups and icon containers. Number and step badges may sit inside small hexagons.

**Never** fill a hexagon with a gradient or a photograph, rotate the network at random, place more than one focal cluster in a layout, or run the pattern above `opacity-pattern-max` — or above `opacity-watermark` behind body text.

The `HexPattern` component draws both the focal cluster and the background layer at the correct opacities; use it rather than redrawing the geometry.

## Group co-branding lockup

Three hexagons joined by the connector network:

1. **Large hexagon** — Al-Qawsan Scientific Bureau. Always the largest, and always at the reading start: left in LTR, right in RTL.
2. **Top hexagon** — CAS Development (mountains and cross mark).
3. **Trailing hexagon** — the pharmaceutical manufacturing partner (three-arrow mark).

On print, the spelling is **CAS Development**. The current notebooks read "Cas Development" — correct it in the next reprint.
