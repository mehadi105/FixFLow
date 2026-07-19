#!/usr/bin/env python3
"""Generate FixFlow Final Project Report in MealWise academic style."""

from __future__ import annotations

from pathlib import Path

from fpdf import FPDF

from diagrams import generate_all

DOCS = Path(__file__).resolve().parent
OUT = DOCS / "FixFlow_Final_Project_Report.pdf"
ASSETS = DOCS / "diagram_assets"
SCREENSHOTS = DOCS / "screenshots"
LOGO = ASSETS / "kuet_logo.png"
FONT_DIR = Path("/System/Library/Fonts/Supplemental")

LM, RM, TM, BM = 18, 18, 18, 16
CW = 210 - LM - RM


class Report(FPDF):
    def __init__(self):
        super().__init__(format="A4", unit="mm")
        self.set_auto_page_break(auto=True, margin=BM + 4)
        self.set_margins(LM, TM + 4, RM)
        self._cover = True
        self.chapter_left = ""
        self.chapter_right = ""
        self.footer_left = "FixFlow Final Report"
        self._pending_chapter = None  # applied in header on next page only
        self._register_fonts()

    def _register_fonts(self):
        self.add_font("TNR", "", str(FONT_DIR / "Times New Roman.ttf"))
        self.add_font("TNR", "B", str(FONT_DIR / "Times New Roman Bold.ttf"))
        self.add_font("TNR", "I", str(FONT_DIR / "Times New Roman Italic.ttf"))
        self.add_font("TNR", "BI", str(FONT_DIR / "Times New Roman Bold Italic.ttf"))
        self.add_font("AR", "", str(FONT_DIR / "Arial.ttf"))
        self.add_font("AR", "B", str(FONT_DIR / "Arial Bold.ttf"))

    def header(self):
        if self._pending_chapter:
            self.chapter_left, self.chapter_right, self.footer_left = self._pending_chapter
            self._pending_chapter = None
            self._cover = False
        if self._cover:
            return
        self.set_font("AR", "", 9)
        self.set_text_color(60, 60, 60)
        self.set_xy(LM, 10)
        self.cell(CW / 2, 5, self.chapter_left, align="L")
        self.cell(CW / 2, 5, self.chapter_right, align="R")
        self.set_draw_color(0, 0, 0)
        self.set_line_width(0.35)
        self.line(LM, 16, 210 - RM, 16)
        self.set_y(20)
        self.set_text_color(0, 0, 0)

    def footer(self):
        if self._cover:
            return
        self.set_y(-14)
        self.set_draw_color(0, 0, 0)
        self.set_line_width(0.35)
        self.line(LM, self.get_y(), 210 - RM, self.get_y())
        self.ln(1.5)
        self.set_font("AR", "", 8)
        self.set_text_color(60, 60, 60)
        self.set_x(LM)
        self.cell(CW - 15, 5, self.footer_left, align="L")
        self.cell(15, 5, str(self.page_no()), align="R")

    def _x(self):
        self.set_x(LM)

    def set_chapter(self, left: str, right: str, footer: str):
        """Queue chapter labels for the next add_page (keeps current page footer intact)."""
        self._pending_chapter = (left, right, footer)

    def begin_chapter(self, left: str, right: str, footer: str, min_remain: float = 55):
        """Start a chapter; new page only if not enough space left (reduces blank pages)."""
        if self.page_no() == 0 or self.remaining() < min_remain:
            self.set_chapter(left, right, footer)
            self.add_page()
        else:
            # continue packing on current page; labels apply on next auto-break
            self.chapter_left = left
            self.chapter_right = right
            self.footer_left = footer
            self.ln(2)

    def section_title(self, text: str):
        self._x()
        self.set_font("TNR", "B", 14)
        self.set_text_color(0, 0, 0)
        self.multi_cell(CW, 7, text)
        self.set_draw_color(0, 0, 0)
        self.set_line_width(0.45)
        y = self.get_y() + 0.5
        self.line(LM, y, 210 - RM, y)
        self.ln(5)

    def h2(self, text: str):
        self._x()
        if self.get_y() > 255:
            self.add_page()
        self.ln(1)
        self.set_font("TNR", "B", 11)
        self.multi_cell(CW, 6, text)
        self.ln(1.5)

    def p(self, text: str):
        self._x()
        self.set_font("TNR", "", 10)
        self.set_text_color(20, 20, 20)
        self.multi_cell(CW, 5.0, text, align="L")
        self.ln(2)

    def toc_row(self, x: float, y: float, width: float, num: str, title: str, page: str):
        """Single TOC line with dot leaders (MealWise style)."""
        num_w, page_w = 16, 8
        mid_w = width - num_w - page_w
        self.set_xy(x, y)
        self.set_font("TNR", "B", 9)
        self.cell(num_w, 5.5, num)
        self.set_font("AR", "", 9)
        title = title.strip()
        tw = self.get_string_width(title)
        if tw > mid_w - 14:
            while tw > mid_w - 14 and len(title) > 6:
                title = title[:-4].rstrip() + "..."
                tw = self.get_string_width(title)
        self.cell(tw + 1, 5.5, title)
        dot_w = max(0, mid_w - tw - 2)
        if dot_w > 2:
            self.set_text_color(160, 160, 160)
            self.set_font("AR", "", 8)
            n_dots = max(4, int(dot_w / 1.3))
            self.cell(dot_w, 5.5, "." * n_dots)
            self.set_text_color(0, 0, 0)
        self.set_font("TNR", "", 9)
        self.cell(page_w, 5.5, page, align="R")

    def bullet(self, text: str):
        self._x()
        self.set_font("TNR", "", 10)
        self.set_x(LM + 3)
        self.cell(4, 5, "-")
        self.multi_cell(CW - 7, 5, text)
        self.ln(0.3)

    def numbered(self, n: int, text: str):
        self._x()
        self.set_font("TNR", "", 10)
        self.set_x(LM + 3)
        self.cell(7, 5, f"{n}.")
        self.multi_cell(CW - 10, 5, text)
        self.ln(0.3)

    def caption(self, text: str):
        self._x()
        self.set_font("TNR", "I", 9)
        self.set_text_color(50, 50, 50)
        self.multi_cell(CW, 4.5, text, align="C")
        self.ln(3)
        self.set_text_color(0, 0, 0)

    def note_box(self, text: str):
        self._x()
        self.set_font("TNR", "", 9)
        lines = self.multi_cell(CW - 6, 4.5, text, dry_run=True, output="LINES")
        h = len(lines) * 4.5 + 6
        # Page-break only when the box cannot fit on the current page
        # (avoids nearly-blank pages with a single note).
        if self.get_y() + h > 272:
            self.add_page()
        y = self.get_y()
        self.set_draw_color(0, 0, 0)
        self.set_line_width(0.3)
        self.rect(LM, y, CW, h)
        self.set_xy(LM + 3, y + 3)
        self.multi_cell(CW - 6, 4.5, text)
        self.set_y(y + h + 3)

    def keywords_box(self, text: str):
        self._x()
        y = self.get_y()
        self.set_font("TNR", "", 9)
        body = f"Keywords: {text}"
        lines = self.multi_cell(CW - 6, 4.5, body, dry_run=True, output="LINES")
        h = len(lines) * 4.5 + 6
        self.set_draw_color(0, 0, 0)
        self.rect(LM, y, CW, h)
        self.set_xy(LM + 3, y + 3)
        self.set_font("TNR", "B", 9)
        self.write(4.5, "Keywords: ")
        self.set_font("TNR", "", 9)
        self.multi_cell(CW - 6 - self.get_string_width("Keywords: "), 4.5, text)
        self.set_y(y + h + 3)

    def metric_grid(self, items):
        """2x4 metric boxes like MealWise abstract."""
        cols, rows = 4, 2
        gap = 3
        box_w = (CW - gap * (cols - 1)) / cols
        box_h = 22
        start_y = self.get_y()
        for i, (value, label) in enumerate(items):
            r, c = divmod(i, cols)
            x = LM + c * (box_w + gap)
            y = start_y + r * (box_h + gap)
            self.set_draw_color(0, 0, 0)
            self.set_line_width(0.35)
            self.rect(x, y, box_w, box_h)
            self.set_xy(x, y + 3)
            self.set_font("TNR", "B", 14)
            self.cell(box_w, 8, str(value), align="C")
            self.set_xy(x, y + 12)
            self.set_font("AR", "", 7.5)
            self.cell(box_w, 6, label, align="C")
        self.set_y(start_y + rows * (box_h + gap) + 2)

    def two_col_boxes(self, left_title, left_items, right_title, right_items):
        y0 = self.get_y()
        gap = 4
        w = (CW - gap) / 2
        self.set_font("TNR", "", 9)
        # measure
        self.set_font("TNR", "B", 10)
        lh = 6 + len(left_items) * 5 + 8
        rh = 6 + len(right_items) * 5 + 8
        h = max(lh, rh, 40)

        for x, title, items in (
            (LM, left_title, left_items),
            (LM + w + gap, right_title, right_items),
        ):
            self.set_draw_color(0, 0, 0)
            self.rect(x, y0, w, h)
            self.set_xy(x + 3, y0 + 3)
            self.set_font("TNR", "B", 10)
            self.cell(w - 6, 5, title)
            self.set_xy(x + 3, y0 + 10)
            self.set_font("TNR", "", 9)
            for t in items:
                self.set_x(x + 3)
                self.multi_cell(w - 6, 4.5, f"- {t}")
        self.set_y(y0 + h + 4)

    def table(self, headers, rows, col_widths=None):
        self._x()
        if col_widths is None:
            col_widths = [CW / len(headers)] * len(headers)
        else:
            s = sum(col_widths)
            col_widths = [w * CW / s for w in col_widths]

        def header_row():
            self.set_font("AR", "B", 8)
            self.set_fill_color(230, 230, 230)
            self.set_text_color(0, 0, 0)
            self._x()
            for i, h in enumerate(headers):
                self.cell(col_widths[i], 7, h, border=1, fill=True, align="C")
            self.ln()

        # Avoid orphan header: need room for header + at least one data row.
        if self.get_y() + 20 > 270:
            self.add_page()

        header_row()
        self.set_font("AR", "", 8)
        for row in rows:
            max_h = 7
            for i, cell in enumerate(row):
                lines = self.multi_cell(
                    col_widths[i] - 2, 4.0, str(cell), dry_run=True, output="LINES"
                )
                max_h = max(max_h, len(lines) * 4.0 + 2)
            if self.get_y() + max_h > 270:
                self.add_page()
                header_row()
                self.set_font("AR", "", 8)
            x0, y0 = LM, self.get_y()
            for i, cell in enumerate(row):
                self.set_draw_color(0, 0, 0)
                self.rect(x0 + sum(col_widths[:i]), y0, col_widths[i], max_h)
                self.set_xy(x0 + sum(col_widths[:i]) + 1, y0 + 1)
                self.multi_cell(col_widths[i] - 2, 4.0, str(cell))
            self.set_y(y0 + max_h)
        self.ln(3)

    def remaining(self) -> float:
        """mm left before footer zone."""
        return 272 - self.get_y()

    def ui_remaining(self) -> float:
        """Tighter bottom bound for screenshot pages (closer to footer)."""
        return 285 - self.get_y()

    def ui_figure(self, path: Path, caption: str, max_h: float = 95):
        """Full-width UI screenshot that fills the reserved vertical slot."""
        self._x()
        if not path.exists():
            self.note_box(f"[Missing figure: {path.name}]")
            return
        caption_h = 6.0
        w = CW
        h = min(max_h, self.ui_remaining() - caption_h - 2)
        if h < 48:
            self.add_page()
            h = min(max_h, self.ui_remaining() - caption_h - 2)
        h = max(48.0, h)
        x = LM
        y = self.get_y()
        self.set_draw_color(210, 210, 210)
        self.set_line_width(0.3)
        self.rect(x - 0.5, y - 0.5, w + 1, h + 1)
        self.image(str(path), x=x, y=y, w=w, h=h)
        self.set_y(y + h + 1.0)
        self.caption(caption)

    def figure(self, path: Path, caption: str, max_h=130, min_h=55):
        """Place figure using remaining space; page-break only if it will not fit."""
        from PIL import Image as PILImage

        self._x()
        if not path.exists():
            self.note_box(f"[Missing figure: {path.name}]")
            return
        with PILImage.open(path) as im:
            pw, ph = im.size
        aspect = ph / float(pw)

        caption_h = 8
        pad = 5
        # Prefer full content width; clamp height
        w = CW
        h = w * aspect
        if h > max_h:
            h = max_h
            w = h / aspect

        need = h + caption_h + pad
        space = self.remaining()
        if need > space:
            # try shrink to remaining before breaking
            if space >= min_h + caption_h + pad:
                h = space - caption_h - pad
                w = h / aspect
                if w > CW:
                    w = CW
                    h = w * aspect
                    if h > space - caption_h - pad:
                        self.add_page()
                        w, h = CW, min(max_h, CW * aspect)
                        if h > max_h:
                            h = max_h
                            w = h / aspect
            else:
                self.add_page()
                w = CW
                h = w * aspect
                if h > max_h:
                    h = max_h
                    w = h / aspect

        x = LM + (CW - w) / 2
        y = self.get_y()
        self.set_draw_color(200, 200, 200)
        self.set_line_width(0.35)
        self.rect(x - 1, y - 1, w + 2, h + 2)
        self.image(str(path), x=x, y=y, w=w, h=h)
        self.set_y(y + h + 2)
        self.caption(caption)

    def code_block(self, text: str):
        self._x()
        self.set_fill_color(248, 248, 248)
        self.set_draw_color(0, 0, 0)
        self.set_font("Courier", "", 8)
        lines = text.split("\n")
        h = len(lines) * 4.0 + 6
        if self.get_y() + h > 270:
            self.add_page()
        y = self.get_y()
        self.rect(LM, y, CW, h, style="D")
        self.set_xy(LM + 2, y + 2)
        for line in lines:
            self.cell(CW - 4, 4.0, line[:92])
            self.ln(4.0)
            self.set_x(LM + 2)
        self.set_y(y + h + 3)
        self.set_font("TNR", "", 10)


def prepare_report_shot(src: Path, dest: Path, max_hw: float = 0.70) -> Path:
    """Crop tall captures from the top so report figures stay wide (fill page width)."""
    from PIL import Image as PILImage

    dest.parent.mkdir(parents=True, exist_ok=True)
    with PILImage.open(src) as im:
        rgb = im.convert("RGB")
        w, h = rgb.size
        if h / float(w) > max_hw:
            rgb = rgb.crop((0, 0, w, max(1, int(w * max_hw))))
        rgb.save(dest, optimize=True)
    return dest


def build():
    print("Generating diagrams...")
    diagrams = generate_all()

    pdf = Report()
    pdf.alias_nb_pages()

    # ===================== COVER =====================
    # Even vertical rhythm from logo → title → course → submitted boxes.
    # Removed "partial fulfillment" footer line (not needed on this cover).
    pdf.add_page()
    pdf._cover = True
    pdf.set_auto_page_break(auto=False)

    if LOGO.exists():
        logo_w = 30.0
        logo_h = logo_w * 1024 / 902
        pdf.image(str(LOGO), x=(210 - logo_w) / 2, y=12, w=logo_w, h=logo_h)
        pdf.set_y(12 + logo_h + 2)
    else:
        pdf.set_y(16)

    pdf.set_x(LM)
    pdf.set_font("TNR", "B", 12.5)
    pdf.cell(CW, 6, "Khulna University of Engineering & Technology", align="C", new_x="LMARGIN", new_y="NEXT")
    pdf.set_font("TNR", "", 10.5)
    pdf.cell(CW, 5.5, "Department of Computer Science and Engineering", align="C", new_x="LMARGIN", new_y="NEXT")
    pdf.ln(4)

    y = pdf.get_y()
    pdf.set_draw_color(0, 0, 0)
    pdf.set_line_width(0.85)
    pdf.line(LM + 10, y, 210 - RM - 10, y)
    pdf.set_y(y + 4)
    pdf.set_font("TNR", "", 11)
    pdf.cell(CW, 5.5, "Final Project Report", align="C")
    pdf.ln(6)
    pdf.set_font("TNR", "B", 24)
    pdf.cell(CW, 10, "FixFlow", align="C")
    pdf.ln(9)
    pdf.set_font("TNR", "B", 13)
    pdf.multi_cell(CW, 6, "Electronic Device Repair Management System", align="C")
    pdf.ln(2)
    pdf.set_font("TNR", "", 9.5)
    pdf.multi_cell(
        CW,
        4.5,
        "A role-based web application for repair request, diagnosis, invoicing,\n"
        "payment, fulfillment, and reporting using Laravel.",
        align="C",
    )
    pdf.ln(3)
    y = pdf.get_y()
    pdf.line(LM + 10, y, 210 - RM - 10, y)
    pdf.ln(5)

    # Course information
    meta = [
        ("Course", "CSE 3100 — Web Programming Laboratory"),
        ("Lab Group", "B2"),
        ("Submission Date", "19 July 2026"),
        ("Technology Stack", "Laravel 13 · PHP 8.3+ · PostgreSQL · Stripe · Reverb"),
    ]
    meta_y = pdf.get_y()
    meta_h = 8 + len(meta) * 5.6 + 3
    pdf.set_line_width(0.4)
    pdf.rect(LM, meta_y, CW, meta_h)
    pdf.set_xy(LM, meta_y + 2)
    pdf.set_font("TNR", "B", 10.5)
    pdf.cell(CW, 5.5, "Course Information", align="C")
    pdf.ln(6)
    for label, value in meta:
        pdf.set_x(LM + 8)
        pdf.set_font("TNR", "B", 9.5)
        pdf.cell(40, 5.3, label)
        pdf.set_font("TNR", "", 9.5)
        pdf.cell(CW - 56, 5.3, value)
        pdf.ln(5.3)
    pdf.set_y(meta_y + meta_h + 5)

    # Project highlights fill the middle with useful content (not empty space)
    pdf.set_font("TNR", "B", 10.5)
    pdf.cell(CW, 5.5, "Project Highlights", align="C")
    pdf.ln(6)
    for item in [
        "Multi-role portals for Customer, Technician, and Admin",
        "Repair lifecycle with diagnosis, quote approve/decline, and status timeline",
        "Stripe Checkout payments, invoices, fulfillment, and warranties",
        "Realtime per-repair chat (Laravel Reverb) and admin reports",
    ]:
        pdf.set_x(LM + 14)
        pdf.set_font("TNR", "", 9.5)
        pdf.multi_cell(CW - 28, 5, f"•  {item}")
        pdf.ln(1.2)
    pdf.ln(4)

    # SUBMITTED TO / BY
    gap_x = 5
    col_w = (CW - gap_x) / 2
    left_x, right_x = LM, LM + col_w + gap_x
    y = pdf.get_y()
    submit_h = 56.0

    def draw_submit_box(x: float, title: str, blocks: list[list[str]]):
        pdf.set_line_width(0.4)
        pdf.rect(x, y, col_w, submit_h)
        pdf.set_xy(x, y + 3.5)
        pdf.set_font("TNR", "B", 10.5)
        pdf.cell(col_w, 5.5, title, align="C")
        pdf.ln(6.5)
        for bi, lines in enumerate(blocks):
            if bi:
                pdf.ln(3)
            for li, line in enumerate(lines):
                pdf.set_x(x + 4)
                pdf.set_font("TNR", "B" if li == 0 else "", 10 if li == 0 else 9.5)
                pdf.multi_cell(col_w - 8, 4.4, line, align="C")

    draw_submit_box(
        left_x,
        "SUBMITTED TO",
        [
            ["Mr. Kazi Saeed Alam", "Assistant Professor", "Department of CSE, KUET"],
            ["Ehsanul Karim Talha", "Lecturer", "Department of CSE, KUET"],
        ],
    )
    draw_submit_box(
        right_x,
        "SUBMITTED BY",
        [
            ["Mynul Hassan Mehadi", "Roll: 2207105", "Lab Group: B2", "Department of CSE, KUET"],
        ],
    )
    pdf.set_auto_page_break(auto=True, margin=BM + 4)

    # ===================== ABSTRACT =====================
    # Keep _cover True until this page's header runs (avoids footer on cover).
    pdf.set_chapter("FixFlow Final Report", "Abstract", "CSE 3100 · Web Programming Laboratory")
    pdf.add_page()
    pdf.section_title("Abstract")
    pdf.p(
        "This project presents FixFlow, a web-based electronic device repair management system. "
        "The main goal is to digitize the full repair lifecycle so customers, technicians, and "
        "administrators share one platform instead of paper tickets, phone calls, and disconnected billing."
    )
    pdf.p(
        "On the customer side, a user can submit a repair request, track status on a timeline, chat "
        "with the assigned technician, pay an invoice online (Stripe) or via admin-recorded payment, "
        "choose pickup or home delivery after payment, and view warranties. Technicians update "
        "diagnosis and status on assigned jobs. Admins assign technicians, approve applicants, "
        "review and send invoices, confirm fulfillment, issue warranties, and view reports."
    )
    pdf.p(
        "The application is built with Laravel 13 (MVC), Blade + Tailwind CSS, PostgreSQL, Laravel Reverb "
        "for live chat, and Stripe Checkout for payments. Schema is managed with migrations and "
        "seeders; authorization uses session auth with role and approved-technician middleware. "
        "Core flows were verified with feature tests and seeded demo accounts."
    )
    pdf.p(
        "From a software-engineering view, FixFlow emphasizes clear domain boundaries: repair "
        "requests own status and diagnosis; invoices and warranties stay 1:1 with a completed "
        "repair; messages belong to a repair thread; technician applications gate privileged "
        "access. Business rules for assignment, payment, fulfillment, and warranty issue live "
        "in controllers and model helpers so the UI cannot bypass workflow constraints."
    )
    pdf.p(
        "The external integration used in production-like demos is Stripe Checkout (test mode): "
        "customers pay unpaid invoices through a hosted session, and webhooks or success "
        "callbacks mark invoices paid. Realtime chat uses self-hosted Laravel Reverb rather "
        "than a hosted Pusher account. Email for password reset can run through local Mailpit "
        "during development or any SMTP provider in deployment."
    )
    pdf.ln(2)
    pdf.metric_grid(
        [
            ("8", "Core tables"),
            ("3", "User roles"),
            ("12", "Controllers"),
            ("10", "Design figures"),
            ("1:1", "Invoice / repair"),
            ("Reverb", "Live chat"),
            ("Stripe", "Online pay"),
            ("MVC", "Architecture"),
        ]
    )
    pdf.ln(2)
    pdf.keywords_box(
        "Laravel, MVC, Eloquent, Blade, PostgreSQL, ER model, DFD, UML, Stripe, "
        "WebSockets, role-based access, repair workflow, invoicing, fulfillment."
    )
    pdf.h2("Project Contribution Summary")
    pdf.p(
        "The delivered system demonstrates an end-to-end academic web application: requirement "
        "analysis, conceptual and physical data modeling, MVC implementation, role-based "
        "security, payment and realtime integrations, and verification with seeded demo data "
        "and automated feature tests. The report documents architecture, DFDs, ER/UML diagrams, "
        "module maps, and deployment steps so the design can be reproduced and extended."
    )
    pdf.two_col_boxes(
        "Deliverables",
        [
            "Working Laravel web app with three roles.",
            "Migrations, seeders, and demo accounts.",
            "Design report with ER, DFD, and UML figures.",
            "Stripe test-mode payment path and webhooks.",
            "Reverb-backed live chat per repair.",
        ],
        "Evaluation Focus",
        [
            "Correct repair lifecycle transitions.",
            "Invoice draft → sent → paid integrity.",
            "Role and technician-approval gates.",
            "Fulfillment only after payment.",
            "Readable admin reports and audits.",
        ],
    )

    # ===================== TOC =====================
    pdf.set_chapter("FixFlow Final Report", "Contents", "CSE 3100 · Web Programming Laboratory")
    pdf.add_page()
    pdf.section_title("Table of Contents")

    toc = [
        ("01", "Introduction and Objectives", "4"),
        ("02", "Requirements and Actors", "5"),
        ("03", "UML Use-Case Diagram", "6"),
        ("04", "System Architecture", "7"),
        ("05", "Data Flow Diagrams (L0/L1)", "7"),
        ("06", "Entity-Relationship Diagram", "8"),
        ("07", "Physical Schema Design", "9"),
        ("08", "Module Design", "10"),
        ("09", "Security and Validation", "11"),
        ("10", "Activity and Sequence Diagrams", "12"),
        ("11", "Class Diagram", "13"),
        ("12", "Testing and Verification", "13"),
        ("13", "Deployment Guide", "14"),
        ("14", "Conclusion and References", "14"),
        ("A", "Demo Accounts", "15"),
        ("B", "Route Map", "15"),
        ("C", "UI Screenshots", "18"),
    ]
    col_w = (CW - 8) / 2
    y_start = pdf.get_y()
    for i, (num, title, page) in enumerate(toc):
        col = i % 2
        row = i // 2
        x = LM + col * (col_w + 8)
        y = y_start + row * 7
        pdf.toc_row(x, y, col_w, num, title, page)

    pdf.set_y(y_start + ((len(toc) + 1) // 2) * 7 + 6)
    pdf.h2("List of Figures")
    figures = [
        ("Fig. 3.1", "UML Use-Case Diagram", "6"),
        ("Fig. 4.1", "Three-layer Architecture", "7"),
        ("Fig. 5.1", "DFD Level 0 (Context)", "7"),
        ("Fig. 5.2", "DFD Level 1", "8"),
        ("Fig. 6.1", "Entity-Relationship Diagram", "8"),
        ("Fig. 10.1", "Repair Lifecycle Activity", "12"),
        ("Fig. 10.2", "Live Chat Sequence", "12"),
        ("Fig. 10.3", "Stripe Payment Sequence", "12"),
        ("Fig. 11.1", "Domain Class Diagram", "13"),
        ("Fig. C.1-8", "UI Screenshots (Appendix C)", "18"),
    ]
    for num, title, page in figures:
        if pdf.remaining() < 10:
            pdf.add_page()
        pdf.toc_row(LM, pdf.get_y(), CW, num, title, page)
        pdf.ln(6.5)

    pdf.h2("List of Tables")
    tables = [
        ("Tbl. 3.1", "Use-Case Summary", "6"),
        ("Tbl. 4.1", "Architecture Layers", "7"),
        ("Tbl. 5.1", "DFD Process Map", "8"),
        ("Tbl. 7.1", "Core Database Tables", "9"),
        ("Tbl. 7.2", "Cardinality Summary", "9"),
        ("Tbl. 8.1", "Controller Map", "10"),
        ("Tbl. 9.1", "Security Controls", "11"),
        ("Tbl. 12.1", "Test Coverage", "13"),
    ]
    for num, title, page in tables:
        if pdf.remaining() < 10:
            pdf.add_page()
        pdf.toc_row(LM, pdf.get_y(), CW, num, title, page)
        pdf.ln(6.5)

    if pdf.remaining() > 22:
        pdf.note_box(
            "Document conventions: demo password is password for all seeded accounts; "
            "Stripe runs in test mode; repository at github.com/mehadi105/FixFLow."
        )

    # ===================== CH 1 =====================
    pdf.set_chapter("Chapter 1", "Introduction", "FixFlow · Introduction and Objectives")
    pdf.add_page()
    pdf.section_title("1. Introduction and Objectives")
    pdf.h2("1.1 Problem Statement")
    pdf.p(
        "Traditional repair shops often rely on paper tickets, phone calls, and manual billing. "
        "Customers lack real-time visibility into repair progress; technicians need clear assigned "
        "jobs and status transitions; admins need centralized control over users, invoices, and "
        "revenue. Payment and device return are frequently disconnected from the repair ticket, "
        "and communication usually happens off-platform."
    )
    pdf.p(
        "These gaps create repeated status inquiries, delayed payments, lost paper records, and "
        "unclear responsibility when a device is ready for pickup or delivery. Without a shared "
        "system of record, shops also struggle to produce simple operational reports such as "
        "open jobs, unpaid invoices, or technician workload."
    )
    pdf.h2("1.2 Proposed Solution")
    pdf.p(
        "FixFlow covers the repair cycle in software: submit a request, assign an approved technician, "
        "update diagnosis and status, auto-create a draft invoice on completion, admin send/pay "
        "(Stripe or manual), choose pickup or delivery, confirm fulfillment, issue warranty, chat "
        "per repair thread, and show admin reports. Authorization and workflow rules are enforced "
        "in Laravel middleware and model helpers, not only in the UI."
    )
    pdf.p(
        "Each actor sees a role-specific dashboard. Customers follow a timeline; technicians work "
        "only on assigned jobs; admins oversee applications, assignments, invoices, fulfillment, "
        "and warranties. Stripe Checkout is the online payment path; admin-recorded payments "
        "cover walk-in or cash cases. After payment, fulfillment and warranty steps close the loop."
    )
    pdf.two_col_boxes(
        "Primary Objectives",
        [
            "Secure multi-role authentication and dashboards.",
            "Full repair lifecycle with timeline tracking.",
            "Draft-to-paid invoicing with Stripe or manual pay.",
            "Pickup/delivery fulfillment after payment.",
            "Live messaging and unread notifications.",
            "Admin reports with joins and aggregates.",
        ],
        "Learning / Design Objectives",
        [
            "Model the system with ER, DFD, and UML.",
            "Apply MVC, migrations, Eloquent relations.",
            "Use middleware for roles and rate limits.",
            "Integrate payments and WebSocket chat.",
            "Demonstrate transactions and integrity rules.",
        ],
    )
    pdf.h2("1.3 Scope")
    pdf.table(
        ["Included", "Outside Current Scope"],
        [
            [
                "Customer/technician/admin auth; repair CRUD; assignment; status/diagnosis; draft invoices; Stripe/manual pay; fulfillment; warranties; chat; reports; technician applications; password reset.",
                "Native mobile apps, spare-parts inventory, GPS courier tracking, multi-branch SaaS billing, and production multi-tenant deployment.",
            ]
        ],
        [87, 87],
    )
    pdf.h2("1.4 Stakeholders and Success Criteria")
    pdf.p(
        "Primary stakeholders are customers (device owners), technicians (service providers), and "
        "admins (shop operators). Secondary stakeholders include course instructors evaluating "
        "design quality and future maintainers extending the codebase."
    )
    pdf.bullet("A customer can create a request, see status updates, chat, pay, and choose fulfillment.")
    pdf.bullet("A technician can update only assigned jobs and communicate inside the repair thread.")
    pdf.bullet("An admin can approve technicians, assign work, manage invoices, and view reports.")
    pdf.bullet("Data integrity holds: one invoice and at most one warranty per repair; paid before fulfillment.")
    pdf.bullet(
        "Success criteria: reproducible demo — migrate + seed, sign in with role accounts, "
        "walk a repair through quote/payment/fulfillment, and show matching design artifacts."
    )

    # ===================== CH 2 =====================
    pdf.set_chapter("Chapter 2", "Requirements", "FixFlow · Requirements and Actors")
    pdf.add_page()
    pdf.section_title("2. Requirements and Actors")

    pdf.h2("2.1 Customer Requirements")
    for i, t in enumerate(
        [
            "Register and sign in with role-based dashboard access.",
            "Create a repair request with device details and optional image.",
            "Track repair status on a timeline.",
            "Chat with participants on a repair thread.",
            "Pay unpaid invoices via Stripe Checkout.",
            "Choose pickup or home delivery after payment.",
            "View issued warranties.",
            "Reset password via emailed link.",
        ],
        1,
    ):
        pdf.numbered(i, t)

    pdf.h2("2.2 Technician Requirements")
    for i, t in enumerate(
        [
            "Apply to join as a technician and await admin approval.",
            "View assigned repair jobs on the technician dashboard.",
            "Update status (diagnosing, repairing, completed).",
            "Save diagnosis notes for a repair.",
            "Chat with the customer on assigned repairs.",
        ],
        1,
    ):
        pdf.numbered(i, t)

    pdf.h2("2.3 Administrator Requirements")
    for i, t in enumerate(
        [
            "Manage users and roles.",
            "Approve or reject technician applications.",
            "Assign approved technicians to pending repairs.",
            "Update any repair; review/send/delete draft invoices.",
            "Mark invoices paid; complete fulfillment.",
            "Issue warranties; view analytics reports.",
        ],
        1,
    ):
        pdf.numbered(i, t)

    pdf.h2("2.4 Non-Functional Requirements")
    pdf.table(
        ["Quality", "Requirement and Implementation"],
        [
            ["Integrity", "Migrations, FKs, unique constraints; one invoice/warranty per repair."],
            ["Security", "CSRF, hashed passwords, role middleware, rate limiting, Stripe webhook signature."],
            ["Reliability", "Stripe success URL + webhook dual path; session regenerate on login."],
            ["Usability", "Responsive Blade UI, status timeline, inbox-style messaging."],
            ["Performance", "Pagination, eager loading, AJAX badges, named throttle limiters."],
            ["Maintainability", "MVC separation, Invoice/Stripe services, seeders, feature tests."],
        ],
        [35, 139],
    )

    pdf.h2("2.5 Core Business Rules")
    rules = [
        "Role-scoped access",
        "Approved technician only",
        "Draft invoice on complete",
        "Customer cannot see drafts",
        "Pay then choose fulfillment",
        "Chat participants only",
        "1:1 invoice per repair",
        "1:1 warranty per repair",
    ]
    # chip row
    pdf.set_font("AR", "", 8)
    x, y = LM, pdf.get_y()
    for r in rules:
        tw = pdf.get_string_width(r) + 6
        if x + tw > 210 - RM:
            x = LM
            y += 9
        pdf.set_xy(x, y)
        pdf.set_draw_color(0, 0, 0)
        pdf.rect(x, y, tw, 7)
        pdf.cell(tw, 7, r, align="C")
        x += tw + 3
    pdf.set_y(y + 12)

    # ===================== CH 3 Use Case =====================
    pdf.set_chapter("Chapter 3", "UML Use Cases", "FixFlow · UML Use-Case Diagram")
    pdf.add_page()
    pdf.section_title("3. UML Use-Case Diagram")
    pdf.figure(
        diagrams["usecase"],
        "Figure 3.1 - Role-specific use cases inside the FixFlow system boundary.",
        max_h=125,
    )
    pdf.h2("3.1 Use-Case Summary")
    pdf.table(
        ["Actor", "Primary Use Cases", "System Effect"],
        [
            [
                "Customer",
                "Register/login, create repair, track, chat, pay, pickup/delivery, warranty",
                "Writes repairs, messages, payments, fulfillment choice",
            ],
            [
                "Technician",
                "Apply, view jobs, update status, diagnosis, chat",
                "Updates repairs; writes messages; application row",
            ],
            [
                "Admin",
                "Users, approve apps, assign, invoices, fulfillment, warranties, reports",
                "Writes users/invoices/warranties; reads analytics",
            ],
        ],
        [28, 78, 68],
    )

    # ===================== CH 4 Architecture =====================
    pdf.set_chapter("Chapter 4", "Architecture", "FixFlow · System Architecture")
    pdf.add_page()
    pdf.section_title("4. System Architecture")
    pdf.p(
        "FixFlow follows a classic three-layer web architecture: a Blade/Tailwind presentation "
        "layer, a Laravel application layer (controllers, middleware, services), and a PostgreSQL "
        "data layer with file storage for device images."
    )
    pdf.figure(
        diagrams["architecture"],
        "Figure 4.1 - Three-layer architecture and local ports (black-and-white).",
        max_h=78,
    )
    pdf.table(
        ["Layer", "Key Technologies", "Responsibility"],
        [
            [
                "Frontend",
                "Blade, Tailwind CSS 4, Vite 8, Echo/pusher-js",
                "Role-specific screens, forms, chat UI, charts/badges.",
            ],
            [
                "Backend",
                "Laravel 13, PHP 8.3+, middleware, services",
                "Auth, authorization, validation, orchestration, Stripe.",
            ],
            [
                "Database",
                "PostgreSQL, Eloquent, Query Builder, migrations",
                "Persistence, constraints, aggregates, seed data.",
            ],
            [
                "Realtime / Pay",
                "Laravel Reverb, Stripe Checkout",
                "Live chat events; card payments in test mode.",
            ],
        ],
        [28, 70, 76],
    )
    pdf.h2("4.1 Local Runtime Ports")
    pdf.bullet("Web app: php artisan serve → http://127.0.0.1:8000")
    pdf.bullet("Vite HMR (dev): npm run dev → http://localhost:5173")
    pdf.bullet("Reverb WebSockets: php artisan reverb:start → port 8080")
    pdf.bullet("PostgreSQL: 127.0.0.1:5432, database fixflow")
    pdf.note_box(
        "PostgreSQL is the default database. Reverb and Mailpit are optional local processes "
        "for live chat and password-reset email. Stripe runs in test mode with webhook/success confirmation."
    )

    # ===================== CH 5 DFD =====================
    # Wide/short DFD assets: keep Level 0 + Level 1 + store map on a single full page.
    pdf.set_chapter("Chapter 5", "Data Flow Diagrams", "FixFlow · Data Flow Diagrams")
    pdf.add_page()
    pdf.section_title("5. Data Flow Diagrams - Level 0 and Level 1")
    pdf.h2("5.1 Level 0 - Context")
    pdf.p(
        "Customer, Technician, Admin, Stripe, and Email exchange data with FixFlow; "
        "role access and chat participation are enforced inside the app."
    )
    pdf.figure(diagrams["dfd0"], "Figure 5.1 - Context diagram (Level 0).", max_h=54, min_h=45)

    pdf.h2("5.2 Level 1 - Major Processes")
    pdf.p(
        "Auth, Repairs, Tech Work, Invoice/Pay, Fulfillment, Messaging, Reports, and Tech Apps; "
        "stores D1-D5 map to core tables."
    )
    pdf.figure(
        diagrams["dfd1"],
        "Figure 5.2 - Level-1 processes and data stores (D1-D5 map to core tables).",
        max_h=60,
        min_h=48,
    )
    pdf.h2("5.3 Process-to-Store Map")
    pdf.table(
        ["Process", "Writes / Reads", "Store"],
        [
            ["1.0 Auth", "login, session", "D1"],
            ["2.0-3.0 Repairs / Tech", "requests, status, diagnosis", "D2"],
            ["4.0-5.0 Invoice / Fulfill", "invoices, payments, delivery", "D3"],
            ["6.0 Messaging", "chat messages, read state", "D4"],
            ["7.0-8.0 Reports / Apps", "analytics, applications", "D5"],
        ],
        [40, 90, 30],
    )

    # ===================== CH 6 ERD =====================
    pdf.set_chapter("Chapter 6", "ER Diagram", "FixFlow · ER Diagram")
    pdf.add_page()
    pdf.section_title("6. Entity-Relationship Diagram")
    pdf.p(
        "ER diagram from the live PostgreSQL schema (DBML / dbdiagram.io). Yellow = PK; teal = FK. "
        "Quote columns on REPAIR_REQUESTS support post-diagnosis approve/decline."
    )
    erd_path = ASSETS / "erd_dbdiagram.png"
    # Reserve space for entity table + integrity note on the same page.
    pdf.figure(
        erd_path if erd_path.exists() else diagrams["erd"],
        "Figure 6.1 - FixFlow ER diagram (PostgreSQL domain schema).",
        max_h=max(100.0, pdf.remaining() - 95),
        min_h=88,
    )
    pdf.h2("6.1 Entity Overview")
    pdf.table(
        ["Entity", "Role in Domain", "Key Relationship"],
        [
            ["USERS", "Identity and role", "1:N repairs as customer or technician"],
            ["REPAIR_REQUESTS", "Job ticket / lifecycle", "N:1 customer; N:1 technician (nullable)"],
            ["INVOICES", "Billing for a repair", "1:1 with repair_request"],
            ["WARRANTIES", "Post-repair guarantee", "1:1 with repair_request"],
            ["MESSAGES", "Per-repair chat", "N:1 repair; N:1 sender user"],
            ["TECHNICIAN_APPLICATIONS", "Onboarding gate", "1:1 with applicant user"],
        ],
        [48, 48, 78],
    )
    pdf.note_box(
        "Integrity: unique email and repair reference; unique repair_request_id on invoices and "
        "warranties (1:1); message threads indexed by (repair_request_id, created_at); user FKs "
        "cascade or null per migration policy."
    )

    # ===================== CH 7 Schema (dedicated page — avoid mid-chapter orphan) =====================
    pdf.set_chapter("Chapter 7", "Schema", "FixFlow · Physical Schema")
    pdf.add_page()
    pdf.section_title("7. Physical Schema and Relational Design")
    pdf.p(
        "Schema is versioned with Laravel migrations. Key decisions: unique email and repair reference; "
        "unique repair_request_id on invoices and warranties; composite index on messages "
        "(repair_request_id, created_at); cascade / nullOnDelete on user FKs."
    )
    pdf.h2("7.1 Core Tables")
    pdf.table(
        ["Table", "Key Columns", "Constraints"],
        [
            ["users", "id, name, email, role, password", "email unique; role default customer"],
            ["repair_requests", "reference, user_id, technician_id, status", "FKs to users; reference unique"],
            ["invoices", "repair_request_id, totals, payment_status", "repair_request_id unique"],
            ["warranties", "repair_request_id, warranty_code, dates", "repair_request_id unique"],
            ["messages", "repair_request_id, user_id, body, read_at", "index on RR + created_at"],
            ["technician_applications", "user_id, phone, status, reviewed_by", "user_id unique"],
            ["password_reset_tokens", "email, token, created_at", "email PK"],
            ["sessions", "id, user_id, payload, last_activity", "session driver storage"],
        ],
        [38, 82, 56],
    )

    pdf.two_col_boxes(
        "7.2 Status Domains",
        [
            "Repair: pending, assigned, diagnosing, repairing, completed",
            "Invoice: draft, unpaid, paid",
            "Fulfillment: awaiting_invoice through fulfilled",
            "Application: pending, approved, rejected",
        ],
        "7.3 Design Notes",
        [
            "One invoice and one warranty per repair (unique FK).",
            "Chat indexed by repair_request_id + created_at.",
            "Technician applications bound 1:1 to user.",
            "Cascade / nullOnDelete on user foreign keys.",
        ],
    )

    pdf.h2("7.4 Cardinality Summary")
    pdf.table(
        ["Relationship", "Cardinality", "Notes"],
        [
            ["User (customer) - RepairRequest", "1 : N", "user_id FK cascade"],
            ["User (technician) - RepairRequest", "1 : N", "technician_id nullable"],
            ["RepairRequest - Invoice", "1 : 1", "unique repair_request_id"],
            ["RepairRequest - Warranty", "1 : 1", "unique repair_request_id"],
            ["RepairRequest - Message", "1 : N", "chat thread per repair"],
            ["User - TechnicianApplication", "1 : 1", "unique user_id"],
        ],
        [70, 35, 69],
    )

    # ===================== CH 8 Modules =====================
    pdf.set_chapter("Chapter 8", "Modules", "FixFlow · Module Design")
    pdf.add_page()
    pdf.section_title("8. Module Design and Implementation")
    modules = [
        (
            "8.1 Authentication and Authorization",
            "Session login/register, PasswordResetController, EnsureUserHasRole, "
            "EnsureApprovedTechnician, and named rate limiters.",
        ),
        (
            "8.2 Repair Requests",
            "Create with optional image; admin assignment; status/diagnosis updates; "
            "show page with timeline, invoice/warranty cards, and chat link.",
        ),
        (
            "8.3 Technician Applications",
            "Public apply form creates technician user + pending application; "
            "admin approve/reject with notes.",
        ),
        (
            "8.4 Invoicing and Payments",
            "InvoiceService creates drafts on completion; admin send/edit/delete; "
            "StripePaymentService + webhook/success mark paid; manual mark-paid supported.",
        ),
        (
            "8.5 Fulfillment",
            "After payment, customer chooses pickup or delivery; admin confirms handover to fulfilled.",
        ),
        (
            "8.6 Messaging and Notifications",
            "Per-repair inbox; MessageSent on private channels; Echo + polling fallback; "
            "unread badges and navbar notification feed.",
        ),
        (
            "8.7 Warranties and Reports",
            "Admin-issued warranties; Eloquent aggregates plus DB::table()->leftJoin() technician report.",
        ),
    ]
    for title, body in modules:
        pdf.h2(title)
        pdf.p(body)

    pdf.h2("8.8 Controllers")
    pdf.table(
        ["Controller", "Responsibility"],
        [
            ["DashboardController", "Role dashboards and /dashboard redirect"],
            ["RepairRequestController", "Repairs, assign, status, diagnosis, fulfillment"],
            ["InvoiceController", "Draft/send/update/delete/mark-paid"],
            ["InvoicePaymentController", "Stripe checkout, success/cancel, webhook"],
            ["MessageController", "Inbox + JSON chat APIs"],
            ["WarrantyController", "List/issue warranties"],
            ["ReportController", "Admin analytics + joins"],
            ["UserController", "User list and role updates"],
            ["TechnicianApplicationController", "Apply/status/approve/reject"],
            ["PasswordResetController", "Forgot/reset password"],
            ["NotificationController", "Navbar notification JSON"],
            ["PreferenceController", "Table density cookie"],
        ],
        [65, 109],
    )

    # ===================== CH 9 Security =====================
    pdf.set_chapter("Chapter 9", "Security", "FixFlow · Security and Validation")
    pdf.add_page()
    pdf.section_title("9. Security, Validation, and Error Handling")
    pdf.h2("9.1 Authentication and Session Security")
    for t in [
        "CSRF tokens on state-changing forms; AJAX sends X-CSRF-TOKEN.",
        "Session regenerate on login/register; invalidate + regenerateToken on logout.",
        "Passwords hashed via Laravel hashed cast / Hash::make.",
        "HttpOnly session cookie; SameSite lax; Secure optional in production.",
    ]:
        pdf.bullet(t)

    pdf.h2("9.2 Authorization and Access Control")
    for t in [
        "Role middleware prevents privilege escalation across portals.",
        "Approved-technician middleware blocks unapproved tech dashboards.",
        "Chat channel authorization via RepairRequest::hasChatParticipant.",
        "Customers cannot view draft invoices until admin sends them.",
    ]:
        pdf.bullet(t)

    pdf.h2("9.3 Rate Limiting and External Integrations")
    for t in [
        "Rate limiting on login, registration, password reset, AJAX, and message send.",
        "Stripe webhook verifies signature; CSRF excluded only for webhook path.",
    ]:
        pdf.bullet(t)

    pdf.h2("9.4 Validation and Error Handling")
    pdf.table(
        ["Area", "Validation / Control", "On Failure"],
        [
            ["Forms", "Blade CSRF + server-side rules in controllers", "422/redirect with errors"],
            ["Auth", "Credential check + throttle limiters", "401/429 with safe message"],
            ["Roles", "EnsureUserHasRole / EnsureApprovedTechnician", "403 abort"],
            ["Payments", "Stripe signature + session_id check", "Reject webhook; no state change"],
            ["Chat", "Participant check + repair ownership", "403 on private channel"],
            ["Invoices", "Draft visibility + payable state guards", "403/404 as appropriate"],
        ],
        [28, 78, 68],
    )
    pdf.note_box(
        "Errors are surfaced through Laravel validation responses, HTTP abort codes, and "
        "flash messages in Blade views so users receive clear feedback without exposing internals."
    )

    # ===================== CH 10 Activity / Sequence =====================
    pdf.set_chapter("Chapter 10", "Flows", "FixFlow · Activity and Sequence")
    pdf.add_page()
    pdf.section_title("10. Activity and Sequence Diagrams")
    pdf.h2("10.1 Repair Lifecycle Activity")
    pdf.figure(
        diagrams["activity"],
        "Figure 10.1 - Happy-path activity from request creation to fulfillment.",
        max_h=58,
    )

    pdf.h2("10.2 Sequence: Live Chat")
    pdf.figure(
        diagrams["seq_chat"],
        "Figure 10.2 - Chat message broadcast and unread polling.",
        max_h=52,
    )

    pdf.h2("10.3 Sequence: Stripe Payment")
    pdf.figure(
        diagrams["seq_stripe"],
        "Figure 10.3 - Stripe Checkout (success URL + signed webhook both mark paid).",
        max_h=58,
    )

    # ===================== CH 11 Class =====================
    pdf.set_chapter("Chapter 11", "Class Diagram", "FixFlow · Class Diagram")
    pdf.add_page()
    pdf.section_title("11. Class Diagram and Domain Models")
    pdf.p(
        "Domain classes mirror Eloquent models under app/Models, including attributes, "
        "relationship methods, and key helpers used by controllers and policies."
    )
    pdf.figure(
        diagrams["class"],
        "Figure 11.1 - UML class diagram of core domain models.",
        max_h=115,
    )
    if pdf.remaining() > 40:
        pdf.h2("11.1 Key Helpers")
        pdf.bullet("User::isApprovedTechnician() gates technician dashboard access.")
        pdf.bullet("RepairRequest::hasChatParticipant() authorizes private chat channels.")
        pdf.bullet("RepairRequest::canChooseFulfillment() enables pickup/delivery after pay.")
        pdf.bullet("Invoice::isDraft / isPayable control admin send and customer pay actions.")

    # ===================== CH 12 Testing =====================
    pdf.begin_chapter("Chapter 12", "Testing", "FixFlow · Testing and Verification", min_remain=90)
    pdf.section_title("12. Testing and Verification")
    pdf.p(
        "Feature tests under tests/Feature cover response headers, login rate limiting, "
        "joined report data, preference cookies, and draft invoice deletion. Manual showcase "
        "flows cover chat, Stripe test payments, and fulfillment."
    )
    pdf.table(
        ["Test Focus", "Approach"],
        [
            ["Smoke", "GET / returns 200"],
            ["Trace headers", "X-Request-ID / X-FixFlow-App asserted"],
            ["Rate limit", "11th failed login returns 429"],
            ["Reports join", "Admin sees technician via leftJoin query"],
            ["Cookie", "POST preferences queues ff_table_density"],
            ["DELETE", "Draft invoice deleted; unpaid invoice protected"],
            ["Manual E2E", "Seeded demos RR-DEMO-PAY / RR-DEMO-PAY-2"],
        ],
        [40, 134],
    )

    # ===================== CH 13 Deploy =====================
    pdf.begin_chapter("Chapter 13", "Deployment", "FixFlow · Deployment and Demo", min_remain=85)
    pdf.section_title("13. Deployment and Demonstration")
    pdf.h2("13.1 Local Setup")
    pdf.code_block(
        """composer install && npm install
cp .env.example .env && php artisan key:generate
# Configure DB_* for PostgreSQL in .env, then:
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
php artisan reverb:start
mailpit"""
    )
    pdf.h2("13.2 Showcase Script")
    for t in [
        "Admin: approve applicant; assign pending repair.",
        "Technician: update status/diagnosis; reply in Messages.",
        "Customer: open Messages; view progress threads.",
        "Admin: send RR-DEMO-PAY draft; customer pays RR-DEMO-PAY-2 (card 4242...).",
        "Customer: choose pickup/delivery; admin mark fulfilled; show Reports.",
    ]:
        pdf.bullet(t)

    # ===================== CH 14 Conclusion =====================
    pdf.begin_chapter("Chapter 14", "Conclusion", "FixFlow · Conclusion and References", min_remain=70)
    pdf.section_title("14. Conclusion and References")
    pdf.p(
        "FixFlow demonstrates a complete academic web system for electronic device repair "
        "management. It integrates authentication, role-based access, repair workflow, Stripe "
        "invoicing, fulfillment, warranties, live chat, and reporting in a coherent Laravel MVC "
        "application. The design artifacts in this report (DFD, ERD, UML use case, activity, "
        "sequence, and class diagrams) map directly to the implemented codebase."
    )
    pdf.h2("14.1 References")
    refs = [
        "Laravel Documentation. https://laravel.com/docs",
        "Stripe Checkout Documentation. https://stripe.com/docs/payments/checkout",
        "Laravel Reverb. https://laravel.com/docs/reverb",
        "Somerville, I. Software Engineering. Pearson.",
        "Elmasri, R. & Navathe, S. Fundamentals of Database Systems.",
    ]
    for i, r in enumerate(refs, 1):
        pdf.numbered(i, r)

    # ===================== Appendix A =====================
    pdf.begin_chapter("Appendix A", "Demo Accounts", "FixFlow · Demo Accounts", min_remain=90)
    pdf.section_title("A. Demo Accounts and Seed Data")
    pdf.p("Password for all seeded accounts: password")
    pdf.table(
        ["Role", "Email", "Notes"],
        [
            ["Admin", "admin@fixflow.test", "Full system access"],
            ["Customer", "customer@fixflow.test", "John Customer; seeded repairs/chat"],
            ["Technician", "technician@fixflow.test", "Mike Torres (approved)"],
            ["Technician", "technician2@fixflow.test", "Lisa Chen (approved)"],
            ["Technician", "technician3@fixflow.test", "David Park (approved)"],
            ["Applicant", "applicant@fixflow.test", "Pending admin approval"],
        ],
        [32, 70, 72],
    )
    pdf.h2("Payment Demo Repairs")
    pdf.bullet("RR-DEMO-PAY: completed + draft invoice ($230) for admin send flow.")
    pdf.bullet("RR-DEMO-PAY-2: completed + unpaid invoice ($150) for immediate Stripe pay.")

    # ===================== Appendix B =====================
    pdf.begin_chapter("Appendix B", "Routes", "FixFlow · Route Map", min_remain=100)
    pdf.section_title("B. Route Map and Repository")
    pdf.table(
        ["Method", "Path", "Purpose"],
        [
            ["GET", "/", "Marketing home"],
            ["GET/POST", "/login, /register", "Auth"],
            ["GET/POST", "/forgot-password, /reset-password", "Password reset"],
            ["GET", "/dashboard/*", "Role dashboards"],
            ["*", "/repair-requests...", "Repair lifecycle"],
            ["GET", "/messages, /messages/{rr}", "Inbox"],
            ["GET/POST", "/repair-requests/{rr}/messages*", "Chat API"],
            ["*", "/invoices...", "Invoice + Stripe"],
            ["GET", "/reports", "Admin analytics"],
            ["GET/POST", "/technician-applications...", "Tech hiring"],
            ["POST", "/stripe/webhook", "Stripe events"],
            ["POST", "/preferences/table-density", "UI cookie"],
        ],
        [28, 78, 68],
    )
    pdf.ln(4)
    pdf.note_box(
        "Repository: https://github.com/mehadi105/FixFLow\n"
        "UI screenshots of the running demo are included in Appendix C."
    )

    # ===================== Appendix C =====================
    pdf.set_chapter("Appendix C", "UI Screenshots", "FixFlow · User Interface")
    pdf.add_page()
    # Allow screenshot pages to pack closer to the footer.
    pdf.set_auto_page_break(auto=True, margin=12)
    pdf.section_title("C. User Interface Screenshots")
    pdf.p(
        "Screenshots from the local demo (http://127.0.0.1:8000) with seeded accounts. "
        "Tall captures are cropped to the primary viewport so each figure fills the page width."
    )

    report_shots = SCREENSHOTS / "_report"
    # Two full-width screenshots per page: (source, caption, explanation)
    ui_pages = [
        [
            (
                "01_landing.png",
                "Figure C.1 - Marketing home hero (landing page).",
                "Public marketing hero: product pitch, CTAs, live dashboard preview, and portal entry points.",
            ),
            (
                "02_login.png",
                "Figure C.2 - Sign-in screen.",
                "Session login for all roles; register as customer or apply as technician.",
            ),
        ],
        [
            (
                "03_admin_dashboard.png",
                "Figure C.3 - Admin dashboard.",
                "KPIs, recent repairs, and status doughnut (includes Quoted / Declined).",
            ),
            (
                "04_customer_dashboard.png",
                "Figure C.4 - Customer dashboard.",
                "Request totals, warranties, and recent repairs with View actions.",
            ),
        ],
        [
            (
                "05_create_repair.png",
                "Figure C.5 - Create repair request.",
                "Customer submits device details, priority, issue text, and optional image.",
            ),
            (
                "06_repair_detail.png",
                "Figure C.6 - Repair request detail (admin).",
                "Timeline, assignment, diagnosis/quote panel, invoice and warranty shortcuts.",
            ),
        ],
        [
            (
                "07_messages.png",
                "Figure C.7 - Live messages inbox.",
                "One chat thread per repair; realtime updates via Laravel Reverb.",
            ),
            (
                "08_reports.png",
                "Figure C.8 - Admin reports.",
                "Revenue, status mix, monthly volume, and technician completion rates.",
            ),
        ],
    ]

    for page_i, shots in enumerate(ui_pages):
        if page_i > 0:
            pdf.add_page()

        for i, (name, caption, explanation) in enumerate(shots):
            src = SCREENSHOTS / name
            # Taller crop ratio so full-width figures can fill vertical slots.
            dest = prepare_report_shot(src, report_shots / name, max_hw=0.78)
            pdf.set_font("TNR", "", 9)
            pdf.set_text_color(35, 35, 35)
            pdf._x()
            pdf.multi_cell(CW, 3.8, explanation)
            pdf.ln(0.5)

            left = len(shots) - i
            # Spend remaining page height across shots still to place (keeps pages dense).
            reserve = (left - 1) * 10  # later explanations + captions
            end_note = 14 if page_i == len(ui_pages) - 1 and i == len(shots) - 1 else 0
            img_h = (pdf.ui_remaining() - reserve - end_note - 6) / left
            img_h = max(64.0, img_h)
            pdf.ui_figure(dest, caption, max_h=img_h)

    pdf.set_font("TNR", "I", 9)
    pdf._x()
    pdf.multi_cell(CW, 4.5, "End of Report — FixFlow Final Project Documentation.")
    pdf.set_auto_page_break(auto=True, margin=BM + 4)

    pdf.output(OUT)
    print(f"Wrote {OUT} ({OUT.stat().st_size // 1024} KB, {pdf.page_no()} pages)")


if __name__ == "__main__":
    build()
