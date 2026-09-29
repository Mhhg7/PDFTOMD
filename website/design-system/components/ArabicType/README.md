# Arabic type

A specimen of the two Arabic faces this system ships, set side by side at every weight, so the choice between them can be made by looking.

This is a reference page, not a component — there is nothing to import. It exists because a bilingual system has one decision that a token list cannot settle on its own: which face carries which role in Arabic, and at what weight.

As it stands, **Cairo** carries every Arabic heading, card title and slide title (600, 700, 800), and **IBM Plex Sans Arabic** carries every Arabic paragraph, label, button, input and table cell (400, 500, 600). That split follows the v1.0 specification: Cairo is geometric and sits beside Montserrat so a bilingual headline reads as one family; Plex Arabic is narrower and evener, built for the small sizes a dosage line or a batch column needs.

The top row of the page shows the one string that appears in both faces, in the two places it actually appears in the system, because that is the comparison people mean when they say the Arabic should look like something else.

Two things to know before changing the split. Cairo ships here at 600 and above, so moving body copy to Cairo means adding a 400 weight, not just editing a token. And Arabic at weight 400 reads lighter than Latin at 400 beside it — if the complaint is that Arabic body copy looks washed out next to English, the fix is a weight step, not a different family.
