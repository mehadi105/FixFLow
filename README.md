# FixFlow — Electronic Device Repair Management System

FixFlow is a role-based web application for managing the full lifecycle of electronic device repairs — from a customer submitting a repair request, through technician diagnosis and repair, to invoicing, warranty, and business reporting.

Built as a university final project with **Laravel**, **Blade**, and **Tailwind CSS**.

---

## Features

### Customer
- Register / sign in and land on a personal dashboard
- Submit repair requests (device details, issue description, priority, optional image)
- Track repair status through a live timeline
- View invoices and warranty coverage

### Technician
- See jobs assigned to them
- Update repair status (assigned → diagnosing → repairing → completed)
- Record diagnosis notes

### Admin
- System-wide dashboard (customers, technicians, repairs, revenue)
- Manage all repair requests and assign technicians
- Create invoices (service charge, parts, discount, auto total) and mark them paid/unpaid
- Issue warranties with selectable coverage duration
- Manage users and change roles
- Business reports: revenue, repairs by status, monthly trend, technician performance

---

## Tech Stack

- **Laravel 13** (PHP 8.2+)
- **Blade** templating with reusable components
- **Tailwind CSS** (custom `ff-*` design system) built via **Vite**
- **SQLite** by default (any Laravel-supported database works)
- Session-based authentication with role middleware

---

## Getting Started

### Requirements
- PHP 8.2+
- Composer
- Node.js & npm

### Installation

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite is configured by default)
touch database/database.sqlite
php artisan migrate --seed

# 4. Storage symlink (for uploaded device images)
php artisan storage:link

# 5. Build front-end assets
npm run build      # or: npm run dev (for live reload)

# 6. Run the app (with live chat)
composer run dev

# Or run manually in separate terminals:
# php artisan serve
# php artisan reverb:start
# npm run dev
```

Then open http://127.0.0.1:8000.

### Live chat demo (two windows)

1. Start the app with Reverb running (`composer run dev` or `php artisan reverb:start` in a second terminal).
2. Log in as `customer@fixflow.test` in a normal browser window.
3. Log in as `technician@fixflow.test` in an incognito/private window.
4. Both accounts have **pre-seeded conversations** on John's repair requests — look for the unread badge on **My Repair Requests** / **Assigned Jobs** in the sidebar.
5. Open the **repairing** request (technician sees 1 unread) or **diagnosing** request (customer sees 1 unread) → scroll to the **Conversation** panel.
6. Send a new message in one window; it should appear instantly in the other. Start typing to show the **typing indicator** (requires Reverb / Live status).

If Reverb is not running, chat falls back to **polling** every few seconds (status badge shows "Polling"). Typing indicators only work in Live mode.

### Showcase script (~3 minutes)

Use this flow for a viva or demo recording:

| Step | Account | Action |
| ---- | ------- | ------ |
| 1 | Admin `admin@fixflow.test` | Dashboard → Users → show role management |
| 2 | Admin | Repair Requests → assign a technician to a pending job |
| 3 | Customer `customer@fixflow.test` | Dashboard → note unread badge → open diagnosing repair → read technician message |
| 4 | Technician `technician@fixflow.test` | Assigned Jobs → open repairing request → reply in Conversation |
| 5 | Both (two windows) | Live chat: send messages + typing indicator with Reverb running |
| 6 | Admin | Reports + Invoices (mark paid) → Warranty on completed job |

Reset demo data anytime:

```bash
php artisan migrate:fresh --seed
```

---

## Demo Accounts

The seeder creates the following accounts (password for all: **`password`**):

| Role       | Email                      |
| ---------- | -------------------------- |
| Admin      | `admin@fixflow.test`       |
| Technician | `technician@fixflow.test`  |
| Customer   | `customer@fixflow.test`    |

The seeder also generates sample customers, repair requests across all statuses, invoices, warranties, and **chat message threads** spread over the last six months so dashboards and reports look populated.

To reset the database with fresh demo data:

```bash
php artisan migrate:fresh --seed
```

---

## Project Structure

| Area | Location |
| ---- | -------- |
| Routes | `routes/web.php` |
| Controllers | `app/Http/Controllers/` |
| Models | `app/Models/` (`User`, `RepairRequest`, `Invoice`, `Warranty`) |
| Role middleware | `app/Http/Middleware/EnsureUserHasRole.php` |
| Views | `resources/views/` |
| Design system | `resources/css/app.css` |
| Migrations | `database/migrations/` |
| Factories & seeder | `database/factories/`, `database/seeders/` |

### Modules
1. **User Roles & Auth** — role column, role-based redirects, `role` middleware
2. **Repair Requests** — submit, list, view, image upload
3. **Technician Workflow** — assignment, status updates, diagnosis notes
4. **Invoices** — create, compute totals, mark paid/unpaid, printable view
5. **Warranty** — issue coverage, derived active/expired status
6. **Reports** — revenue, status breakdown, monthly trend, technician performance

---

## License

Released under the [MIT license](https://opensource.org/licenses/MIT).
