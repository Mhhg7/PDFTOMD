#!/usr/bin/env bash
# Downloads the brand assets exported from the Figma file
# "Al-Qawsan Brand Guidelines" (node 40:31, Mobile website — Home (390)).
# The Figma asset URLs are short-lived (about 7 days). If a download fails,
# re-run get_design_context / download_assets on node 40:31 for fresh URLs.
set -euo pipefail
cd "$(dirname "$0")/assets"
mkdir -p icons logos
B=https://www.figma.com/api/mcp/asset
get() { curl -fsSL -o "$2" "$B/$1" && echo "ok  $2"; }

# Logo layers (AQ/Logo – full colour)
get 1795d77a-a22a-4b7a-991d-b2f1b37fd2c6.svg logos/arcs-gold.svg
get 459718d3-e494-4dda-b2e4-71b9505f72b6.svg logos/arcs-orange.svg
get 3aeef582-e8d0-4953-849a-325c7cfd6da0.svg logos/wordmark-navy.svg

# Icons
get cfb08fb6-fe90-4fac-85e7-f1bfabc1779c.svg icons/shield-check.svg
get fc001d6c-613c-4095-97f2-1cce1fb64e26.svg icons/thermometer.svg
get 22ddbaf8-77ba-415c-9114-33ec340048ee.svg icons/truck.svg
get 6626d0ef-3f9a-48af-8604-6498af65c9fc.svg icons/menu.svg

# Two-arcs motif (hero size, section-title size)
get f5c062ee-b19b-4cae-b69c-a7940a8f706f.svg arc-hero.svg
get 4aa00bba-cddf-487e-ab32-c5468c56201b.svg arc.svg

# Partner logos
get f65d3ed0-5464-406f-b89f-ff04a3c6f273.png logos/sibionics.png
get 7dfb9561-b2d8-41ff-a058-52acc8542e01.png logos/unimed.png
get ce99013f-8b3b-44a1-a99d-11b16a180873.png logos/corona.png
get cf3f4add-4080-4129-8721-7522bceac8e7.png logos/dawah.png
get b50c115c-8e20-4c07-bf00-9b0007d73040.png logos/thera.png
get eed61972-8897-4b03-93b1-517f81221405.png logos/microsynergy.png
get 9021101d-56c9-47c2-8068-2c3a89274b0d.png logos/dorcas.png
get 9a16d3aa-68fa-4715-bb8b-aed349eb93c0.png logos/sdi.png
echo "done"
