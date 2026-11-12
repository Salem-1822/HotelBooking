<h1 align="center">🏨 HotelBooking</h1>

<p align="center">
<strong>A Full-Stack Multi-Tenant Hotel & Room Reservation Platform Built on Laravel 12</strong>
</p>

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12.x-red?style=flat-square&logo=laravel" alt="Laravel 12">
<img src="https://img.shields.io/badge/PHP-8.2.35-blue?style=flat-square&logo=php" alt="PHP 8.2">
<img src="https://img.shields.io/badge/MySQL-8.x-orange?style=flat-square&logo=mysql" alt="MySQL">
<img src="https://img.shields.io/badge/Bootstrap-5.3-purple?style=flat-square&logo=bootstrap" alt="Bootstrap 5.3">
<img src="https://img.shields.io/badge/Tests-143%20passed%20(603%20assertions)-brightgreen?style=flat-square" alt="Tests">
<img src="https://img.shields.io/badge/Vulnerabilities-0%20reported-success?style=flat-square" alt="Security">
<img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="License">
</p>

---

## 📖 Portfolio Project Overview

**HotelBooking** is a comprehensive multi-portal hospitality management system developed as a portfolio showcase. Built on modern **Laravel 12** and styled with a responsive **Bootstrap 5.3** interface, it models the complete operational lifecycle of a hospitality platform: public client booking engine, single-hotel manager console, and platform-wide super administrator governance.

> **Note for Portfolio Reviewers:**  
> This project is designed as an architectural and engineering demonstration. All payment flows (e.g. Stripe Test Mode) operate in sandbox simulation without real financial transactions.

---

## 🏛️ Application Architecture

The application enforces strict multi-guard authentication and role-based access control (RBAC):

```
                                  Incoming Request
                                         │
                    ┌────────────────────┴────────────────────┐
                    ▼                                         ▼
             Guard: 'web'                              Guard: 'admin'
           (Clients / Guests)                   (Hotel & Super Administrators)
                    │                                         │
              Users Table                                Admins Table
            [role: client]                       ┌────────────┴────────────┐
                    │                            ▼                         ▼
            Client Dashboard              Role: 'admin'          Role: 'super_admin'
            My Reservations             (Hotel Admin Portal)     (Super Admin Portal)
            Guest Reviews               Scoped by hotel_id       Global Platform Control
```

---

## ✨ Features by Role

### 👑 1. Super Administrator (Platform Governance)
- **Global Overview Dashboard:** Real-time KPI aggregation of active hotels, inventory, total bookings, registered managers, and platform revenue.
- **Hotel Onboarding & CRUD:** Create and configure hotel listings, star ratings (1–5), amenities, location coordinates, and cover imagery.
- **City Directory:** Manage supported cities, regional slugs, and destination categories.
- **Administrator Management:** Create hotel administrators, assign single-hotel management permissions, and toggle instant **Account Blocking & Unblocking** with mandatory audit reasons.
- **Reporting & PDF Exports:** Filter and export platform-wide reservation dossiers as formatted PDF documents.

### 🏨 2. Hotel Administrator (Single-Hotel Operations)
- **Hotel Scoping (IDOR-Safe):** All queries and CRUD actions are strictly bounded to the administrator's assigned property (`hotel_id`).
- **Room Inventory Management:** Real-time room CRUD (number, room type, capacity, price per night, status: available/occupied/maintenance, and gallery uploads).
- **Reservation Workflow:** Full status lifecycle transitions (`pending` $\rightarrow$ `confirmed` $\rightarrow$ `checked_in` $\rightarrow$ `checked_out` $\rightarrow$ `cancelled`).
- **Customer Directory:** Normalized customer database (`Customer::normalizePhone`) tracking stay history, returning guest metrics, and direct contacts.
- **Hotel Profile & Manager Settings:** Self-service hotel details update and administrator profile/password management with verified password hashing.

### 🛎️ 3. Client & Guest Experience
- **Hotel Discovery & Filtering:** Filter hotels by destination city, star rating, availability dates, and guest capacity.
- **Pessimistic Concurrency Locking:** Atomic reservation store (`Room::lockForUpdate`) with mathematical overlap detection (`check_in < $newCheckOut AND check_out > $newCheckIn`) preventing overbooking.
- **Client Dashboard & Confirmation:** View reservation status, manage personal bookings, download vouchers, and cancel bookings within permitted policy windows.
- **Post-Stay Verified Reviews:** Reviews can strictly only be submitted after a verified `checked_out` stay. Input is sanitized (`strip_tags`) to prevent stored XSS.

---

## 🛠️ Technology Stack

| Layer | Technologies |
|---|---|
| **Backend Framework** | Laravel 12.x (PHP 8.2.35 Target) |
| **Authentication & RBAC** | Dual Guards (`web`, `admin`), `RoleMiddleware`, `CheckAdminBlocked` |
| **Database & ORM** | MySQL 8.x / SQLite (PDO Prepared Statements, Foreign Keys, Composite Indexes) |
| **Frontend & UI** | Blade Templating, Bootstrap 5.3, Bootstrap Icons, Responsive Flexbox/Grid |
| **Email & Notifications** | Brevo SMTP, Responsive HTML Email Templates (`ReservationStatusChangedNotification`) |
| **Document Generation** | DomPDF (`Barryvdh\DomPDF`) |
| **Testing & QA** | PHPUnit 11 with native `#[Test]` attributes, 143 tests (603 assertions) |

---

## 🚀 Local Installation & Setup

### Prerequisites
- PHP $\ge$ 8.2 (with `pdo_mysql`, `mbstring`, `curl`, `gd`, `bcmath`)
- Composer $\ge$ 2.0
- MySQL $\ge$ 8.0 (or SQLite for testing)

### Step-by-Step Installation
```bash
# 1. Clone repository
git clone https://github.com/Salem-1822/HotelBooking.git
cd HotelBooking

# 2. Install dependencies
composer install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=hotelbooking
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Run database migrations & seed demo data
php artisan migrate --seed

# 6. Create storage symlink
php artisan storage:link

# 7. Start local server
php artisan serve
```
Access the application at `http://127.0.0.1:8000`.

---

## 🧪 Testing & Verification

The test suite runs against an in-memory SQLite database (`:memory:`) with full isolation:

```bash
# Run all automated tests
php artisan test

# Run a specific feature test suite
php artisan test tests/Feature/ReservationLifecycleTest.php
php artisan test tests/Feature/AdminManagementAuditTest.php
php artisan test tests/Feature/EmailAndNotificationSystemTest.php

# Security & dependency audit
composer audit
composer validate
```

---

## ☁️ Public Demo Deployment (Render PaaS)

This repository includes a production-ready `render.yaml` specification for deploying a public demo on [Render](https://render.com).

### Deployment Architecture
1. **Web Service (`render.yaml`):**
   - Environment: `php`
   - Build Command:
     ```bash
     composer install --no-dev --optimize-autoloader --no-interaction
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     ```
   - Start Command:
     ```bash
     php artisan storage:link
     php artisan migrate --force
     apache2-foreground
     ```
2. **Managed MySQL Database:**
   - Automatically provisions a free-tier Render MySQL database linked to the web service via environment variables.

### Demo Safety Safeguards
- `APP_DEBUG=false` in production.
- `SESSION_SECURE_COOKIE=true` for HTTPS encryption.
- No production secrets or personal payment credentials committed to version control.
- Rate-limited authentication routes against brute-force attacks.

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
