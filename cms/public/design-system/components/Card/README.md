# Card

The content container, in the three surfaces the brand allows: white, the Depth gradient and orange.

`content` is the default — `surface` with a `border-default` hairline, `shadow-md` and `radius-lg`, padding `space-5`. `dark` fills with `gradient-depth` for value cards and agenda panels. `accent` fills with `surface-accent`, and its copy is `on-accent` navy: white body text on Qawsan Orange is 2.3:1 and is only ever used at 24px bold and above.

`number` renders the 48px circle from the agenda and step patterns — orange with navy digits on white and dark cards, navy with white digits on orange cards. Numbers stay Western digits with tabular figures even in Arabic layouts.

**You provide:** `title`, `body` and optionally `overline`, `number`, and arbitrary `children` for a card that holds a list, a figure or a button.

**Do**

- Alternate `dark` and `accent` across a grid of agenda cards, as the onboarding deck does.
- Keep card titles to one line at `h4` / `ar-h4`.
- Give a grid of cards equal heights; ragged card bottoms read as carelessness in a system whose first value is Commitment.

**Don't**

- Put a gradient behind a card that already sits on `gradient-horizon`.
- Add a coloured left border as an accent — that is not in this identity.
- Use `accent` cards for anything longer than a short paragraph; long copy belongs on white.
