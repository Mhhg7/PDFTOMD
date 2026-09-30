# HexPattern

The molecular hexagon — the brand's signature graphic, as a focal cluster or a background layer.

`variant="cluster"` draws the group shape: one large hexagon with a hub node, joined by connectors to two smaller hexagons. That is the Bureau with CAS Development and the manufacturing partner, and it is why the large hexagon always sits at the reading start. **One focal cluster per layout** — a second one turns a signature into wallpaper.

`variant="field"` draws the honeycomb background used on covers, footers, section dividers and slide grounds. `tone` picks the colours for the ground it sits on: `navy` gives an `indigo-400` layer at `opacity-pattern-strong`, `orange` an `orange-200` layer at 0.35, `light` a navy frame with orange connectors, and `watermark` the 6% navy layer that is the maximum allowed behind body text.

Geometry follows the spec: flat-top hexagons, stroke at 5–7% of the hexagon width, node circles about three times the stroke, a larger hub where connectors meet.

**You provide:** `width`, `height` and `cell` (the circumradius) to match the box it fills; the SVG scales with `preserveAspectRatio="xMidYMid slice"`. Position it yourself — it is a presentational layer, so give it an absolutely-positioned container and put your content above it.

**Do**

- Keep the pattern tone-on-tone with its ground.
- Let the pattern bleed off the edge rather than fitting a whole number of hexagons.

**Don't**

- Fill a hexagon with a gradient or a photograph, or rotate the network at random.
- Raise `opacity` above `opacity-pattern-max`, or above `opacity-watermark` behind reading matter.
- Use it as a substitute for the Arc logo. It is the brand's texture, not its mark.
