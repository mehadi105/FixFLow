#!/usr/bin/env python3
"""Generate FixFlow Laravel viva cheat sheet PDF (simple wording)."""

from __future__ import annotations

from pathlib import Path

from fpdf import FPDF


DOCS = Path(__file__).resolve().parent
OUT = DOCS / "FixFlow_Viva_Cheat_Sheet.pdf"
FONT_DIR = Path("/System/Library/Fonts/Supplemental")

LM, RM, TM, BM = 12, 12, 14, 14
CW = 210 - LM - RM

# Column widths: Question | Simple answer | Where in codebase
Q_W, A_W = 38, 88
P_W = CW - Q_W - A_W

# (section title, [(question, simple answer, where in project)])
SECTIONS: list[tuple[str, list[tuple[str, str, str]]]] = [
    (
        "1. How Laravel works & project setup",
        [
            (
                "How is Laravel structured?",
                "Every request starts at one entry file. Then Laravel loads routes, checks, and pages.",
                "public/index.php · bootstrap/app.php",
            ),
            (
                "How do we set up the project?",
                "Install packages, copy .env, make app key, start Postgres, migrate + seed, run composer run dev.",
                "composer.json · README.md · .env · .env.example",
            ),
            (
                "What are the main folders?",
                "app = code, routes = URLs, resources/views = pages, database = tables and demo data.",
                "app/ · routes/ · resources/ · database/",
            ),
            (
                "What is Artisan?",
                "Laravel’s CLI. We use it for migrate, seed, serve, queue, and Reverb.",
                "README.md · php artisan …",
            ),
            (
                "What is routing?",
                "Routing means: this URL opens this page or runs this action.",
                "routes/web.php · routes/channels.php",
            ),
            (
                "Which database?",
                "PostgreSQL locally (pgsql). Tests still use SQLite in-memory.",
                "config/database.php · .env (DB_CONNECTION=pgsql) · phpunit.xml",
            ),
        ],
    ),
    (
        "2. Blade (HTML pages)",
        [
            (
                "What is {{ }} ?",
                "It prints safe text on the page. Special HTML characters are escaped.",
                "resources/views/layouts/app.blade.php",
            ),
            (
                "What is {!! !!} ?",
                "It prints HTML as-is. We use it only for trusted icons.",
                "resources/views/components/stat-card.blade.php",
            ),
            (
                "Blade comments?",
                "Notes written as {{-- ... --}}. Users never see them in the browser.",
                "resources/views/welcome.blade.php",
            ),
            (
                "If / loops in Blade?",
                "We use @if for conditions, @for / @foreach / @forelse for lists, and @while once in reports.",
                "resources/views/repair-requests/* · reports/index.blade.php",
            ),
            (
                "How do pages share one layout?",
                "Guest pages use guest layout; app pages use app layout; landing uses marketing layout.",
                "layouts/guest.blade.php · layouts/app.blade.php · layouts/marketing.blade.php",
            ),
            (
                "How does data reach the page?",
                "The controller sends variables with the view. The Blade file prints those variables.",
                "app/Http/Controllers/DashboardController.php · ReportController.php",
            ),
            (
                "What are @push and @stack?",
                "A page can add extra JS/CSS. The layout prints those extras with @stack.",
                "layouts/app.blade.php · invoices/create.blade.php",
            ),
            (
                "How do we make links?",
                "We use route('name') or url('/...') so links stay correct if URLs change.",
                "routes/web.php · layouts/partials/sidebar-nav.blade.php",
            ),
        ],
    ),
    (
        "3. MVC, controllers, middleware",
        [
            (
                "What is MVC here?",
                "Model = database data. View = page. Controller = decides what happens for a request.",
                "app/Models/RepairRequest.php · RepairRequestController.php · resources/views/repair-requests/",
            ),
            (
                "What does a controller do?",
                "It checks the user, checks the form, saves or loads data, then shows a page or redirects.",
                "app/Http/Controllers/RepairRequestController.php",
            ),
            (
                "What is middleware?",
                "Code that runs before the page. Example: only admin can open admin pages.",
                "app/Http/Middleware/EnsureUserHasRole.php · bootstrap/app.php",
            ),
            (
                "Middleware redirect / headers?",
                "Unapproved technicians are redirected. Authenticated pages send Cache-Control: no-store (no back-button leak).",
                "EnsureApprovedTechnician.php · AddFixFlowResponseHeaders.php · resources/js/session-guard.js",
            ),
            (
                "What is rate limiting?",
                "We block too many tries in a short time (login, signup, ajax).",
                "AppServiceProvider.php · routes/web.php (throttle:…)",
            ),
        ],
    ),
    (
        "4. Routes, forms, session, cookies",
        [
            (
                "Which HTTP methods do we use?",
                "GET to open pages. POST to submit forms. PATCH/DELETE to update or remove (with @method).",
                "routes/web.php · resources/views/invoices/*",
            ),
            (
                "What is CSRF?",
                "A hidden token in forms so bad sites cannot submit forms for the user. We use @csrf.",
                "resources/views/auth/login.blade.php · repair-requests/create.blade.php",
            ),
            (
                "How do we process a form?",
                "Validate the input, then save it to the database, then redirect with a success message.",
                "RepairRequestController@store · TechnicianApplicationController@store",
            ),
            (
                "What is a session?",
                "Server memory of the logged-in user. After login we refresh the session. We also flash short messages.",
                "routes/web.php (login / logout) · SESSION_* in .env",
            ),
            (
                "What are cookies here?",
                "We save table density (compact/comfortable) in a cookie and read it on the layout.",
                "PreferenceController.php · layouts/app.blade.php · top-navbar.blade.php",
            ),
        ],
    ),
    (
        "5. Database migrations & seeding",
        [
            (
                "What is a migration?",
                "A PHP file that creates or changes database tables. Easy to share and repeat.",
                "database/migrations/",
            ),
            (
                "How do we design tables?",
                "We define columns, unique fields, and links between tables in create migrations.",
                "…_create_repair_requests_table.php · …_create_invoices_table.php",
            ),
            (
                "How do we change a table later?",
                "We add a new migration that only adds or removes columns (quotes, fulfillment, documents).",
                "…_add_quote_fields… · …_add_document_to_technician_applications…",
            ),
            (
                "Indexes and foreign keys?",
                "Foreign keys link tables. Indexes/uniques make lookups faster and keep data unique.",
                "…_create_messages_table.php · invoices migrations",
            ),
            (
                "What is seeding?",
                "We fill the database with demo users, repairs, chat, invoices, and a waiting quote (RR-DEMO-QUOTE).",
                "database/seeders/DatabaseSeeder.php",
            ),
            (
                "Rollback / refresh?",
                "down() undoes a migration. For a clean demo we use migrate:fresh --seed.",
                "README.md",
            ),
        ],
    ),
    (
        "6. Query Builder & Eloquent (database code)",
        [
            (
                "What is Query Builder?",
                "We write database queries in PHP (select, where, join) without raw SQL everywhere.",
                "app/Http/Controllers/ReportController.php",
            ),
            (
                "What is Eloquent?",
                "Each table has a Model class. RepairRequest model uses the repair_requests table.",
                "app/Models/RepairRequest.php · Invoice.php · User.php",
            ),
            (
                "Insert / update / delete?",
                "create to add, update to change, delete to remove. fillable lists safe fields.",
                "Controllers + model $fillable arrays",
            ),
            (
                "Where / select with Eloquent?",
                "We filter lists and count rows on dashboards with where and count.",
                "RepairRequestController · DashboardController",
            ),
            (
                "Joins and totals?",
                "Reports join tables and use sum/count/groupBy. Charts use Chart.js on that data.",
                "ReportController.php · resources/js/charts.js · reports/index.blade.php",
            ),
        ],
    ),
    (
        "7. Requests, JSON, Ajax",
        [
            (
                "Do we have a REST API?",
                "Not a full API project. Chat and notifications return JSON from normal web routes.",
                "MessageController.php · NotificationController.php",
            ),
            (
                "Did we use Postman?",
                "No Postman file in the project. Those same URLs can still be tested in Postman.",
                "—",
            ),
            (
                "Different request types?",
                "Forms send body data. Filters use URL params. Uploads send files. Fetch sends headers.",
                "repair-requests/create · auth/technician-apply · resources/js/live-filter.js",
            ),
            (
                "File upload?",
                "Customer: device image on repair form. Applicant: CV/certificate document (required).",
                "RepairRequestController@store · TechnicianApplicationController@store · storage/app/public",
            ),
            (
                "Quote after diagnosis?",
                "Technician sends quote → customer approves (repair continues) or declines (diagnosis fee only).",
                "RepairRequestController quote/approve/decline · InvoiceService.php · repair-requests/show.blade.php",
            ),
            (
                "Redirect response?",
                "After save we send the user to another page. Stripe sends them to Stripe’s website to pay.",
                "app/Http/Controllers/InvoicePaymentController.php",
            ),
            (
                "Ajax / Fetch?",
                "JavaScript loads chat, filters, and notifications without a full page reload.",
                "resources/js/chat.js · live-filter.js · notifications.js · unread-badges.js",
            ),
        ],
    ),
    (
        "8. Outside services (API)",
        [
            (
                "Any external API?",
                "Yes. Stripe for online invoice payment (test mode).",
                "app/Services/StripePaymentService.php · InvoicePaymentController.php · .env STRIPE_*",
            ),
            (
                "Is chat an external API?",
                "No. Live chat uses our own Laravel Reverb WebSocket server (local).",
                "config/reverb.php · resources/js/chat.js · routes/channels.php · composer run dev",
            ),
        ],
    ),
    (
        "9. Frontend UI animations (what moves where)",
        [
            (
                "Landing hero fade-in?",
                "Hero title, CTAs, stats, and dashboard preview fade/slide up with staggered delays.",
                "welcome.blade.php [data-hero-item] · app.css ff-fade-up · landing.js initHero()",
            ),
            (
                "Scroll reveal?",
                "Sections fade in when they enter the viewport (features, services, steps, CTA).",
                "welcome.blade.php data-reveal / data-reveal-child · landing.js initReveal() · app.css .ff-in-view",
            ),
            (
                "Number counters?",
                "Hero stats count up from 0 when visible (eased animation).",
                "welcome.blade.php data-counter · resources/js/landing.js initCounters()",
            ),
            (
                "Floating Live Chat / Invoice badges?",
                "Soft up-down float loop on the hero mockup. Invoice badge starts later (delay).",
                "welcome.blade.php .ff-float-badge · app.css @keyframes ff-float",
            ),
            (
                "Brand logo marquee?",
                "Trusted brands strip scrolls horizontally forever; slows/pauses feel on hover.",
                "welcome.blade.php .ff-marquee · app.css @keyframes ff-marquee",
            ),
            (
                "Reviews marquee?",
                "Customer review cards scroll sideways; pause when the mouse is over them.",
                "welcome.blade.php .ff-reviews-marquee · app.css @keyframes ff-reviews-marquee",
            ),
            (
                "Animated gradient text?",
                "Some headings use a moving gradient color shift.",
                "app.css .ff-gradient-text-animated · @keyframes ff-gradient-shift",
            ),
            (
                "Live green pulse on mockup?",
                "Small “Live” dot on the fake dashboard pulses.",
                "welcome.blade.php · app.css .ff-dashboard-live-dot · @keyframes ff-pulse-dot",
            ),
            (
                "Hover on cards / buttons?",
                "Cards lift slightly; icons scale/rotate; primary buttons darken and press (active:scale).",
                "app.css .ff-feature-card · .ff-btn-primary · welcome group-hover classes",
            ),
            (
                "Nav underline / spy?",
                "Marketing nav highlights the section in view; underline grows with CSS transform.",
                "landing.js initNavSpy() · app.css nav underline scale-x",
            ),
            (
                "Smooth scroll?",
                "Clicking About/Services anchors scrolls smoothly (respects reduced-motion).",
                "resources/js/landing.js initSmoothScroll()",
            ),
            (
                "Sidebar slide (app)?",
                "On mobile, the admin/customer sidebar slides in/out with transform transition.",
                "layouts/app.blade.php #sidebar · transition-transform duration-300",
            ),
            (
                "Chat typing indicator?",
                "When the other user types, a “is typing…” line shows (Reverb whisper). Polling fallback if offline.",
                "resources/js/chat.js · chat-panel component · Laravel Echo + Reverb",
            ),
            (
                "Reduced motion?",
                "If the OS asks for less motion, we disable loops and show content instantly.",
                "app.css @media (prefers-reduced-motion) · landing.js prefersReducedMotion()",
            ),
            (
                "Charts (not CSS animation)?",
                "Reports/admin use Chart.js bar + doughnut charts (JS library, not keyframes).",
                "resources/js/charts.js · reports/index.blade.php · dashboard/admin.blade.php",
            ),
        ],
    ),
]


class CheatSheet(FPDF):
    def __init__(self):
        super().__init__(format="A4", unit="mm")
        self.set_auto_page_break(auto=True, margin=BM + 2)
        self.set_margins(LM, TM + 6, RM)
        self.add_font("TNR", "", str(FONT_DIR / "Times New Roman.ttf"))
        self.add_font("TNR", "B", str(FONT_DIR / "Times New Roman Bold.ttf"))
        self.add_font("TNR", "I", str(FONT_DIR / "Times New Roman Italic.ttf"))
        self.add_font("AR", "", str(FONT_DIR / "Arial.ttf"))
        self.add_font("AR", "B", str(FONT_DIR / "Arial Bold.ttf"))

    def header(self):
        self.set_font("AR", "", 8)
        self.set_text_color(60, 60, 60)
        self.set_xy(LM, 8)
        self.cell(CW / 2, 5, "FixFlow · CSE 3100", align="L")
        self.cell(CW / 2, 5, "Viva Cheat Sheet (Simple)", align="R")
        self.set_draw_color(0, 0, 0)
        self.set_line_width(0.3)
        self.line(LM, 14, 210 - RM, 14)
        self.set_y(17)
        self.set_text_color(0, 0, 0)

    def footer(self):
        self.set_y(-12)
        self.set_draw_color(0, 0, 0)
        self.set_line_width(0.3)
        self.line(LM, self.get_y(), 210 - RM, self.get_y())
        self.ln(1)
        self.set_font("AR", "", 7.5)
        self.set_text_color(60, 60, 60)
        self.set_x(LM)
        self.cell(CW - 12, 4, "Read: Question -> Say this -> Where in codebase (file / class)", align="L")
        self.cell(12, 4, str(self.page_no()), align="R")

    def _x(self):
        self.set_x(LM)

    def title_block(self):
        self._x()
        self.set_font("TNR", "B", 15)
        self.multi_cell(CW, 7, "FixFlow Viva Cheat Sheet")
        self.ln(0.5)
        self._x()
        self.set_font("TNR", "", 9)
        self.multi_cell(
            CW,
            4.2,
            "Simple answers for oral exam. Left = question. Middle = what to say. "
            "Right = exact place in the codebase / UI.",
        )
        self.ln(1.5)
        self._x()
        self.set_font("TNR", "I", 8.5)
        self.multi_cell(
            CW,
            4,
            "Tip: Section 9 lists landing/app animations: which UI piece, what motion, which file.",
        )
        self.ln(2)

    def section(self, title: str):
        if self.get_y() > 262:
            self.add_page()
        self._x()
        self.set_font("TNR", "B", 10.5)
        self.set_fill_color(235, 235, 235)
        self.cell(CW, 6.5, f"  {title}", fill=True)
        self.ln(7.5)

    def row(self, asked: str, say: str, point: str):
        self.set_font("AR", "", 7.5)
        q_lines = self.multi_cell(Q_W - 2, 3.5, asked, dry_run=True, output="LINES")
        a_lines = self.multi_cell(A_W - 2, 3.5, say, dry_run=True, output="LINES")
        p_lines = self.multi_cell(P_W - 2, 3.5, point, dry_run=True, output="LINES")
        h = max(len(q_lines), len(a_lines), len(p_lines)) * 3.5 + 2.2
        if self.get_y() + h > 275:
            self.add_page()
            self._header_row()
        y0 = self.get_y()
        x0 = LM
        widths = [Q_W, A_W, P_W]
        texts = [asked, say, point]
        styles = ["B", "", ""]
        for w, text, style in zip(widths, texts, styles):
            self.set_draw_color(180, 180, 180)
            self.set_line_width(0.2)
            self.rect(x0, y0, w, h)
            self.set_xy(x0 + 1, y0 + 1.0)
            self.set_font("AR", style, 7.5)
            self.set_text_color(0, 0, 0)
            self.multi_cell(w - 2, 3.5, text)
            x0 += w
        self.set_y(y0 + h)

    def _header_row(self):
        self.set_font("AR", "B", 7.5)
        self.set_fill_color(245, 245, 245)
        self.set_x(LM)
        for w, label in (
            (Q_W, "Question"),
            (A_W, "Simple answer (say this)"),
            (P_W, "Where in codebase"),
        ):
            self.cell(w, 5.5, f"  {label}", border=1, fill=True)
        self.ln()

    def closing(self):
        if self.get_y() > 248:
            self.add_page()
        self.ln(2.5)
        self._x()
        self.set_font("TNR", "B", 11)
        self.cell(CW, 6, "Short closing answer")
        self.ln(7)
        y = self.get_y()
        self.set_font("TNR", "", 9)
        text = (
            "FixFlow is a repair shop website built with Laravel + Blade + PostgreSQL. "
            "Users open Blade pages, controllers handle actions, and Eloquent saves data. "
            "We use migrations, seeders, middleware, forms with CSRF, sessions, file upload, "
            "quote approval after diagnosis, Chart.js reports, and Fetch/Reverb for live chat. "
            "Online payment uses Stripe. Landing motion is CSS keyframes + landing.js "
            "(no heavy animation library)."
        )
        lines = self.multi_cell(CW - 6, 4.3, text, dry_run=True, output="LINES")
        h = len(lines) * 4.3 + 6
        self.set_draw_color(0, 0, 0)
        self.set_line_width(0.35)
        self.rect(LM, y, CW, h)
        self.set_xy(LM + 3, y + 3)
        self.multi_cell(CW - 6, 4.3, text)


def main():
    pdf = CheatSheet()
    pdf.add_page()
    pdf.title_block()
    for title, rows in SECTIONS:
        pdf.section(title)
        pdf._header_row()
        for asked, say, point in rows:
            pdf.row(asked, say, point)
        pdf.ln(2.2)
    pdf.closing()
    pdf.output(OUT)
    print(f"Wrote {OUT} ({OUT.stat().st_size // 1024} KB, {pdf.page_no()} pages)")


if __name__ == "__main__":
    main()
