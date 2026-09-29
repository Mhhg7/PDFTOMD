# Timeline

The growth-journey track: hollow orange nodes on a navy rule, with the year set in Deep Orange.

Horizontal on desktop and vertical below the tablet breakpoint — pass `orientation`. Direction follows `lang`: the track runs left to right in English and right to left in Arabic, because a timeline is a reading order, not a chart axis. (A chart's time axis is the opposite case and stays left to right; see *Imagery, icons & data*.)

Nodes are the molecule motif at small scale: a 24px hollow circle with a 3px `orange-500` stroke on a 2px `navy-800` rule. Years are `h3`-sized in `orange-700`, which is the only orange that passes for text at that size; the description is `body-sm` / `ar-body-sm`.

**You provide:** `items`, each `{ year, title, description }`, already in chronological order and already in the page's language.

**Do**

- Keep four to six entries; a timeline with ten nodes is a table.
- Use real, dated milestones — 2009 founding, 2015 Lara, 2019 Sanaya, the five hubs.

**Don't**

- Animate the nodes more than once per page; the draw-in is the signature move and it loses its force on repetition.
- Mirror the node artwork itself when mirroring the track.
