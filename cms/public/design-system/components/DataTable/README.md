# DataTable

The product, stock and price table: navy header, 48px rows, zebra stripes, tabular figures.

The header is `surface-inverse` with `text-inverse` `label` text; `dense` swaps it for a `gray-50` ground with navy text for long internal tables. Rows are `row-height` with `border-default` rules and `gray-25` on even rows.

Give every numeric column `align: "end"`. That is the whole trick for a bilingual table: the trailing edge is the right in English and the left in Arabic, so numbers line up correctly in both without a second stylesheet. All cells carry tabular figures, so batch codes and quantities sit in columns.

Status belongs in a `Badge`, not in coloured text, and the caption carries the source and the period — a stock figure without a date is not information.

**You provide:** `columns` as `{ key, label, align }` and `rows` as objects keyed to those columns; cell values may be nodes, which is how badges get in. Sorting, pagination and fetching are yours — this is a presentational table.

**Do**

- Keep column labels to one or two words, in the page's language.
- Use Western digits and ISO-style dates (2026-11) in both languages.

**Don't**

- Colour a whole row red to mean recalled; use the badge and keep the row legible.
- Drop the caption because the table "obviously" shows current stock.
