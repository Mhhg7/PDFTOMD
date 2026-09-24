"""Build the Al-Qawsan Scientific Bureau website structure proposal (PDF).

Usage:
    pip install reportlab
    python proposals/build_proposal.py

Output: proposals/alqawsan-website-proposal.pdf
"""

import math
import os
from datetime import date

from reportlab.graphics.shapes import (Circle, Drawing, Line, Polygon, PolyLine,
                                       Rect, String)
from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.units import mm
from reportlab.lib.utils import simpleSplit
from reportlab.pdfbase.pdfmetrics import stringWidth
from reportlab.platypus import (BaseDocTemplate, CondPageBreak, Flowable, Frame,
                                KeepTogether, NextPageTemplate, PageBreak,
                                PageTemplate, Paragraph, Spacer, Table,
                                TableStyle)
from reportlab.platypus.tableofcontents import TableOfContents

OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)),
                   "alqawsan-website-proposal.pdf")

# ---------------------------------------------------------------- palette
NAVY = colors.HexColor("#12355B")
TEAL = colors.HexColor("#0E7C86")
TEAL_D = colors.HexColor("#0A5F67")
TEAL_L = colors.HexColor("#E3F2F3")
TEAL_M = colors.HexColor("#A9D6D9")
ORANGE = colors.HexColor("#F2A541")
RED = colors.HexColor("#C8553D")
GREY = colors.HexColor("#6B7280")
GREY_L = colors.HexColor("#F1F3F5")
GREY_M = colors.HexColor("#D5DADF")
INK = colors.HexColor("#1F2933")
WHITE = colors.white

PAGE_W, PAGE_H = A4
LM = RM = 50
TM, BM = 62, 55
FW = PAGE_W - LM - RM  # frame width (~495pt)

# ---------------------------------------------------------------- styles
BODY = ParagraphStyle("body", fontName="Helvetica", fontSize=9.5, leading=13.5,
                      textColor=INK, spaceAfter=6, alignment=TA_LEFT)
SMALL = ParagraphStyle("small", parent=BODY, fontSize=8, leading=10.5,
                       spaceAfter=0)
SMALL_B = ParagraphStyle("smallb", parent=SMALL, fontName="Helvetica-Bold")
CELL_H = ParagraphStyle("cellh", parent=SMALL, fontName="Helvetica-Bold",
                        textColor=WHITE)
H2 = ParagraphStyle("h2", parent=BODY, fontName="Helvetica-Bold", fontSize=11.5,
                    leading=15, textColor=NAVY, spaceBefore=8, spaceAfter=5)
NOTE = ParagraphStyle("note", parent=BODY, fontSize=8.5, leading=12,
                      textColor=GREY, backColor=GREY_L, borderPadding=6,
                      spaceBefore=4, spaceAfter=10)
BULLET = ParagraphStyle("bullet", parent=BODY, leftIndent=14, bulletIndent=3,
                        spaceAfter=3)
TOC_1 = ParagraphStyle("toc1", fontName="Helvetica", fontSize=10.5, leading=20,
                       textColor=INK, leftIndent=0)


def P(text, style=BODY):
    return Paragraph(text, style)


def bullets(items, style=BULLET):
    return [Paragraph(t, style, bulletText="•") for t in items]


# ---------------------------------------------------------------- flowables
class SectionHeader(Flowable):
    """Numbered section title with a rule; also feeds the table of contents."""

    def __init__(self, num, title):
        super().__init__()
        self.num, self.title = num, title
        self.height = 34

    def wrap(self, aw, ah):
        self.width = aw
        return aw, self.height

    def draw(self):
        c = self.canv
        c.setFillColor(TEAL)
        c.roundRect(0, 8, 26, 22, 4, stroke=0, fill=1)
        c.setFillColor(WHITE)
        c.setFont("Helvetica-Bold", 12)
        c.drawCentredString(13, 14.5, str(self.num))
        c.setFillColor(NAVY)
        c.setFont("Helvetica-Bold", 16)
        c.drawString(36, 13, self.title)
        c.setStrokeColor(TEAL_M)
        c.setLineWidth(1)
        c.line(0, 2, self.width, 2)


def section(num, title):
    return [CondPageBreak(160), SectionHeader(num, title), Spacer(1, 8)]


def table(rows, widths, header=True, zebra=True, font_size=8):
    """rows: list of lists of str/Paragraph. Strings are wrapped in Paragraphs."""
    body_style = ParagraphStyle("tb", parent=SMALL, fontSize=font_size,
                                leading=font_size * 1.3)
    data = []
    for r, row in enumerate(rows):
        out = []
        for cell in row:
            if isinstance(cell, str):
                st = CELL_H if (header and r == 0) else body_style
                cell = Paragraph(cell, st)
            out.append(cell)
        data.append(out)
    t = Table(data, colWidths=widths, repeatRows=1 if header else 0)
    style = [
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("LEFTPADDING", (0, 0), (-1, -1), 5),
        ("RIGHTPADDING", (0, 0), (-1, -1), 5),
        ("TOPPADDING", (0, 0), (-1, -1), 4),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ("LINEBELOW", (0, 0), (-1, -1), 0.4, GREY_M),
        ("BOX", (0, 0), (-1, -1), 0.6, GREY_M),
    ]
    if header:
        style += [("BACKGROUND", (0, 0), (-1, 0), NAVY)]
    if zebra:
        for r in range(1 if header else 0, len(data)):
            if r % 2 == 0:
                style.append(("BACKGROUND", (0, r), (-1, r), GREY_L))
    t.setStyle(TableStyle(style))
    return t


# ---------------------------------------------------------------- drawing helpers
def text_lines(d, x, y_top, text, size=7, font="Helvetica", color=INK,
               width=100, anchor="start", lead=1.22):
    lines = simpleSplit(text, font, size, width)
    for i, ln in enumerate(lines):
        d.add(String(x, y_top - size - i * size * lead, ln, fontName=font,
                     fontSize=size, fillColor=color, textAnchor=anchor))
    return len(lines) * size * lead


def box(d, x, y, w, h, text, fill=TEAL, stroke=None, fc=WHITE, size=7,
        font="Helvetica-Bold", r=3, sw=0.8, dash=None):
    rect = Rect(x, y, w, h, rx=r, ry=r, fillColor=fill,
                strokeColor=stroke or fill, strokeWidth=sw)
    if dash:
        rect.strokeDashArray = dash
    d.add(rect)
    if text:
        lines = simpleSplit(text, font, size, w - 6)
        lh = size * 1.18
        base = y + h / 2 + (len(lines) - 1) * lh / 2 - size * 0.34
        for i, ln in enumerate(lines):
            d.add(String(x + w / 2, base - i * lh, ln, fontName=font,
                         fontSize=size, fillColor=fc, textAnchor="middle"))


def arrow_head(d, x1, y1, x2, y2, color, size=5):
    ang = math.atan2(y2 - y1, x2 - x1)
    sp = 0.42
    pts = [x2, y2,
           x2 - size * math.cos(ang - sp), y2 - size * math.sin(ang - sp),
           x2 - size * math.cos(ang + sp), y2 - size * math.sin(ang + sp)]
    d.add(Polygon(pts, fillColor=color, strokeColor=color, strokeWidth=0.4))


def path_arrow(d, pts, color=NAVY, w=0.9, dash=None):
    pl = PolyLine(pts, strokeColor=color, strokeWidth=w)
    if dash:
        pl.strokeDashArray = dash
    d.add(pl)
    arrow_head(d, pts[-4], pts[-3], pts[-2], pts[-1], color)


def badge(d, x, y, n, fill=ORANGE, r=6.5):
    d.add(Circle(x, y, r, fillColor=fill, strokeColor=WHITE, strokeWidth=1))
    d.add(String(x, y - 2.8, str(n), fontName="Helvetica-Bold", fontSize=7.5,
                 fillColor=WHITE, textAnchor="middle"))


def caret(d, x, y, color=GREY, s=2.4):
    d.add(Polygon([x - s, y + s / 2, x + s, y + s / 2, x, y - s / 1.2],
                  fillColor=color, strokeColor=color, strokeWidth=0.2))


# ---------------------------------------------------------------- content data
NAV = [
    ("Home", []),
    ("About Us", ["Who We Are", "Vision, Mission & Values",
                  "Our History (2009 - today)", "Leadership Team",
                  "Quality & Compliance (GDP / GSP)",
                  "Group Companies (optional)"]),
    ("Services", ["Registration & Regulatory Affairs", "Pricing & Market Access",
                  "Scientific Promotion (Medical Reps)",
                  "Warehousing & Cold Chain", "Distribution & Logistics",
                  "Tenders & Public Sector"]),
    ("Partners", ["International Partners (by region)", "Partner Detail Page",
                  "Become a Partner (form)"]),
    ("Products", ["Therapeutic Areas", "Product Listing (search & filter)",
                  "Product Detail Page"]),
    ("Media Center", ["News", "Events & Conferences", "Photo & Video Gallery"]),
    ("Careers", ["Why Join Us", "Open Positions", "Apply Online (form)"]),
    ("Contact Us", ["Head Office & Map", "Branches / Coverage",
                    "Inquiry Form (form)"]),
]


# ---------------------------------------------------------------- diagrams
def header_wireframe():
    W, H = FW, 228
    d = Drawing(W, H)
    # browser chrome
    d.add(Rect(0, H - 18, W, 18, fillColor=GREY_M, strokeColor=GREY_M))
    for i, c in enumerate([RED, ORANGE, TEAL]):
        d.add(Circle(9 + i * 10, H - 9, 3, fillColor=c, strokeColor=c))
    box(d, 50, H - 14.5, 220, 11, "https://alqawsangroup.com", fill=WHITE,
        stroke=WHITE, fc=GREY, size=6.5, font="Helvetica", r=5)
    # utility bar
    uy = H - 40
    d.add(Rect(0, uy, W, 22, fillColor=NAVY, strokeColor=NAVY))
    d.add(String(10, uy + 8, "Tel: +964 7XX XXX XXXX     |     "
                 "info@alqawsangroup.com     |     Sun - Thu  8:30 - 16:00",
                 fontName="Helvetica", fontSize=6.5, fillColor=WHITE))
    for i, s in enumerate(["f", "in", "ig", "x"]):
        cx = 360 + i * 15
        d.add(Circle(cx, uy + 11, 5.5, fillColor=TEAL, strokeColor=TEAL))
        d.add(String(cx, uy + 8.8, s, fontName="Helvetica-Bold", fontSize=5.5,
                     fillColor=WHITE, textAnchor="middle"))
    box(d, 432, uy + 4, 54, 14, "EN  |  AR", fill=WHITE, fc=NAVY, size=6.5, r=7)
    # main bar
    my = uy - 48
    d.add(Rect(0, my, W, 48, fillColor=WHITE, strokeColor=GREY_M, strokeWidth=0.6))
    d.add(Rect(10, my + 9, 30, 30, rx=15, ry=15, fillColor=TEAL, strokeColor=TEAL))
    d.add(String(25, my + 20.5, "AQ", fontName="Helvetica-Bold", fontSize=10,
                 fillColor=WHITE, textAnchor="middle"))
    d.add(String(45, my + 26, "AL-QAWSAN", fontName="Helvetica-Bold",
                 fontSize=8, fillColor=NAVY))
    d.add(String(45, my + 16, "Scientific Bureau", fontName="Helvetica",
                 fontSize=6.5, fillColor=GREY))
    labels = [n for n, _ in NAV]
    fs = 6.2
    x0, x1 = 102, 394
    widths = [stringWidth(l, "Helvetica-Bold", fs) + (6 if NAV[i][1] else 0)
              for i, l in enumerate(labels)]
    gap = (x1 - x0 - sum(widths)) / (len(labels) - 1)
    x = x0
    pos = {}
    for i, l in enumerate(labels):
        col = TEAL if l == "Home" else INK
        d.add(String(x, my + 21, l, fontName="Helvetica-Bold", fontSize=fs,
                     fillColor=col))
        tw = stringWidth(l, "Helvetica-Bold", fs)
        if NAV[i][1]:
            caret(d, x + tw + 3.5, my + 23, s=2)
        if l == "Home":
            d.add(Rect(x, my + 12, tw, 2, fillColor=TEAL, strokeColor=TEAL))
        pos[l] = x
        x += widths[i] + gap
    # search
    d.add(Circle(403, my + 24, 4.5, fillColor=None, strokeColor=INK, strokeWidth=1.1))
    d.add(Line(406, my + 20.5, 410, my + 16.5, strokeColor=INK, strokeWidth=1.3))
    box(d, 416, my + 13, 72, 22, "Become a Partner", fill=ORANGE, size=6.8, r=11)
    # dropdown under Services
    sx = pos["Services"] - 8
    items = NAV[2][1]
    dh = len(items) * 15 + 8
    dy = my - dh
    d.add(Rect(sx + 3, dy - 3, 150, dh, fillColor=GREY_M, strokeColor=GREY_M))
    d.add(Rect(sx, dy, 150, dh, fillColor=WHITE, strokeColor=GREY_M, strokeWidth=0.6))
    d.add(Rect(sx, my - 2, 150, 2, fillColor=TEAL, strokeColor=TEAL))
    for i, it in enumerate(items):
        yy = my - 16 - i * 15
        if i == 0:
            d.add(Rect(sx + 1, yy - 4, 148, 14, fillColor=TEAL_L, strokeColor=TEAL_L))
        d.add(String(sx + 9, yy, it, fontName="Helvetica", fontSize=6.8,
                     fillColor=INK))
    # hero placeholder
    hy = 8
    d.add(Rect(0, hy, W, my - hy - 6, fillColor=TEAL_L, strokeColor=TEAL_L))
    d.add(String(W - 12, hy + 30, "Page content (hero slider) starts here",
                 fontName="Helvetica-Oblique", fontSize=7.5, fillColor=TEAL_D,
                 textAnchor="end"))
    d.add(String(W - 12, hy + 18, "Header stays sticky while scrolling",
                 fontName="Helvetica-Oblique", fontSize=7, fillColor=GREY,
                 textAnchor="end"))
    # re-draw dropdown above hero
    d.add(Rect(sx + 3, dy - 3, 150, dh, fillColor=GREY_M, strokeColor=GREY_M))
    d.add(Rect(sx, dy, 150, dh, fillColor=WHITE, strokeColor=GREY_M, strokeWidth=0.6))
    d.add(Rect(sx, my - 2, 150, 2, fillColor=TEAL, strokeColor=TEAL))
    for i, it in enumerate(items):
        yy = my - 16 - i * 15
        if i == 0:
            d.add(Rect(sx + 1, yy - 4, 148, 14, fillColor=TEAL_L, strokeColor=TEAL_L))
        d.add(String(sx + 9, yy, it, fontName="Helvetica", fontSize=6.8,
                     fillColor=INK))
    # badges
    badge(d, 6, uy + 20, 1)
    badge(d, 8, my + 42, 2)
    badge(d, pos["About Us"] + 20, my + 40, 3)
    badge(d, sx + 150, dy + dh - 10, 4)
    badge(d, 403, my + 40, 5)
    badge(d, 432, uy + 20, 6)
    badge(d, 486, my + 40, 7)
    return d


def mobile_header():
    W, H = 150, 200
    d = Drawing(W, H)
    d.add(Rect(20, 2, 110, H - 4, rx=12, ry=12, fillColor=INK, strokeColor=INK))
    sx, sw = 26, 98
    top = H - 16
    d.add(Rect(sx, 12, sw, top - 12, fillColor=WHITE, strokeColor=WHITE))
    d.add(Rect(sx, top - 12, sw, 12, fillColor=NAVY, strokeColor=NAVY))
    d.add(String(sx + 4, top - 8.5, "Tel  |  Email", fontName="Helvetica",
                 fontSize=5, fillColor=WHITE))
    d.add(String(sx + sw - 4, top - 8.5, "EN | AR", fontName="Helvetica-Bold",
                 fontSize=5, fillColor=WHITE, textAnchor="end"))
    d.add(Rect(sx, top - 34, sw, 22, fillColor=WHITE, strokeColor=GREY_M, strokeWidth=0.5))
    d.add(Circle(sx + 12, top - 23, 7, fillColor=TEAL, strokeColor=TEAL))
    d.add(String(sx + 22, top - 25.5, "AL-QAWSAN", fontName="Helvetica-Bold",
                 fontSize=6, fillColor=NAVY))
    for i in range(3):
        d.add(Rect(sx + sw - 16, top - 19 - i * 4, 10, 1.6, fillColor=INK, strokeColor=INK))
    # drawer
    items = ["Home", "About Us  +", "Services  -", "   Registration", "   Pricing",
             "Partners  +", "Products  +",
             "Media Center  +", "Careers  +", "Contact Us"]
    y = top - 46
    for it in items:
        sub = it.startswith("   ")
        if sub:
            d.add(Rect(sx, y - 3.5, sw, 11, fillColor=TEAL_L, strokeColor=TEAL_L))
        d.add(String(sx + 6, y, it.strip() if not sub else "   " + it.strip(),
                     fontName="Helvetica" if sub else "Helvetica-Bold",
                     fontSize=5.8, fillColor=INK))
        d.add(Line(sx + 3, y - 4, sx + sw - 3, y - 4, strokeColor=GREY_M, strokeWidth=0.3))
        y -= 10
    box(d, sx + 8, 20, sw - 16, 14, "Become a Partner", fill=ORANGE, size=6, r=7)
    return d


def sitemap():
    W = FW
    row, gap_g = 15, 9
    total = sum(max(len(c), 1) for _, c in NAV[1:]) * row + (len(NAV) - 2) * gap_g
    H = total + 40
    d = Drawing(W, H)
    home_x, home_w = 0, 62
    tab_x, tab_w = 96, 92
    ch_x, ch_w = 222, 200
    y = H - 10
    tab_centres = []
    for name, kids in NAV[1:]:
        n = max(len(kids), 1)
        grp_top = y
        kid_centres = []
        for k in kids:
            cy = y - row / 2
            is_form = "(form)" in k
            box(d, ch_x, cy - 5.5, ch_w, 11, k.replace(" (form)", ""),
                fill=TEAL_L if not is_form else colors.HexColor("#FFF4E3"),
                stroke=TEAL_M if not is_form else ORANGE, fc=INK, size=6.8,
                font="Helvetica", r=2, sw=0.6)
            if is_form:
                d.add(String(ch_x + ch_w - 4, cy - 2.3, "FORM", fontName="Helvetica-Bold",
                             fontSize=5.5, fillColor=ORANGE, textAnchor="end"))
            kid_centres.append(cy)
            y -= row
        tc = (grp_top + y) / 2
        box(d, tab_x, tc - 8, tab_w, 16, name, fill=TEAL, size=7.5)
        tab_centres.append(tc)
        # tab -> children connectors
        spine = ch_x - 14
        d.add(Line(tab_x + tab_w, tc, spine, tc, strokeColor=TEAL_M, strokeWidth=0.8))
        d.add(Line(spine, kid_centres[0], spine, kid_centres[-1],
                   strokeColor=TEAL_M, strokeWidth=0.8))
        for kc in kid_centres:
            d.add(Line(spine, kc, ch_x, kc, strokeColor=TEAL_M, strokeWidth=0.8))
        y -= gap_g
    hc = (tab_centres[0] + tab_centres[-1]) / 2
    box(d, home_x, hc - 14, home_w, 28, "HOME", fill=NAVY, size=9)
    sp = tab_x - 16
    d.add(Line(home_x + home_w, hc, sp, hc, strokeColor=NAVY, strokeWidth=1))
    d.add(Line(sp, tab_centres[0], sp, tab_centres[-1], strokeColor=NAVY, strokeWidth=1))
    for tc in tab_centres:
        path_arrow(d, [sp, tc, tab_x, tc], color=NAVY)
    # legend
    lx = ch_x + ch_w + 12
    ly = H - 20
    d.add(String(lx, ly, "Legend", fontName="Helvetica-Bold", fontSize=7.5, fillColor=NAVY))
    for i, (lab, f, s) in enumerate([("Home page", NAVY, NAVY), ("Main tab", TEAL, TEAL),
                                     ("Sub-page", TEAL_L, TEAL_M),
                                     ("Form page", colors.HexColor("#FFF4E3"), ORANGE)]):
        yy = ly - 16 - i * 14
        d.add(Rect(lx, yy, 12, 9, fillColor=f, strokeColor=s, strokeWidth=0.6))
        d.add(String(lx + 16, yy + 2, lab, fontName="Helvetica", fontSize=6.8, fillColor=INK))
    d.add(String(lx, ly - 84, "Global pages", fontName="Helvetica-Bold", fontSize=7.5,
                 fillColor=NAVY))
    for i, g in enumerate(["Search results", "Privacy Policy", "Terms of Use",
                           "404 page", "Thank-you pages"]):
        d.add(String(lx, ly - 98 - i * 11, "- " + g, fontName="Helvetica", fontSize=6.8,
                     fillColor=INK))
    return d


HOME_SECTIONS = [
    ("Header", 26, "Sticky header: utility bar, logo, 8 tabs, search, CTA."),
    ("Hero slider", 72, "3 - 4 slides: company promise, key service, partner call, news."),
    ("Key numbers", 34, "Animated counters: years, partners, products, customers, coverage."),
    ("About teaser", 46, "Short intro + photo + 'Read more' to About Us."),
    ("Services grid", 60, "6 service cards linking to each Services sub-page."),
    ("Partners carousel", 30, "Scrolling logos of international partners."),
    ("Therapeutic areas", 36, "Tiles that open the filtered product listing."),
    ("Coverage map", 50, "Iraq map with governorates / branches served."),
    ("Latest news", 50, "3 latest articles from the Media Center."),
    ("Call-to-action band", 26, "'Become a Partner' + 'Contact Us' buttons."),
    ("Footer", 44, "4 columns + copyright bar (see section 8)."),
]


def homepage_wireframe():
    W = FW
    gap = 4
    H = sum(h for _, h, _ in HOME_SECTIONS) + gap * (len(HOME_SECTIONS) - 1) + 10
    d = Drawing(W, H)
    mx, mw = 0, 290
    y = H - 5
    for i, (name, h, desc) in enumerate(HOME_SECTIONS):
        yb = y - h
        cx = mx + mw / 2
        if name == "Header":
            d.add(Rect(mx, yb, mw, h, fillColor=WHITE, strokeColor=GREY_M))
            d.add(Rect(mx, yb + h - 7, mw, 7, fillColor=NAVY, strokeColor=NAVY))
            d.add(Circle(mx + 12, yb + 9, 6, fillColor=TEAL, strokeColor=TEAL))
            for k in range(8):
                d.add(Rect(mx + 60 + k * 23, yb + 8, 17, 2.5, fillColor=GREY, strokeColor=GREY))
            d.add(Rect(mx + mw - 42, yb + 4, 36, 10, rx=5, ry=5, fillColor=ORANGE,
                       strokeColor=ORANGE))
        elif name == "Hero slider":
            d.add(Rect(mx, yb, mw, h, fillColor=TEAL_D, strokeColor=TEAL_D))
            d.add(Rect(mx + 20, yb + h - 26, 150, 8, fillColor=WHITE, strokeColor=WHITE))
            d.add(Rect(mx + 20, yb + h - 38, 110, 5, fillColor=TEAL_M, strokeColor=TEAL_M))
            d.add(Rect(mx + 20, yb + 14, 50, 12, rx=6, ry=6, fillColor=ORANGE,
                       strokeColor=ORANGE))
            for k in range(4):
                d.add(Circle(cx - 12 + k * 8, yb + 6, 2, fillColor=WHITE if k == 0 else TEAL_M,
                             strokeColor=WHITE))
            d.add(String(mx + mw - 8, yb + 8, "< >", fontName="Helvetica-Bold", fontSize=8,
                         fillColor=WHITE, textAnchor="end"))
        elif name == "Key numbers":
            d.add(Rect(mx, yb, mw, h, fillColor=GREY_L, strokeColor=GREY_L))
            vals = [("17+", "Years"), ("XX", "Partners"), ("XXX", "Products"),
                    ("X,XXX", "Customers"), ("XX", "Governorates")]
            for k, (v, l) in enumerate(vals):
                bx = mx + 8 + k * 56
                d.add(String(bx + 24, yb + 17, v, fontName="Helvetica-Bold", fontSize=9,
                             fillColor=TEAL, textAnchor="middle"))
                d.add(String(bx + 24, yb + 7, l, fontName="Helvetica", fontSize=5.5,
                             fillColor=GREY, textAnchor="middle"))
        elif name == "About teaser":
            d.add(Rect(mx, yb, mw, h, fillColor=WHITE, strokeColor=GREY_M))
            d.add(Rect(mx + 8, yb + 6, 90, h - 12, fillColor=GREY_M, strokeColor=GREY_M))
            d.add(Line(mx + 8, yb + 6, mx + 98, yb + h - 6, strokeColor=WHITE))
            d.add(Line(mx + 8, yb + h - 6, mx + 98, yb + 6, strokeColor=WHITE))
            for k in range(4):
                d.add(Rect(mx + 110, yb + h - 12 - k * 7, 160 - k * 20, 3,
                           fillColor=GREY, strokeColor=GREY))
        elif name == "Services grid":
            d.add(Rect(mx, yb, mw, h, fillColor=WHITE, strokeColor=GREY_M))
            for r_ in range(2):
                for c_ in range(3):
                    bx = mx + 10 + c_ * 92
                    by = yb + 6 + (1 - r_) * 26
                    d.add(Rect(bx, by, 84, 22, fillColor=TEAL_L, strokeColor=TEAL_M,
                               strokeWidth=0.5))
                    d.add(Circle(bx + 10, by + 11, 5, fillColor=TEAL, strokeColor=TEAL))
                    d.add(Rect(bx + 20, by + 10, 50, 3, fillColor=GREY, strokeColor=GREY))
        elif name == "Partners carousel":
            d.add(Rect(mx, yb, mw, h, fillColor=GREY_L, strokeColor=GREY_L))
            for k in range(6):
                d.add(Rect(mx + 12 + k * 45, yb + 8, 38, 14, fillColor=WHITE,
                           strokeColor=GREY_M))
        elif name == "Therapeutic areas":
            d.add(Rect(mx, yb, mw, h, fillColor=WHITE, strokeColor=GREY_M))
            for k, a in enumerate(["Cardio", "Diabetes", "Oncology", "Antibiotics", "Derma"]):
                box(d, mx + 8 + k * 56, yb + 10, 50, 16, a, fill=TEAL, size=5.8, r=8)
        elif name == "Coverage map":
            d.add(Rect(mx, yb, mw, h, fillColor=TEAL_L, strokeColor=TEAL_L))
            shape = [cx - 30, yb + 8, cx + 10, yb + 6, cx + 36, yb + 18, cx + 30, yb + 40,
                     cx + 6, yb + 45, cx - 22, yb + 38, cx - 36, yb + 22]
            d.add(Polygon(shape, fillColor=TEAL_M, strokeColor=TEAL, strokeWidth=0.6))
            for px, py in [(-10, 30), (5, 20), (15, 34), (-20, 18), (20, 14)]:
                d.add(Circle(cx + px, yb + py, 2.2, fillColor=ORANGE, strokeColor=WHITE,
                             strokeWidth=0.4))
        elif name == "Latest news":
            d.add(Rect(mx, yb, mw, h, fillColor=WHITE, strokeColor=GREY_M))
            for k in range(3):
                bx = mx + 10 + k * 92
                d.add(Rect(bx, yb + 16, 84, 28, fillColor=GREY_M, strokeColor=GREY_M))
                d.add(Rect(bx, yb + 9, 70, 3, fillColor=GREY, strokeColor=GREY))
                d.add(Rect(bx, yb + 4, 50, 2.5, fillColor=GREY_M, strokeColor=GREY_M))
        elif name == "Call-to-action band":
            d.add(Rect(mx, yb, mw, h, fillColor=ORANGE, strokeColor=ORANGE))
            d.add(Rect(mx + 14, yb + 11, 120, 5, fillColor=WHITE, strokeColor=WHITE))
            d.add(Rect(mx + mw - 70, yb + 7, 56, 12, rx=6, ry=6, fillColor=NAVY,
                       strokeColor=NAVY))
        elif name == "Footer":
            d.add(Rect(mx, yb, mw, h, fillColor=NAVY, strokeColor=NAVY))
            d.add(Rect(mx, yb, mw, 8, fillColor=colors.HexColor("#0C2440"),
                       strokeColor=colors.HexColor("#0C2440")))
            for c_ in range(4):
                for k in range(4):
                    d.add(Rect(mx + 12 + c_ * 70, yb + h - 10 - k * 7, 50 - k * 5, 2.5,
                               fillColor=TEAL_M, strokeColor=TEAL_M))
        # label on the right
        badge(d, mx + mw + 16, y - h / 2 + 3, i + 1, fill=TEAL)
        d.add(String(mx + mw + 28, y - h / 2 + 3.5, name, fontName="Helvetica-Bold",
                     fontSize=8, fillColor=NAVY))
        text_lines(d, mx + mw + 28, y - h / 2 + 1, desc, size=6.6, color=GREY,
                   width=W - mw - 30)
        d.add(Line(mx + mw + 2, y - h / 2 + 3, mx + mw + 9, y - h / 2 + 3,
                   strokeColor=TEAL_M, strokeWidth=0.6))
        y = yb - gap
    return d


JOURNEYS = [
    ("International manufacturer", "wants a distribution partner",
     ["Home (hero CTA)", "Services", "Partners (success)", "Become a Partner form",
      "BD team follow-up"]),
    ("Doctor / pharmacist", "needs product information",
     ["Home", "Therapeutic Areas", "Product Listing", "Product Detail",
      "Medical inquiry"]),
    ("Hospital / public sector", "buyer or tender officer",
     ["Home", "Services", "Tenders & Public Sector", "Contact Us form",
      "Sales team reply"]),
    ("Job seeker", "looking for a role",
     ["Home", "Careers", "Open Positions", "Apply Online form", "HR screening"]),
]


def journeys_diagram():
    W = FW
    lane_h, lab_w = 50, 100
    H = lane_h * len(JOURNEYS) + 6
    d = Drawing(W, H)
    n = 5
    g = 11
    bw = (W - lab_w - 8 - g * (n - 1)) / n
    for li, (who, why, steps) in enumerate(JOURNEYS):
        y0 = H - (li + 1) * lane_h
        d.add(Rect(0, y0 + 3, W, lane_h - 6, fillColor=GREY_L if li % 2 == 0 else WHITE,
                   strokeColor=GREY_M, strokeWidth=0.4))
        d.add(Rect(0, y0 + 3, lab_w, lane_h - 6, fillColor=NAVY, strokeColor=NAVY))
        text_lines(d, 7, y0 + lane_h - 9, who, size=7.2, font="Helvetica-Bold",
                   color=WHITE, width=lab_w - 12)
        text_lines(d, 7, y0 + 21, why, size=6, color=TEAL_M, width=lab_w - 12)
        cy = y0 + lane_h / 2
        for si, st in enumerate(steps):
            bx = lab_w + 8 + si * (bw + g)
            last = si == n - 1
            form = "form" in st
            fill = TEAL if si < n - 1 else colors.HexColor("#FFF4E3")
            fc = WHITE if si < n - 1 else INK
            if form:
                fill, fc = ORANGE, WHITE
            box(d, bx, cy - 13, bw, 26, st, fill=fill, fc=fc,
                stroke=ORANGE if last else None, size=6.4, dash=[2, 1.5] if last else None)
            if si:
                path_arrow(d, [bx - g + 1, cy, bx - 1, cy], color=NAVY)
    return d


def footer_wireframe():
    W, H = FW, 170
    d = Drawing(W, H)
    d.add(Rect(0, 22, W, H - 22, fillColor=NAVY, strokeColor=NAVY))
    d.add(Rect(0, 0, W, 22, fillColor=colors.HexColor("#0C2440"),
               strokeColor=colors.HexColor("#0C2440")))
    d.add(String(12, 8, "(c) 2026 Al-Qawsan Scientific Bureau. All rights reserved.",
                 fontName="Helvetica", fontSize=6.5, fillColor=TEAL_M))
    d.add(String(W - 12, 8, "Privacy Policy   |   Terms of Use   |   Sitemap",
                 fontName="Helvetica", fontSize=6.5, fillColor=TEAL_M, textAnchor="end"))
    cw = (W - 24) / 4
    cols = [
        ("AL-QAWSAN", ["Pharmaceutical distribution and", "scientific promotion across",
                       "Iraq since 2009.", "", "Newsletter: [ email ]  [Subscribe]"]),
        ("Quick Links", ["About Us", "Partners", "Products", "Media Center", "Careers",
                         "Contact Us"]),
        ("Our Services", ["Registration & Regulatory", "Pricing & Market Access",
                          "Scientific Promotion", "Warehousing & Cold Chain",
                          "Distribution & Logistics", "Tenders & Public Sector"]),
        ("Contact Us", ["Head Office: Baghdad, Iraq", "Tel: +964 7XX XXX XXXX",
                        "info@alqawsangroup.com", "Sun - Thu  8:30 - 16:00"]),
    ]
    for i, (title, lines) in enumerate(cols):
        x = 12 + i * cw
        d.add(String(x, H - 20, title, fontName="Helvetica-Bold", fontSize=8,
                     fillColor=WHITE))
        d.add(Rect(x, H - 26, 22, 1.8, fillColor=ORANGE, strokeColor=ORANGE))
        for k, ln in enumerate(lines):
            d.add(String(x, H - 40 - k * 12, ln, fontName="Helvetica", fontSize=6.6,
                         fillColor=GREY_M))
    for k, s in enumerate(["f", "in", "ig", "x", "yt"]):
        cx = 18 + k * 16
        d.add(Circle(cx, 42, 6, fillColor=TEAL, strokeColor=TEAL))
        d.add(String(cx, 39.8, s, fontName="Helvetica-Bold", fontSize=5.5,
                     fillColor=WHITE, textAnchor="middle"))
    mx = 12 + 3 * cw
    d.add(Rect(mx, 32, cw - 14, 44, fillColor=TEAL_D, strokeColor=TEAL_M, strokeWidth=0.5))
    d.add(String(mx + (cw - 14) / 2, 51, "Google Map (HQ)", fontName="Helvetica-Oblique",
                 fontSize=6.5, fillColor=WHITE, textAnchor="middle"))
    return d


WF_LANES = ["Marketing", "Content Writer", "Medical / Regulatory", "Web Admin",
            "Management"]
WF_STEPS = [
    (0, "Content request & brief"),
    (1, "Draft EN + AR text, collect images"),
    (2, "Medical & regulatory review"),
    (3, "Build page in CMS (staging)"),
    (4, "Final approval"),
    (3, "Publish live + SEO check"),
    (0, "Promote & monitor analytics"),
]


def workflow_diagram():
    W = FW
    lane_h, lab_w, head = 46, 80, 16
    H = lane_h * len(WF_LANES) + head + 4
    d = Drawing(W, H)
    n = len(WF_STEPS)
    colw = (W - lab_w) / n
    bw, bh = colw - 12, 32
    for i, ln in enumerate(WF_LANES):
        y0 = H - head - (i + 1) * lane_h
        d.add(Rect(0, y0, W, lane_h, fillColor=GREY_L if i % 2 == 0 else WHITE,
                   strokeColor=GREY_M, strokeWidth=0.4))
        d.add(Rect(0, y0, lab_w, lane_h, fillColor=NAVY, strokeColor=WHITE, strokeWidth=0.6))
        text_lines(d, 6, y0 + lane_h / 2 + 7, ln, size=7.2, font="Helvetica-Bold",
                   color=WHITE, width=lab_w - 10)
    for s in range(n):
        d.add(String(lab_w + colw * s + colw / 2, H - 11, "Step %d" % (s + 1),
                     fontName="Helvetica-Bold", fontSize=6.5, fillColor=TEAL,
                     textAnchor="middle"))

    def centre(s):
        lane, _ = WF_STEPS[s]
        x = lab_w + colw * s + 6
        y = H - head - (lane + 1) * lane_h + (lane_h - bh) / 2
        return x, y

    for s, (lane, txt) in enumerate(WF_STEPS):
        x, y = centre(s)
        fill = ORANGE if s in (2, 4) else TEAL
        box(d, x, y, bw, bh, txt, fill=fill, size=6.2)
        badge(d, x + 2, y + bh - 1, s + 1, fill=NAVY, r=5.5)
    for s in range(n - 1):
        x1, y1 = centre(s)
        x2, y2 = centre(s + 1)
        a = (x1 + bw, y1 + bh / 2)
        b = (x2, y2 + bh / 2)
        if abs(a[1] - b[1]) < 1:
            path_arrow(d, [a[0], a[1], b[0] - 1, b[1]])
        else:
            gx = a[0] + 6
            path_arrow(d, [a[0], a[1], gx, a[1], gx, b[1], b[0] - 1, b[1]])
    # revision loops (dashed red)
    for frm, to, label in [(2, 1, "revise"), (4, 3, "revise")]:
        fx, fy = centre(frm)
        tx, ty = centre(to)
        ly = ty + bh / 2 + (9 if fy < ty else -9)
        path_arrow(d, [fx + bw / 2, fy + (bh if fy < ty else 0), fx + bw / 2, ly,
                       tx + bw + 1, ly], color=RED, dash=[2, 1.5])
        d.add(String((fx + bw / 2 + tx + bw) / 2, ly + 2.5, label,
                     fontName="Helvetica-Oblique", fontSize=5.8, fillColor=RED,
                     textAnchor="middle"))
    return d


PHASES = [
    ("Discovery & content audit", 1, 2),
    ("Sitemap & wireframes", 2, 3),
    ("UI design (desktop + mobile)", 3, 5),
    ("Content writing & translation", 3, 7),
    ("Development & CMS setup", 5, 9),
    ("Content upload & SEO", 8, 10),
    ("Testing (QA & client UAT)", 10, 11),
    ("Launch & staff training", 12, 12),
]
MILESTONES = [(3, "Sitemap sign-off"), (5, "Design sign-off"), (12, "Go-live")]


def gantt():
    W = FW
    lab_w, weeks, rh, head = 150, 12, 20, 18
    H = head + rh * len(PHASES) + 40
    d = Drawing(W, H)
    ww = (W - lab_w) / weeks
    for w in range(weeks):
        x = lab_w + w * ww
        d.add(Rect(x, 36, ww, H - head - 36, fillColor=GREY_L if w % 2 == 0 else WHITE,
                   strokeColor=GREY_M, strokeWidth=0.3))
        d.add(String(x + ww / 2, H - 12, "W%d" % (w + 1), fontName="Helvetica-Bold",
                     fontSize=7, fillColor=NAVY, textAnchor="middle"))
    for i, (name, s, e) in enumerate(PHASES):
        y = H - head - (i + 1) * rh
        d.add(String(4, y + 7, "%d. %s" % (i + 1, name), fontName="Helvetica",
                     fontSize=7.3, fillColor=INK))
        box(d, lab_w + (s - 1) * ww + 2, y + 4, (e - s + 1) * ww - 4, rh - 8, "",
            fill=TEAL if i % 2 == 0 else TEAL_D, r=5)
    for k, (wk, lab) in enumerate(MILESTONES):
        x = lab_w + wk * ww - ww / 2
        d.add(Polygon([x, 32, x + 5, 26, x, 20, x - 5, 26], fillColor=ORANGE,
                      strokeColor=ORANGE))
        d.add(String(x, 10 if k % 2 == 0 else 2, lab, fontName="Helvetica-Bold",
                     fontSize=6.5, fillColor=INK, textAnchor="middle"))
    return d


# ---------------------------------------------------------------- page decoration
def draw_cover(c, doc):
    c.saveState()
    c.setFillColor(NAVY)
    c.rect(0, PAGE_H * 0.38, PAGE_W, PAGE_H * 0.62, stroke=0, fill=1)
    c.setFillColor(TEAL)
    p = c.beginPath()
    p.moveTo(0, PAGE_H * 0.38)
    p.lineTo(PAGE_W, PAGE_H * 0.38)
    p.lineTo(PAGE_W, PAGE_H * 0.47)
    p.close()
    c.drawPath(p, stroke=0, fill=1)
    # network motif (pages connected)
    import random
    random.seed(7)
    nodes = [(PAGE_W * 0.66 + random.random() * 160, PAGE_H * 0.70 + random.random() * 200)
             for _ in range(14)]
    c.setStrokeColor(colors.HexColor("#2A5A84"))
    c.setLineWidth(0.7)
    for i, a in enumerate(nodes):
        for b in nodes[i + 1:i + 3]:
            c.line(a[0], a[1], b[0], b[1])
    for i, (x, y) in enumerate(nodes):
        c.setFillColor(ORANGE if i % 5 == 0 else colors.HexColor("#3F7FA8"))
        c.circle(x, y, 3.5 if i % 5 else 5, stroke=0, fill=1)
    c.setFillColor(ORANGE)
    c.rect(LM, PAGE_H - 150, 50, 4, stroke=0, fill=1)
    c.setFillColor(TEAL_M)
    c.setFont("Helvetica-Bold", 11)
    c.drawString(LM, PAGE_H - 130, "WEBSITE PROPOSAL  |  DRAFT v1.0")
    c.setFillColor(WHITE)
    c.setFont("Helvetica-Bold", 30)
    c.drawString(LM, PAGE_H - 200, "Website Structure")
    c.drawString(LM, PAGE_H - 236, "& Content Workflow")
    c.setFont("Helvetica", 15)
    c.setFillColor(TEAL_M)
    c.drawString(LM, PAGE_H - 270, "Al-Qawsan Scientific Bureau")
    c.setFont("Helvetica", 10.5)
    c.setFillColor(WHITE)
    c.drawString(LM, PAGE_H - 292,
                 "Header, navigation tabs, sitemap, page connections and publishing workflow")
    # lower info block
    y = PAGE_H * 0.38 - 60
    info = [("Prepared for", "Al-Qawsan Scientific Bureau, Baghdad - Iraq"),
            ("Current website", "alqawsangroup.com"),
            ("Reference model", "alsumo.com.iq (Al Sumo Scientific Bureau)"),
            ("Date", date(2026, 9, 24).strftime("%d %B %Y")),
            ("Status", "Draft for discussion")]
    for k, v in info:
        c.setFont("Helvetica-Bold", 9)
        c.setFillColor(TEAL)
        c.drawString(LM, y, k.upper())
        c.setFont("Helvetica", 11)
        c.setFillColor(INK)
        c.drawString(LM + 130, y, v)
        c.setStrokeColor(GREY_M)
        c.setLineWidth(0.5)
        c.line(LM, y - 9, PAGE_W - RM, y - 9)
        y -= 30
    c.setFont("Helvetica", 7.5)
    c.setFillColor(GREY)
    c.drawString(LM, 40, "Figures marked [TBC] / XX are placeholders to be confirmed "
                 "by Al-Qawsan during the Discovery phase.")
    c.restoreState()


def draw_page(c, doc):
    c.saveState()
    c.setStrokeColor(TEAL)
    c.setLineWidth(1.2)
    c.line(LM, PAGE_H - 40, PAGE_W - RM, PAGE_H - 40)
    c.setFont("Helvetica-Bold", 8)
    c.setFillColor(NAVY)
    c.drawString(LM, PAGE_H - 34, "AL-QAWSAN SCIENTIFIC BUREAU")
    c.setFont("Helvetica", 8)
    c.setFillColor(GREY)
    c.drawRightString(PAGE_W - RM, PAGE_H - 34, "Website Structure & Content Workflow Proposal")
    c.setStrokeColor(GREY_M)
    c.setLineWidth(0.5)
    c.line(LM, 38, PAGE_W - RM, 38)
    c.drawString(LM, 26, "Draft v1.0  -  prepared for Al-Qawsan Scientific Bureau")
    c.setFillColor(NAVY)
    c.setFont("Helvetica-Bold", 8)
    c.drawRightString(PAGE_W - RM, 26, "Page %d" % doc.page)
    c.restoreState()


class ProposalDoc(BaseDocTemplate):
    def __init__(self, path):
        super().__init__(path, pagesize=A4, leftMargin=LM, rightMargin=RM,
                         topMargin=TM, bottomMargin=BM,
                         title="Al-Qawsan Scientific Bureau - Website Structure Proposal",
                         author="Website proposal", subject="Website structure, "
                         "header navigation, sitemap and content workflow")
        frame = Frame(LM, BM, FW, PAGE_H - TM - BM, id="f", leftPadding=0,
                      rightPadding=0, topPadding=0, bottomPadding=0)
        self.addPageTemplates([
            PageTemplate(id="cover", frames=[frame], onPage=draw_cover),
            PageTemplate(id="body", frames=[frame], onPage=draw_page),
        ])

    def afterFlowable(self, flowable):
        if isinstance(flowable, SectionHeader):
            self.notify("TOCEntry", (0, "%d.  %s" % (flowable.num, flowable.title),
                                     self.page))


# ---------------------------------------------------------------- story
def build():
    s = []
    s += [NextPageTemplate("body"), PageBreak()]

    # contents
    s.append(P("Contents", ParagraphStyle("ct", parent=H2, fontSize=18, leading=24,
                                          spaceAfter=14)))
    toc = TableOfContents()
    toc.levelStyles = [TOC_1]
    toc.dotsMinLevel = 0
    s += [toc, PageBreak()]

    # 1 executive summary
    s += section(1, "Executive Summary")
    s.append(P(
        "Al-Qawsan Scientific Bureau was established in Baghdad in 2009 and has grown into "
        "one of the leading private-sector pharmaceutical distributors in Iraq, serving "
        "healthcare professionals, pharmacies and the public sector through partnerships "
        "with international manufacturers. The new website must present that position "
        "with the same clarity and credibility as the best sites in the Iraqi scientific "
        "bureau market."))
    s.append(P(
        "This proposal defines the <b>structure</b> of the new website: the header and its "
        "navigation tabs, every page and sub-page, how pages <b>connect</b> to each other, "
        "and the <b>workflow</b> that turns content into published pages. It follows the "
        "proven pattern of <b>alsumo.com.iq</b> (Al Sumo Scientific Bureau): a clear "
        "corporate header, headline numbers, a full-lifecycle services story, partners "
        "grouped by region and a strong contact / partnership call to action."))
    s.append(P("Objectives of the new website", H2))
    s += bullets([
        "<b>Win partners:</b> convince international manufacturers that Al-Qawsan is the "
        "right distribution and market-access partner in Iraq.",
        "<b>Inform healthcare professionals:</b> easy access to the product portfolio by "
        "therapeutic area.",
        "<b>Serve the public sector:</b> a clear path for hospitals and tender officers.",
        "<b>Recruit talent:</b> a careers section with online applications.",
        "<b>Be bilingual-ready:</b> English and Arabic (right-to-left) from day one, with a "
        "language switch in the header.",
        "<b>Be easy to maintain:</b> a CMS and a defined content workflow so the site stays "
        "current.",
    ])
    s.append(P("What this proposal covers", H2))
    s.append(table([
        ["#", "Deliverable", "Where"],
        ["1", "Reference analysis of alsumo.com.iq", "Section 2"],
        ["2", "Header design: utility bar, logo, tabs, dropdowns, CTA, mobile", "Section 3"],
        ["3", "Navigation tabs and dropdown menu items", "Section 4"],
        ["4", "Full sitemap (page tree)", "Section 5"],
        ["5", "Homepage section order and page-by-page content", "Sections 6 - 7"],
        ["6", "How the pages connect: user journeys and linking rules", "Section 8"],
        ["7", "Footer structure", "Section 9"],
        ["8", "Content workflow, roles (RACI) and update schedule", "Section 10"],
        ["9", "Technical recommendations, timeline and next steps", "Sections 11 - 13"],
    ], [24, 330, FW - 354]))

    # 2 reference analysis
    s += section(2, "Reference Model: alsumo.com.iq")
    s.append(P(
        "Al Sumo Scientific Bureau (founded 1997, Baghdad) runs one of the most complete "
        "websites among Iraqi scientific bureaus. The table below lists the patterns that "
        "make it effective and how each one is adapted for Al-Qawsan. We copy the "
        "<b>structure</b>, not the content: all Al-Qawsan text, figures and images will be "
        "original."))
    s.append(table([
        ["Pattern on alsumo.com.iq", "Why it works", "Adoption for Al-Qawsan"],
        ["Bilingual site (English / Arabic) with a language switch",
         "Serves both international partners and local customers",
         "EN / AR switch in the utility bar; full RTL layout for Arabic"],
        ["Headline numbers (e.g. 5,600+ customers, 18 governorates, 400+ products)",
         "Instant proof of scale and reach",
         "'Key numbers' counter strip on the homepage (years since 2009, partners, "
         "products, customers, coverage) [TBC]"],
        ["Services told as the full market-entry lifecycle: registration, pricing, "
         "scientific promotion, warehousing, cold chain, delivery",
         "Speaks directly to what a manufacturer needs",
         "Services tab with one sub-page per lifecycle stage + Tenders & Public Sector"],
        ["Partners page grouped by region (Europe, Middle East)",
         "Shows international credibility at a glance",
         "Partners tab: logos grouped by region, each with a partner detail page"],
        ["About page built on experience ('25+ years')",
         "Trust through history",
         "About Us: history timeline from 2009, vision / mission, leadership, quality"],
        ["Group / sister companies (Amman, London, Erbil)",
         "Shows regional reach",
         "Optional 'Group Companies' page under About Us, if applicable"],
        ["Contact page with HQ address, map and working hours",
         "Easy to reach, looks established",
         "Contact Us: HQ + map, branches / coverage, inquiry form, working hours"],
    ], [150, 130, FW - 280]))
    s.append(P(
        "Reference points are based on publicly available information about "
        "alsumo.com.iq and alqawsangroup.com. They will be validated with the Al-Qawsan "
        "team during the Discovery phase.", NOTE))

    # 3 header
    s += section(3, "Header Design")
    s.append(P(
        "The header appears on every page and is the main way visitors move around the "
        "site. It has two rows, a slim <b>utility bar</b> and a <b>main navigation bar</b>, "
        "and stays fixed (sticky) at the top while the visitor scrolls. The numbered markers "
        "are explained in the table below the wireframe."))
    s.append(header_wireframe())
    s.append(Spacer(1, 8))
    s.append(table([
        ["#", "Element", "Specification"],
        ["1", "Utility bar", "Navy strip: phone, e-mail, working hours (left); social "
         "icons and language switch (right). Hidden on scroll to save space."],
        ["2", "Logo", "Al-Qawsan logo, links to Home. Left in English, right in Arabic (RTL)."],
        ["3", "Main tabs", "8 tabs: Home, About Us, Services, Partners, Products, Media "
         "Center, Careers, Contact Us. The active tab is underlined in teal."],
        ["4", "Dropdown menu", "Opens on hover (desktop) or tap (mobile) and lists the "
         "sub-pages of the tab (see Section 4)."],
        ["5", "Search", "Site-wide search for products, news and pages."],
        ["6", "Language switch", "EN | AR. Keeps the visitor on the same page in the "
         "other language."],
        ["7", "Call to action", "'Become a Partner' button (orange) opens the partner "
         "form. It is the main business goal of the site."],
    ], [24, 90, FW - 114]))
    s.append(P("Mobile header", H2))
    mob_text = [P("On phones the tabs collapse into a <b>hamburger menu</b>:", SMALL),
                Spacer(1, 4)] + bullets([
        "Utility bar reduced to phone, e-mail and EN | AR.",
        "Logo left, hamburger icon right.",
        "Menu opens as a full-height drawer; tabs with sub-pages expand like an "
        "accordion (+ / -).",
        "'Become a Partner' stays visible at the bottom of the drawer.",
        "Tap targets of at least 44 px; the menu closes on selection.",
    ], ParagraphStyle("mb", parent=BULLET, fontSize=8.5, leading=12))
    t = Table([[mobile_header(), mob_text]], colWidths=[160, FW - 160])
    t.setStyle(TableStyle([("VALIGN", (0, 0), (-1, -1), "TOP"),
                           ("LEFTPADDING", (0, 0), (-1, -1), 0)]))
    s.append(KeepTogether([t]))

    # 4 navigation
    s += section(4, "Navigation Tabs & Dropdown Menus")
    s.append(P(
        "The main navigation has <b>8 tabs</b>, the same depth as the reference site. "
        "Each dropdown has no more than six items so the menu stays easy to scan. Only "
        "Home is a single page without a dropdown."))
    nav_rows = [["Tab", "Dropdown items (sub-pages)", "Purpose / audience"]]
    purposes = {
        "Home": "Overview of the company; entry point for all audiences.",
        "About Us": "Build trust: who Al-Qawsan is, its history since 2009, leadership "
                    "and quality standards.",
        "Services": "Main sales story for manufacturers: the full market-entry lifecycle "
                    "in Iraq.",
        "Partners": "Show international partners and collect new partnership requests.",
        "Products": "Portfolio for doctors, pharmacists and buyers, organised by "
                    "therapeutic area.",
        "Media Center": "News, events and gallery. Keeps the site active and improves SEO.",
        "Careers": "Employer brand and online recruitment.",
        "Contact Us": "All ways to reach the company, plus an inquiry form.",
    }
    for name, kids in NAV:
        nav_rows.append(["<b>%s</b>" % name,
                         "<br/>".join("- " + k for k in kids) if kids else
                         "<i>No dropdown (single page)</i>",
                         purposes[name]])
    s.append(table(nav_rows, [80, 200, FW - 280]))
    s.append(P(
        "<b>Header order rule:</b> tabs are ordered by importance to the business goal: "
        "credibility (About), offer (Services), proof (Partners, Products), activity "
        "(Media), people (Careers), action (Contact). The 'Become a Partner' button "
        "comes last.", NOTE))

    # 5 sitemap
    s += [PageBreak()] + section(5, "Sitemap (Page Tree)")
    s.append(P(
        "The full site has <b>1 home page, 7 main sections and 27 sub-pages</b>, plus "
        "global utility pages. Every page is at most <b>two clicks</b> from Home."))
    s.append(sitemap())

    # 6 homepage
    s += [PageBreak()] + section(6, "Homepage Structure")
    s.append(P(
        "The homepage follows the reference site's pattern: first prove scale, then "
        "explain the offer, then show proof, then ask for action. Sections from top to "
        "bottom:"))
    s.append(homepage_wireframe())
    s.append(Spacer(1, 6))
    s.append(P("Homepage content fields", H2))
    s.append(table([
        ["#", "Section", "Content fields", "Links to"],
        ["1", "Header", "See Section 3", "All main pages"],
        ["2", "Hero slider", "Per slide: background image, headline (max 8 words), "
         "sub-line, button label + link", "Services, Partners, Media"],
        ["3", "Key numbers", "5 counters: value + label (e.g. 17+ Years, XX Partners) [TBC]",
         "About Us"],
        ["4", "About teaser", "Photo, 60-word intro, 'Read more' button", "Who We Are"],
        ["5", "Services grid", "6 cards: icon, title, one-line description", "Each service page"],
        ["6", "Partners carousel", "Partner logos (SVG/PNG), alt text, link", "Partner detail pages"],
        ["7", "Therapeutic areas", "Area name, icon, short text", "Filtered product listing"],
        ["8", "Coverage map", "Governorates served, branches, warehouse locations",
         "Branches / Coverage"],
        ["9", "Latest news", "Pulled automatically: 3 newest articles", "News articles"],
        ["10", "CTA band", "Headline + 2 buttons", "Become a Partner, Contact Us"],
        ["11", "Footer", "See Section 9", "Quick links, services, contact"],
    ], [22, 90, 230, FW - 342]))

    # 7 page content
    s += section(7, "Page-by-Page Content Specification")
    s.append(P(
        "Each inner page uses the same template: <b>page banner</b> (title + breadcrumb) "
        "> <b>content sections</b> > <b>related links</b> > <b>CTA band</b> > footer. "
        "The table lists what each page needs."))
    s.append(table([
        ["Page", "Key sections", "Content required", "Assets"],
        ["Who We Are", "Intro, what we do, key numbers, why Al-Qawsan",
         "300 - 500 words EN + AR, figures [TBC]", "Team / office photos"],
        ["Vision, Mission & Values", "Vision, mission, 4 - 6 values",
         "Approved statements", "Value icons"],
        ["Our History", "Timeline 2009 to today", "Milestones by year",
         "Historic photos"],
        ["Leadership Team", "Profile cards", "Name, title, short bio",
         "Professional portraits"],
        ["Quality & Compliance", "GDP / GSP, pharmacovigilance, certificates",
         "Policy summaries", "Certificate scans (PDF)"],
        ["Services (x6)", "Overview, how it works (steps), benefits, related partners",
         "150 - 300 words per service", "Service photo, icon"],
        ["Partners", "Logos grouped by region, filter", "Partner name, country, since",
         "High-resolution logos"],
        ["Partner Detail", "About the partner, products distributed",
         "Short profile + product links", "Logo, photo"],
        ["Become a Partner", "Benefits, form", "Form fields: company, country, "
         "portfolio, contact, message", "-"],
        ["Therapeutic Areas / Products", "Area tiles, search & filter, product cards",
         "Product name, generic name, form, strength, partner, area",
         "Pack shots"],
        ["Product Detail", "Summary, indications (as registered), partner, related",
         "Only MoH-approved information", "Pack shot, leaflet PDF"],
        ["News / Events / Gallery", "List + article pages, categories",
         "Title, date, text, tags", "Photos, videos"],
        ["Careers", "Why join, open positions, apply", "Job title, location, "
         "requirements, deadline", "Team photos"],
        ["Contact Us", "HQ details, map, branches, inquiry form",
         "Address, phones, e-mails, hours", "Map embed"],
    ], [92, 140, 150, FW - 382], font_size=7.6))

    # 8 connections
    s += [PageBreak()] + section(8, "How the Pages Connect")
    s.append(P(
        "Pages are connected in three ways: (1) the <b>header and footer</b> on every "
        "page, (2) <b>contextual links</b> inside the content, and (3) <b>calls to "
        "action</b> that lead each visitor type to a form. The diagram shows the "
        "main user journeys. Each one ends in a conversion (orange = form)."))
    s.append(journeys_diagram())
    s.append(Spacer(1, 6))
    s.append(P("Linking rules", H2))
    s.append(table([
        ["From", "Always links to", "Why"],
        ["Header (every page)", "8 main tabs, dropdown sub-pages, search, EN | AR, "
         "Become a Partner", "Global navigation"],
        ["Footer (every page)", "Quick links, all 6 services, contact details, "
         "privacy / terms", "Second navigation, SEO"],
        ["Page banner (inner pages)", "Breadcrumb: Home > Section > Page",
         "Visitor always knows where they are"],
        ["Service page", "Related partners, related therapeutic areas, "
         "Become a Partner CTA", "Turns interest into a lead"],
        ["Partner detail", "Products from that partner, Become a Partner",
         "Proof, then action"],
        ["Product detail", "Therapeutic area, partner page, related products, "
         "Contact (medical inquiry)", "Keeps visitors exploring"],
        ["News article", "Related news, share buttons, Media Center",
         "Engagement"],
        ["Any form", "Thank-you page and e-mail to the responsible team",
         "Every lead has an owner"],
    ], [110, 230, FW - 340]))
    s.append(P("Form routing", H2))
    s.append(table([
        ["Form", "Sent to", "Response time"],
        ["Become a Partner", "Business Development", "Within 2 working days"],
        ["Inquiry (Contact Us)", "Sales / Customer Service", "Within 1 working day"],
        ["Medical inquiry (product)", "Medical Affairs", "Within 2 working days"],
        ["Apply Online", "Human Resources", "Acknowledgement e-mail sent automatically"],
    ], [150, 170, FW - 320]))

    # 9 footer
    s += section(9, "Footer Structure")
    s.append(P(
        "The footer is the second navigation on every page. It has four columns and a "
        "copyright bar, following the reference site's layout."))
    s.append(footer_wireframe())
    s.append(Spacer(1, 6))
    s.append(table([
        ["Column", "Content"],
        ["1. About", "Logo, 25-word company description, newsletter sign-up, social icons"],
        ["2. Quick Links", "About Us, Partners, Products, Media Center, Careers, Contact Us"],
        ["3. Our Services", "Links to all 6 service pages"],
        ["4. Contact Us", "HQ address, phone, e-mail, working hours, small map"],
        ["Bottom bar", "Copyright, Privacy Policy, Terms of Use, Sitemap"],
    ], [100, FW - 100]))

    # 10 workflow
    s += [PageBreak()] + section(10, "Content Workflow")
    s.append(P(
        "Every piece of content (a new page, product, news article or job) follows the "
        "same seven-step workflow. Because Al-Qawsan works in a regulated industry, "
        "<b>medical and regulatory review is mandatory</b> before anything is published. "
        "Orange steps are approval gates. Red dashed arrows show where content goes back "
        "for changes."))
    s.append(workflow_diagram())
    s.append(Spacer(1, 6))
    s.append(P("Workflow steps", H2))
    s.append(table([
        ["Step", "Activity", "Owner", "Output"],
        ["1", "Content request & brief: topic, audience, page, deadline", "Marketing",
         "Content brief"],
        ["2", "Draft text in English and Arabic; collect photos, logos, PDFs",
         "Content Writer", "Draft + assets"],
        ["3", "Check medical claims, product data vs. MoH registration, compliance",
         "Medical / Regulatory", "Approved text"],
        ["4", "Build the page in the CMS on the staging site; set SEO title, meta, alt text",
         "Web Admin", "Staging page"],
        ["5", "Final review of the staging page", "Management", "Go / no-go"],
        ["6", "Publish live, check links, mobile view and both languages", "Web Admin",
         "Live page"],
        ["7", "Share on social media; monitor visits and form leads monthly", "Marketing",
         "Monthly report"],
    ], [30, 230, 90, FW - 350]))
    raci_head = [P("Roles & responsibilities (RACI)", H2),
                 P("R = Responsible, A = Accountable, C = Consulted, I = Informed", SMALL),
                 Spacer(1, 4)]
    raci = [["Activity", "Marketing", "Writer", "Medical / Reg.", "Web Admin", "Mgmt"],
            ["Content plan & briefs", "A / R", "C", "C", "I", "I"],
            ["Writing & translation", "A", "R", "C", "I", "-"],
            ["Medical / regulatory approval", "I", "C", "A / R", "-", "I"],
            ["CMS build & SEO", "C", "C", "-", "A / R", "-"],
            ["Final sign-off", "R", "-", "C", "I", "A"],
            ["Publishing & maintenance", "C", "-", "-", "A / R", "I"],
            ["Analytics & reporting", "A / R", "-", "-", "C", "I"]]
    rt = table(raci, [150] + [(FW - 150) / 5] * 5)
    rt.setStyle(TableStyle([("ALIGN", (1, 0), (-1, -1), "CENTER")]))
    s.append(KeepTogether(raci_head + [rt]))
    s.append(P("Update schedule", H2))
    s.append(table([
        ["Content", "Owner", "Frequency"],
        ["News & events", "Marketing", "Weekly (at least 2 posts per month)"],
        ["Products", "Medical / Regulatory + Web Admin", "Monthly, or when a registration changes"],
        ["Partners", "Business Development", "When a partnership is signed or ends"],
        ["Careers", "Human Resources", "When a position opens or closes"],
        ["Key numbers", "Management", "Every 6 months"],
        ["Full content audit", "Marketing", "Once a year"],
    ], [130, 170, FW - 300]))
    s.append(P("Publishing rules", H2))
    s += bullets([
        "No medical claim or product text goes live without Medical / Regulatory approval.",
        "Product information must match the Iraqi Ministry of Health registration.",
        "English and Arabic versions are published together.",
        "Images must be owned by the company or properly licensed. Partner logos need "
        "partner approval.",
        "Every change is logged in the CMS revision history.",
    ])

    # 11 tech
    s += section(11, "Technical Recommendations")
    s.append(table([
        ["Area", "Recommendation"],
        ["CMS", "WordPress with a multilingual plugin (WPML or Polylang) or a headless CMS. "
         "It must support Arabic RTL and user roles that match the workflow (writer, "
         "reviewer, publisher)."],
        ["Design", "Responsive (mobile first), corporate colours from the Al-Qawsan logo, "
         "one font family with Arabic support (e.g. Cairo, Tajawal or IBM Plex Sans Arabic)."],
        ["Performance", "Compressed WebP images, caching and a CDN. Target load time under "
         "3 seconds on mobile."],
        ["SEO", "Clean URLs (/en/services/cold-chain), meta titles and descriptions, "
         "XML sitemap, hreflang tags for EN / AR, Google Search Console."],
        ["Forms & leads", "Forms e-mail the responsible team (Section 8) and are stored in "
         "the CMS or a CRM; spam protection (reCAPTCHA)."],
        ["Security", "SSL (https), regular updates, daily backups, limited admin access."],
        ["Analytics", "Google Analytics 4 with conversion goals for every form."],
        ["Accessibility", "Good colour contrast, alt text on images, keyboard-friendly menus."],
    ], [90, FW - 90]))

    # 12 timeline
    s.append(KeepTogether(section(12, "Project Timeline")[1:] + [
        P("An estimated <b>12-week</b> plan from kick-off to launch. Orange "
          "diamonds are client sign-off milestones."), gantt()]))

    # 13 next steps
    s += section(13, "Next Steps & Inputs Needed from Al-Qawsan")
    s.append(table([
        ["#", "Input", "Details"],
        ["1", "Approve this structure", "Confirm the tabs, dropdowns and sitemap (or "
         "request changes)"],
        ["2", "Brand assets", "Logo files (vector), brand colours, any brand guidelines"],
        ["3", "Key numbers", "Partners, products, customers, governorates covered, "
         "employees"],
        ["4", "Partner list", "Names, countries, logos and permission to display them"],
        ["5", "Product portfolio", "Product list by therapeutic area (Excel), pack shots"],
        ["6", "Company content", "History milestones, vision / mission, leadership bios, "
         "certificates"],
        ["7", "Photography", "Office, warehouse, cold chain, team and event photos"],
        ["8", "Contacts & routing", "Official phones, e-mails, address, form recipients"],
        ["9", "Workflow owners", "Names of the Marketing, Medical / Regulatory, Web Admin "
         "and Management approvers"],
    ], [22, 120, FW - 142]))
    s.append(Spacer(1, 10))
    s.append(P(
        "Once the structure is approved, the next deliverable is the set of "
        "<b>desktop and mobile wireframes</b> for the Home page and one example page "
        "from each section, followed by the visual design.", NOTE))
    return s


def main():
    doc = ProposalDoc(OUT)
    doc.multiBuild(build())
    print("Wrote", OUT)


if __name__ == "__main__":
    main()
