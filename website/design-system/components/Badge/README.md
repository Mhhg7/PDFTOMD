# Badge

The pill that labels status or a credential, always carrying a word and a dot rather than a colour alone.

Six tones: `brand` for credentials such as GDP-certified, `accent` for commercial labels such as Exclusive Distributor, and `success` / `warning` / `danger` / `info` for state. Each is a 50-scale tint with a `radius-full` shape at `caption` size, weight 600.

Two tones carry navy text rather than their own colour, and this is deliberate: `success` on `success-bg` measures 3.7:1 and `warning` on `warning-bg` 2.3:1, both below the floor for text this size. The brand's greens and golds are kept exact, and the badge puts the hue in the dot where contrast is not a text requirement. `accent` steps its text to `orange-800`, because `orange-700` on `orange-50` lands at 4.3:1.

**You provide:** the label as children — a real word in the language of the page ("In stock", "متوفر"), never an abbreviation a pharmacist would have to decode.

**Do**

- Keep the dot. Colour-blind readers, printed price lists and photocopies all depend on the word, and the dot keeps the row scannable for everyone else.
- Reserve `danger` for recalls, cold-chain breaks and out-of-stock — never for emphasis.

**Don't**

- Use a badge as a button; it is not interactive.
- Put more than two badges in a table cell.
