#!/usr/bin/env python3
"""MealWise-style diagrams for the FixFlow final project report."""

from __future__ import annotations

import math
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont

DIR = Path(__file__).resolve().parent / "diagram_assets"
DIR.mkdir(exist_ok=True)

FONT_DIR = Path("/System/Library/Fonts/Supplemental")

# MealWise-inspired palette
BG = (255, 255, 255)
INK = (20, 20, 20)
MUTED = (80, 80, 80)
LINE = (40, 40, 40)
GREEN = (46, 125, 50)
GREEN_FILL = (232, 245, 233)
GREEN_OVAL = (200, 230, 201)
BLUE = (25, 118, 210)
BLUE_FILL = (227, 242, 253)
BLUE_OVAL = (187, 222, 251)
YELLOW = (245, 124, 0)
YELLOW_FILL = (255, 243, 224)
GREY = (245, 245, 245)
GREY_HDR = (224, 224, 224)
WHITE = (255, 255, 255)
FRAME = (210, 210, 210)


def _font(size: int, bold: bool = False, italic: bool = False):
    if bold and italic:
        name = "Times New Roman Bold Italic.ttf"
    elif bold:
        name = "Times New Roman Bold.ttf"
    elif italic:
        name = "Times New Roman Italic.ttf"
    else:
        name = "Times New Roman.ttf"
    path = FONT_DIR / name
    if not path.exists():
        path = FONT_DIR / ("Arial Bold.ttf" if bold else "Arial.ttf")
    try:
        return ImageFont.truetype(str(path), size)
    except OSError:
        return ImageFont.load_default()


def _afont(size: int, bold: bool = False):
    name = "Arial Bold.ttf" if bold else "Arial.ttf"
    try:
        return ImageFont.truetype(str(FONT_DIR / name), size)
    except OSError:
        return _font(size, bold)


def _size(draw, text, font):
    b = draw.textbbox((0, 0), text, font=font)
    return b[2] - b[0], b[3] - b[1]


def _center(draw, cx, cy, text, font, fill=INK):
    w, h = _size(draw, text, font)
    draw.text((cx - w / 2, cy - h / 2), text, font=font, fill=fill)


def _multiline(draw, cx, cy, lines, font, fill=INK, gap=3):
    heights = [_size(draw, ln, font)[1] for ln in lines]
    total = sum(heights) + gap * (len(lines) - 1)
    y = cy - total / 2
    for ln, h in zip(lines, heights):
        w, _ = _size(draw, ln, font)
        draw.text((cx - w / 2, y), ln, font=font, fill=fill)
        y += h + gap


def _arrow(draw, x1, y1, x2, y2, color=LINE, width=2):
    draw.line((x1, y1, x2, y2), fill=color, width=width)
    ang = math.atan2(y2 - y1, x2 - x1)
    size = 9
    left = (x2 - size * math.cos(ang - 0.4), y2 - size * math.sin(ang - 0.4))
    right = (x2 - size * math.cos(ang + 0.4), y2 - size * math.sin(ang + 0.4))
    draw.polygon([(x2, y2), left, right], fill=color)


def _label_box(draw, x, y, text, font):
    w, h = _size(draw, text, font)
    pad = 3
    draw.rectangle((x - pad, y - pad, x + w + pad, y + h + pad), fill=GREY, outline=FRAME)
    draw.text((x, y), text, font=font, fill=MUTED)


def _new(w, h):
    img = Image.new("RGB", (w, h), BG)
    return img, ImageDraw.Draw(img)


def save(img, name):
    path = DIR / name
    img.save(path, "PNG")
    return path


def _frame(draw, box, label=None):
    draw.rectangle(box, outline=FRAME, width=2, fill=None)
    if label:
        font = _afont(12, True)
        w, h = _size(draw, label, font)
        x = (box[0] + box[2] - w) / 2
        draw.rectangle((x - 4, box[1] - 2, x + w + 4, box[1] + h + 4), fill=WHITE)
        draw.text((x, box[1]), label, font=font, fill=INK)


def _cylinder(draw, x, y, w, h, text, font):
    """Classic DFD data-store cylinder."""
    ry = 12
    draw.ellipse((x, y, x + w, y + ry * 2), fill=GREEN_FILL, outline=GREEN, width=2)
    draw.rectangle((x, y + ry, x + w, y + h - ry), fill=GREEN_FILL, outline=GREEN, width=2)
    draw.ellipse((x, y + h - ry * 2, x + w, y + h), fill=GREEN_FILL, outline=GREEN, width=2)
    # cover top arc again
    draw.arc((x, y, x + w, y + ry * 2), 0, 180, fill=GREEN, width=2)
    _center(draw, x + w / 2, y + h / 2, text, font, GREEN)


def _process_circle(draw, cx, cy, r, lines, font, title_font=None):
    draw.ellipse((cx - r, cy - r, cx + r, cy + r), fill=GREEN_FILL, outline=GREEN, width=3)
    _multiline(draw, cx, cy, lines, font, GREEN)


# ---------- Diagrams ----------


def use_case():
    img, d = _new(1200, 780)
    # outer functional boundary
    d.rectangle((40, 30, 1160, 740), outline=LINE, width=2)
    _center(d, 600, 50, "FixFlow Functional Boundary", _afont(14, True))

    # system box
    d.rounded_rectangle((280, 80, 920, 700), radius=18, outline=GREEN, width=3, fill=(250, 255, 250))
    _center(d, 600, 105, "FixFlow System", _afont(15, True), GREEN)

    def stick(x, y, name):
        d.ellipse((x - 12, y, x + 12, y + 24), outline=INK, width=2)
        d.line((x, y + 24, x, y + 58), fill=INK, width=2)
        d.line((x - 18, y + 38, x + 18, y + 38), fill=INK, width=2)
        d.line((x, y + 58, x - 14, y + 85), fill=INK, width=2)
        d.line((x, y + 58, x + 14, y + 85), fill=INK, width=2)
        _center(d, x, y + 105, name, _afont(13, True))

    stick(120, 220, "Customer")
    stick(120, 480, "Technician")
    stick(1080, 350, "Admin")

    def oval(x, y, w, h, text, fill, outline):
        d.ellipse((x, y, x + w, y + h), fill=fill, outline=outline, width=2)
        _center(d, x + w / 2, y + h / 2, text, _afont(11))

    cust = [
        (310, 140, "Register / Login"),
        (310, 195, "Create repair request"),
        (310, 250, "Track repair timeline"),
        (310, 305, "Chat on repair"),
        (310, 360, "Pay invoice (Stripe)"),
        (310, 415, "Choose pickup/delivery"),
        (310, 470, "View warranty"),
        (310, 525, "Reset password"),
    ]
    tech = [
        (530, 220, "Apply as technician"),
        (530, 280, "View assigned jobs"),
        (530, 340, "Update status"),
        (530, 400, "Save diagnosis"),
        (530, 460, "Chat on repair"),
    ]
    admin = [
        (720, 140, "Manage users"),
        (720, 195, "Approve technician apps"),
        (720, 250, "Assign technician"),
        (720, 305, "Send / delete invoice"),
        (720, 360, "Mark invoice paid"),
        (720, 415, "Complete fulfillment"),
        (720, 470, "Issue warranty"),
        (720, 525, "View reports"),
        (720, 580, "Update any repair"),
    ]

    for x, y, t in cust:
        oval(x, y, 190, 42, t, GREEN_OVAL, GREEN)
        d.line((140, 280, x, y + 21), fill=MUTED, width=1)
    for x, y, t in tech:
        oval(x, y, 190, 42, t, GREEN_OVAL, GREEN)
        d.line((140, 560, x, y + 21), fill=MUTED, width=1)
    for x, y, t in admin:
        oval(x, y, 190, 42, t, BLUE_OVAL, BLUE)
        d.line((1080, 420, x + 190, y + 21), fill=MUTED, width=1)

    # include: pay includes choose? better: complete repair includes draft invoice — show book-like include
    # Pay invoice <<include>> Choose pickup after pay — use extend from fulfillment
    d.line((405, 381, 405, 415), fill=LINE, width=1)
    # dashed include from Pay to Choose
    y1, y2 = 402, 415
    for i in range(0, 14, 2):
        yy = y1 + i
        if yy < y2:
            d.line((405, yy, 405, min(yy + 4, y2)), fill=LINE, width=1)
    _center(d, 470, 395, "<<include>>", _afont(9), MUTED)

    return save(img, "usecase.png")


def architecture():
    """Three-layer architecture in black & white (print-friendly)."""
    img, d = _new(1100, 420)
    border = INK
    fill = WHITE
    header_fill = GREY_HDR
    layers = [
        (
            40,
            "Presentation Layer",
            ["Blade + Tailwind UI", "Vite assets", "Laravel Echo"],
            "localhost:8000 / :5173",
        ),
        (
            390,
            "Application Layer",
            ["Laravel Controllers", "Auth + Role middleware", "Services (Invoice/Stripe)", "Eloquent / Query Builder"],
            "PHP 8.3+ / Artisan",
        ),
        (
            740,
            "Data Layer",
            ["PostgreSQL database", "Migrations + Seeders", "File storage (images)"],
            "127.0.0.1:5432 / fixflow",
        ),
    ]
    title_f = _afont(13, True)
    body_f = _afont(12)
    small = _afont(10)

    for x, title, items, port in layers:
        d.rounded_rectangle((x, 40, x + 300, 300), radius=10, fill=fill, outline=border, width=2)
        d.rectangle((x, 40, x + 300, 88), fill=header_fill, outline=border, width=2)
        _center(d, x + 150, 64, title, title_f, INK)
        yy = 105
        for item in items:
            d.rounded_rectangle((x + 20, yy, x + 280, yy + 32), radius=4, fill=WHITE, outline=LINE, width=1)
            _center(d, x + 150, yy + 16, item, body_f, INK)
            yy += 42
        _center(d, x + 150, 330, port, small, MUTED)

    # arrows between layers
    _arrow(d, 340, 160, 385, 160)
    _center(d, 362, 140, "HTTP / Session", small, MUTED)
    _arrow(d, 690, 160, 735, 160)
    _center(d, 712, 140, "SQL / Eloquent", small, MUTED)

    # outer frame
    d.rectangle((20, 20, 1080, 360), outline=LINE, width=1)

    return save(img, "architecture.png")


def dfd_level0():
    img, d = _new(1100, 400)
    body = _afont(13)
    small = _afont(11)

    ents = [
        (40, 50, "Customer"),
        (40, 170, "Technician"),
        (40, 290, "Admin"),
        (900, 90, "Stripe"),
        (900, 240, "Email / SMTP"),
    ]
    for x, y, name in ents:
        d.rectangle((x, y, x + 150, y + 50), fill=GREEN_FILL, outline=GREEN, width=2)
        _center(d, x + 75, y + 25, name, body, GREEN)

    cx, cy, r = 550, 200, 95
    _process_circle(d, cx, cy, r, ["0", "FixFlow", "Repair System"], _afont(14, True))

    flows = [
        (190, 75, 460, 150, "request, pay, chat"),
        (460, 170, 190, 100, "status, invoice"),
        (190, 195, 455, 200, "job update, chat"),
        (190, 315, 460, 250, "assign, reports"),
        (645, 170, 900, 115, "checkout"),
        (900, 130, 645, 185, "payment result"),
        (645, 240, 900, 265, "reset email"),
    ]
    for x1, y1, x2, y2, label in flows:
        _arrow(d, x1, y1, x2, y2, width=2)
        mx, my = (x1 + x2) / 2, (y1 + y2) / 2 - 10
        _label_box(d, mx - 40, my, label, small)

    return save(img, "dfd0.png")


def dfd_level1():
    img, d = _new(1200, 520)
    body = _afont(12)
    small = _afont(10)

    # external entities
    d.rectangle((20, 180, 130, 235), fill=GREEN_FILL, outline=GREEN, width=2)
    _center(d, 75, 207, "Customer", body, GREEN)
    d.rectangle((20, 280, 130, 335), fill=GREEN_FILL, outline=GREEN, width=2)
    _center(d, 75, 307, "Technician", body, GREEN)
    d.rectangle((1070, 220, 1180, 275), fill=GREEN_FILL, outline=GREEN, width=2)
    _center(d, 1125, 247, "Admin", body, GREEN)

    # processes in one denser row + second row
    procs = [
        (180, 40, "1.0", "Auth"),
        (360, 40, "2.0", "Repairs"),
        (540, 40, "3.0", "Tech Work"),
        (720, 40, "4.0", "Invoice/Pay"),
        (270, 200, "5.0", "Fulfill"),
        (450, 200, "6.0", "Messaging"),
        (630, 200, "7.0", "Reports"),
        (810, 200, "8.0", "Tech Apps"),
    ]
    for x, y, num, name in procs:
        _process_circle(d, x + 50, y + 50, 42, [num, name], _afont(11, True))

    stores = [
        (160, 380, "D1 Users"),
        (340, 380, "D2 Repairs"),
        (520, 380, "D3 Invoices"),
        (700, 380, "D4 Messages"),
        (880, 380, "D5 Apps"),
    ]
    for x, y, name in stores:
        _cylinder(d, x, y, 120, 60, name, small)

    _arrow(d, 130, 207, 200, 90)
    _arrow(d, 130, 307, 380, 100)
    _arrow(d, 900, 250, 1070, 247)
    _arrow(d, 230, 132, 220, 380)
    _arrow(d, 410, 132, 400, 380)
    _arrow(d, 790, 132, 580, 380)
    _arrow(d, 500, 292, 760, 380)
    _arrow(d, 860, 292, 940, 380)
    # process links
    _arrow(d, 270, 90, 360, 90)
    _arrow(d, 450, 90, 540, 90)
    _arrow(d, 630, 90, 720, 90)
    _arrow(d, 230, 132, 300, 200)
    _arrow(d, 790, 132, 850, 200)

    _label_box(d, 135, 130, "login", small)
    _label_box(d, 135, 250, "job update", small)
    _label_box(d, 980, 200, "admin ops", small)
    _center(d, 600, 500, "D1-D5 map to users, repairs, invoices, messages, technician_applications", small, MUTED)

    return save(img, "dfd1.png")


def erd():
    """Crow's-foot style ERD matching MealWise tables look."""
    img, d = _new(1200, 860)
    af = _afont(10)
    hdr = _afont(11, True)
    tiny = _afont(9)

    def table(x, y, w, name, rows):
        row_h = 18
        h = 26 + len(rows) * row_h
        d.rectangle((x, y, x + w, y + h), outline=LINE, width=1, fill=WHITE)
        d.rectangle((x, y, x + w, y + 26), fill=GREY_HDR, outline=LINE, width=1)
        _center(d, x + w / 2, y + 13, name, hdr)
        yy = y + 26
        for col, typ, kind in rows:
            d.line((x, yy, x + w, yy), fill=FRAME, width=1)
            # PK/FK marker
            if kind == "PK":
                d.ellipse((x + 6, yy + 5, x + 14, yy + 13), fill=GREEN, outline=GREEN)
            elif kind == "FK":
                d.rectangle((x + 6, yy + 5, x + 14, yy + 13), fill=BLUE, outline=BLUE)
            d.text((x + 20, yy + 3), col, font=tiny, fill=INK)
            tw, _ = _size(d, typ, tiny)
            d.text((x + w - tw - 6, yy + 3), typ, font=tiny, fill=MUTED)
            yy += row_h
        return x, y, w, h

    # tables
    table(
        40,
        40,
        280,
        "USERS",
        [
            ("id", "INTEGER", "PK"),
            ("name", "VARCHAR", ""),
            ("email", "VARCHAR UQ", ""),
            ("password", "VARCHAR", ""),
            ("role", "VARCHAR", ""),
            ("created_at", "TIMESTAMP", ""),
        ],
    )
    table(
        420,
        30,
        320,
        "REPAIR_REQUESTS",
        [
            ("id", "INTEGER", "PK"),
            ("reference", "VARCHAR UQ", ""),
            ("user_id", "INTEGER", "FK"),
            ("technician_id", "INTEGER", "FK"),
            ("status", "VARCHAR", ""),
            ("priority", "VARCHAR", ""),
            ("diagnosis_notes", "TEXT", ""),
            ("fulfillment_status", "VARCHAR", ""),
            ("fulfillment_method", "VARCHAR", ""),
            ("delivery_address", "TEXT", ""),
        ],
    )
    table(
        860,
        40,
        280,
        "INVOICES",
        [
            ("id", "INTEGER", "PK"),
            ("repair_request_id", "INTEGER UQ", "FK"),
            ("invoice_number", "VARCHAR", ""),
            ("total_amount", "DECIMAL", ""),
            ("payment_status", "VARCHAR", ""),
            ("payment_method", "VARCHAR", ""),
            ("stripe_session_id", "VARCHAR", ""),
            ("paid_at", "TIMESTAMP", ""),
        ],
    )
    table(
        860,
        280,
        280,
        "WARRANTIES",
        [
            ("id", "INTEGER", "PK"),
            ("repair_request_id", "INTEGER UQ", "FK"),
            ("warranty_code", "VARCHAR", ""),
            ("start_date", "DATE", ""),
            ("end_date", "DATE", ""),
            ("notes", "TEXT", ""),
        ],
    )
    table(
        420,
        380,
        320,
        "MESSAGES",
        [
            ("id", "INTEGER", "PK"),
            ("repair_request_id", "INTEGER", "FK"),
            ("user_id", "INTEGER", "FK"),
            ("body", "TEXT", ""),
            ("read_at", "TIMESTAMP", ""),
            ("created_at", "TIMESTAMP", ""),
        ],
    )
    table(
        40,
        320,
        280,
        "TECHNICIAN_APPLICATIONS",
        [
            ("id", "INTEGER", "PK"),
            ("user_id", "INTEGER UQ", "FK"),
            ("phone", "VARCHAR", ""),
            ("experience_years", "INTEGER", ""),
            ("status", "VARCHAR", ""),
            ("reviewed_by", "INTEGER", "FK"),
            ("admin_notes", "TEXT", ""),
        ],
    )

    def crow_foot(x, y, facing="right"):
        """Simple crow's foot mark."""
        if facing == "right":
            d.line((x, y - 6, x + 10, y), fill=LINE, width=2)
            d.line((x, y, x + 10, y), fill=LINE, width=2)
            d.line((x, y + 6, x + 10, y), fill=LINE, width=2)
        elif facing == "left":
            d.line((x, y - 6, x - 10, y), fill=LINE, width=2)
            d.line((x, y, x - 10, y), fill=LINE, width=2)
            d.line((x, y + 6, x - 10, y), fill=LINE, width=2)
        elif facing == "down":
            d.line((x - 6, y, x, y + 10), fill=LINE, width=2)
            d.line((x, y, x, y + 10), fill=LINE, width=2)
            d.line((x + 6, y, x, y + 10), fill=LINE, width=2)

    def one_mark(x, y, horiz=True):
        if horiz:
            d.line((x, y - 7, x, y + 7), fill=LINE, width=2)
        else:
            d.line((x - 7, y, x + 7, y), fill=LINE, width=2)

    # USER 1 -- * REPAIR (customer)
    d.line((320, 100, 420, 100), fill=LINE, width=2)
    one_mark(325, 100)
    crow_foot(410, 100, "left")

    # USER 1 -- * REPAIR (tech) second line
    d.line((320, 140, 420, 140), fill=LINE, width=2)
    one_mark(325, 140)
    crow_foot(410, 140, "left")

    # REPAIR 1 -- 1 INVOICE
    d.line((740, 120, 860, 120), fill=LINE, width=2)
    one_mark(750, 120)
    one_mark(850, 120)

    # REPAIR 1 -- 1 WARRANTY
    d.line((740, 220, 860, 320), fill=LINE, width=2)
    one_mark(750, 225)
    one_mark(850, 315)

    # REPAIR 1 -- * MESSAGE
    d.line((580, 310, 580, 380), fill=LINE, width=2)
    one_mark(580, 320, horiz=False)
    crow_foot(580, 370, "down")

    # USER 1 -- 1 APPLICATION
    d.line((180, 230, 180, 320), fill=LINE, width=2)
    one_mark(180, 240, horiz=False)
    one_mark(180, 310, horiz=False)

    # USER -- * MESSAGE
    d.line((320, 200, 420, 450), fill=LINE, width=2)

    # compact legend along bottom
    d.rectangle((40, 560, 1160, 620), outline=FRAME, width=1, fill=GREY)
    d.ellipse((60, 580, 72, 592), fill=GREEN)
    d.text((80, 578), "PK", font=af, fill=INK)
    d.rectangle((140, 580, 152, 592), fill=BLUE)
    d.text((160, 578), "FK", font=af, fill=INK)
    d.line((240, 586, 280, 586), fill=LINE, width=2)
    one_mark(250, 586)
    d.text((290, 578), "One", font=af, fill=INK)
    crow_foot(380, 586, "right")
    d.text((400, 578), "Many (crow's foot)", font=af, fill=INK)
    d.text(
        (620, 578),
        "REPAIR_REQUESTS is central; INVOICE/WARRANTY are 1:1; MESSAGE is 1:N.",
        font=af,
        fill=MUTED,
    )

    d.rectangle((30, 20, 1170, 640), outline=FRAME, width=2)
    _center(d, 600, 12, "FixFlow", _afont(12, True))

    # crop canvas to used area
    img = img.crop((0, 0, 1200, 660))
    return save(img, "erd.png")


def activity():
    """Compact left-to-right then wrap activity (fits half a page when scaled)."""
    img, d = _new(1100, 420)
    body = _afont(11)
    steps = [
        "1. Submit repair\n(pending)",
        "2. Assign tech\n(assigned)",
        "3. Diagnose /\nrepair",
        "4. Complete +\ndraft invoice",
        "5. Send invoice\n(unpaid)",
        "6. Pay\n(awaiting_choice)",
        "7. Pickup or\ndelivery",
        "8. Confirm\n(fulfilled)",
    ]
    # start
    d.ellipse((30, 50, 60, 80), fill=INK)
    # row 1: steps 0-3
    coords = []
    for i in range(4):
        x = 90 + i * 250
        y = 30
        d.rounded_rectangle((x, y, x + 200, y + 70), radius=10, fill=GREEN_FILL, outline=GREEN, width=2)
        _multiline(d, x + 100, y + 35, steps[i].split("\n"), body, GREEN)
        coords.append((x + 100, y + 35, x, y, x + 200, y + 70))
    _arrow(d, 60, 65, 90, 65)
    for i in range(3):
        _arrow(d, coords[i][4], 65, coords[i + 1][2], 65)
    # connector down to row 2
    _arrow(d, coords[3][0], 100, coords[3][0], 160)
    # row 2: steps 4-7 right to left visually but numbered forward left to right
    for i in range(4):
        x = 90 + i * 250
        y = 160
        d.rounded_rectangle((x, y, x + 200, y + 70), radius=10, fill=GREEN_FILL, outline=GREEN, width=2)
        _multiline(d, x + 100, y + 35, steps[4 + i].split("\n"), body, GREEN)
        coords.append((x + 100, y + 35, x, y, x + 200, y + 70))
    for i in range(4, 7):
        _arrow(d, coords[i][4], 195, coords[i + 1][2], 195)
    # end
    _arrow(d, coords[7][4], 195, 1070, 195)
    d.ellipse((1070, 180, 1100, 210), outline=INK, width=3)
    d.ellipse((1078, 188, 1092, 202), fill=INK)
    _center(
        d,
        550,
        290,
        "Happy path: request → assign → repair → invoice → pay → fulfill",
        _afont(12),
        MUTED,
    )
    img = img.crop((0, 0, 1100, 320))
    return save(img, "activity.png")


def sequence_chat():
    img, d = _new(1100, 400)
    small = _afont(11)
    actors = [
        (120, "Customer UI"),
        (340, "Controller"),
        (560, "Database"),
        (780, "Reverb"),
        (980, "Tech UI"),
    ]
    for x, name in actors:
        d.rectangle((x - 60, 20, x + 60, 55), fill=GREEN_FILL, outline=GREEN, width=2)
        _center(d, x, 37, name, small, GREEN)
        d.line((x, 55, x, 370), fill=FRAME, width=1)

    def msg(x1, x2, y, text):
        _arrow(d, x1, y, x2, y)
        mid = (x1 + x2) / 2
        tw, th = _size(d, text, small)
        d.rectangle((mid - tw / 2 - 3, y - th - 5, mid + tw / 2 + 3, y - 1), fill=WHITE)
        d.text((mid - tw / 2, y - th - 3), text, font=small, fill=MUTED)

    msg(120, 340, 90, "1. POST message")
    msg(340, 560, 130, "2. Message::create")
    msg(560, 340, 170, "3. OK")
    msg(340, 780, 210, "4. broadcast MessageSent")
    msg(780, 980, 250, "5. Echo event")
    msg(980, 340, 290, "6. mark read")
    msg(120, 340, 340, "7. unread-count poll")
    return save(img, "seq_chat.png")


def sequence_stripe():
    img, d = _new(1100, 380)
    small = _afont(11)
    actors = [
        (130, "Customer"),
        (350, "FixFlow"),
        (570, "Stripe"),
        (790, "Webhook"),
        (980, "Database"),
    ]
    for x, name in actors:
        d.rectangle((x - 60, 20, x + 60, 55), fill=BLUE_FILL, outline=BLUE, width=2)
        _center(d, x, 37, name, small, BLUE)
        d.line((x, 55, x, 350), fill=FRAME, width=1)

    def msg(x1, x2, y, text):
        _arrow(d, x1, y, x2, y)
        mid = (x1 + x2) / 2
        tw, th = _size(d, text, small)
        d.rectangle((mid - tw / 2 - 3, y - th - 5, mid + tw / 2 + 3, y - 1), fill=WHITE)
        d.text((mid - tw / 2, y - th - 3), text, font=small, fill=MUTED)

    msg(130, 350, 90, "1. POST /invoices/{id}/pay")
    msg(350, 570, 130, "2. createCheckoutSession")
    msg(570, 130, 170, "3. Redirect Checkout")
    msg(130, 570, 210, "4. Pay test card")
    msg(570, 350, 250, "5. success URL")
    msg(570, 790, 290, "6. session.completed")
    msg(350, 980, 330, "7. mark paid")
    return save(img, "seq_stripe.png")


def class_diagram():
    img, d = _new(1100, 620)
    hdr = _afont(11, True)
    tiny = _afont(9)

    def klass(x, y, w, h, name, attrs, methods, border=GREEN, fill=GREEN_FILL):
        d.rectangle((x, y, x + w, y + h), outline=border, width=2, fill=WHITE)
        d.rectangle((x, y, x + w, y + 24), fill=fill, outline=border, width=2)
        _center(d, x + w / 2, y + 12, name, hdr, border)
        yy = y + 30
        for a in attrs:
            d.text((x + 8, yy), a, font=tiny, fill=INK)
            yy += 14
        d.line((x + 4, yy, x + w - 4, yy), fill=FRAME, width=1)
        yy += 4
        for m in methods:
            d.text((x + 8, yy), m, font=tiny, fill=MUTED)
            yy += 14

    klass(
        40,
        40,
        280,
        260,
        "User",
        ["+ id, name, email", "+ role, password"],
        ["+ isAdmin()", "+ isCustomer()", "+ isTechnician()", "+ isApprovedTechnician()", "+ repairRequests()", "+ messages()"],
    )
    klass(
        400,
        40,
        300,
        280,
        "RepairRequest",
        ["+ reference, status", "+ priority, diagnosis", "+ fulfillment_*"],
        ["+ customer()", "+ technician()", "+ invoice()", "+ warranty()", "+ messages()", "+ hasChatParticipant()"],
        BLUE,
        BLUE_FILL,
    )
    klass(
        780,
        40,
        280,
        200,
        "Invoice",
        ["+ totals, payment_status", "+ stripe_*, paid_at"],
        ["+ isDraft()", "+ isPaid()", "+ isPayable()"],
        YELLOW,
        YELLOW_FILL,
    )
    klass(
        780,
        280,
        280,
        160,
        "Warranty",
        ["+ warranty_code", "+ start_date, end_date"],
        ["+ repairRequest()"],
        YELLOW,
        YELLOW_FILL,
    )
    klass(
        400,
        360,
        300,
        160,
        "Message",
        ["+ body, read_at", "+ repair_request_id, user_id"],
        ["+ user()", "+ repairRequest()"],
    )
    klass(
        40,
        340,
        280,
        200,
        "TechnicianApplication",
        ["+ phone, status", "+ reviewed_by, notes"],
        ["+ user()", "+ reviewer()"],
        BLUE,
        BLUE_FILL,
    )

    d.line((320, 120, 400, 120), fill=LINE, width=2)
    d.line((700, 120, 780, 100), fill=LINE, width=2)
    d.line((700, 220, 780, 320), fill=LINE, width=2)
    d.line((550, 320, 550, 360), fill=LINE, width=2)
    d.line((180, 300, 180, 340), fill=LINE, width=2)
    _center(d, 550, 545, "Associations mirror Eloquent relationships in app/Models.", _afont(11), MUTED)

    img = img.crop((0, 0, 1100, 570))
    return save(img, "class.png")


def mvc_architecture():
    """Alias kept for older generator; prefer architecture()."""
    return architecture()


def generate_all():
    paths = {
        "usecase": use_case(),
        "architecture": architecture(),
        "mvc": architecture(),
        "dfd0": dfd_level0(),
        "dfd1": dfd_level1(),
        "erd": erd(),
        "activity": activity(),
        "seq_chat": sequence_chat(),
        "seq_stripe": sequence_stripe(),
        "class": class_diagram(),
    }
    for k, p in paths.items():
        print(f"  {k}: {p}")
    return paths


if __name__ == "__main__":
    generate_all()
