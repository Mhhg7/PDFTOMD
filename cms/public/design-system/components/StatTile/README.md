# StatTile

The KPI tile — one figure, one label, on navy or orange, with the hexagon pattern behind it.

Tiles alternate tone across a row, navy then orange, as on the impact slide. On navy the figure is `accent` orange (5.3:1); on orange it is `text-inverse` white, which is legible only because `stat` is 48px and bold — never shrink an orange tile's figure below that. Labels are `body-sm` / `ar-body-sm`, white on navy and `on-accent` navy on orange.

The approved brand figures are **2009** founded · **18** governorates · **5** strategic hubs · **1,500+** professionals · **Top 5** private pharma in Iraq · **GDP-certified** · **25+** manufacturing partners. Anything else needs a source and a period, and belongs in a `caption` beneath the row.

**You provide:** `value` and `label` as strings, in the language you are setting. Digits are Western in both languages, with tabular figures.

**Do**

- Run four tiles across a desktop row, two on mobile.
- Follow a row of tiles with a single navy banner sentence, as the stats slide does.
- Set `pattern={false}` when tiles sit on an already-patterned ground.

**Don't**

- Put a figure on a tile that you cannot source.
- Use orange tiles for more than half a row; the 60–30–10 proportion holds here too.
