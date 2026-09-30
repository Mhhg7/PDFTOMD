# Field

A labelled form input, 44px tall, with the hint, error and direction handling a bilingual form needs.

The label always sits above the input in `label` / `ar-label` — it is never a placeholder, and never the only thing identifying the field. This matters more here than in most systems: `border-strong` is 1.5:1 against white, so the border alone does not identify a control, and the visible label is what carries it.

Phone, email, URL and number inputs are set `dir="ltr"` automatically inside an Arabic form, while their labels stay RTL — a phone number written right-to-left is unreadable. Override with `dir` if a field needs something else.

Focus draws the `focus-ring` ring outside a 2px offset **and** darkens the border to `navy-900`. An error sets `aria-invalid`, turns the border `danger`, and renders the message in `danger` beneath — never colour alone.

**You provide:** `label`, and whatever of `type`, `placeholder`, `value`/`onChange`, `hint`, `error`, `required` and `disabled` the form needs. Pass a stable `id` when you have one; otherwise the component generates one and wires `aria-describedby` for you.

**Do**

- Write hints as instructions, not apologies: "Numbers stay left-to-right in both languages."
- Mark required fields with `required` and say so in the form's intro, since the asterisk is decorative.

**Don't**

- Use placeholder text as the label.
- Put the error message above the field, away from the control it describes.
