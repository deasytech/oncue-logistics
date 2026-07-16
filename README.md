# Oncue Logistics

Oncue Logistics is a full-stack event logistics and management platform built with **Laravel 12**, **Filament v3**, and **Livewire**. It handles the end-to-end lifecycle of event delivery services — from event creation and package customisation, through payment processing, to guest RSVP and aso ebi (fabric) distribution.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Architecture Overview](#architecture-overview)
- [Getting Started](#getting-started)
- [Environment Variables](#environment-variables)
- [Key Features & Flows](#key-features--flows)
    - [Customer Flow](#customer-flow)
    - [Guest Flow](#guest-flow)
    - [Admin Panel](#admin-panel)
- [Models & Database](#models--database)
- [Services & Integrations](#services--integrations)
- [Routing](#routing)
- [Livewire Components](#livewire-components)
- [Filament Resources](#filament-resources)
- [Queue & Jobs](#queue--jobs)
- [Testing](#testing)
- [Useful Commands](#useful-commands)

---

## Tech Stack

| Layer                      | Technology                                       |
| -------------------------- | ------------------------------------------------ |
| Framework                  | Laravel 12 (PHP 8.2+)                            |
| Admin Panel                | Filament v3                                      |
| Frontend (customer portal) | Livewire Flux + Volt                             |
| Database                   | MySQL                                            |
| Email                      | Resend (`resend/resend-laravel`)                 |
| SMS / WhatsApp             | Twilio SDK + Vonage                              |
| Payments                   | Paystack                                         |
| File Storage               | Local disk (configurable via `FILESYSTEM_DISK`)  |
| Testing                    | Pest                                             |
| Media Library              | Filament Curator (`awcodes/filament-curator`)    |
| Roles & Permissions        | Filament Shield (`bezhansalleh/filament-shield`) |
| Settings                   | TomatoPHP Filament Settings Hub                  |
| CSV Handling               | `league/csv`                                     |

---

## Architecture Overview

```
app/
├── Console/Commands/         # Artisan commands
├── Filament/                 # Admin panel: Resources, Pages, Widgets, Imports, Exports
├── Helpers/formatting.php    # Global helper functions (autoloaded)
├── Http/Controllers/         # Webhook controllers, payment callbacks, RSVP, invoice
├── Listeners/                # Event listeners (e.g. Microsoft 365 sign-in)
├── Livewire/                 # Customer-facing Livewire components (pages + components)
├── Mail/                     # Mailable classes
├── Models/                   # Eloquent models
├── Observers/                # Model observers
├── Policies/                 # Authorization policies
├── Providers/                # Service providers
└── Services/                 # Third-party service wrappers
```

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL
- A local vhost pointing to `public/` (the project uses `http://oncue.test`)

### Installation

```bash
git clone <repo-url>
cd oncue

composer install
npm install

cp .env.example .env
php artisan key:generate

# Create the database and run migrations
php artisan migrate

# Seed roles/permissions (Filament Shield)
php artisan shield:generate --all

# Create a super-admin user
php artisan make:filament-user

# Link storage
php artisan storage:link

# Build assets
npm run build
```

### Development Server

Run all services together with:

```bash
composer run dev
```

This starts the Laravel server, queue listener, log watcher (Pail), and Vite in one terminal using `concurrently`.

---

## Environment Variables

All secrets live in `.env`. Key variables to configure:

| Variable                                                  | Purpose                                    |
| --------------------------------------------------------- | ------------------------------------------ |
| `APP_URL`                                                 | Application base URL                       |
| `DB_*`                                                    | MySQL connection                           |
| `MAIL_MAILER` / `RESEND_API_KEY`                          | Email (currently Resend)                   |
| `TWILIO_*`                                                | SMS, WhatsApp, RSVP template SID           |
| `VONAGE_*`                                                | Alternative SMS/WhatsApp provider          |
| `PAYSTACK_PUBLIC_KEY` / `PAYSTACK_SECRET_KEY`             | Payment processing                         |
| `DELIVERY_MIDDLEWARE_URL` / `DELIVERY_MIDDLEWARE_API_KEY` | External delivery middleware               |
| `GOOGLE_PLACES_API_KEY`                                   | Address autocomplete                       |
| `QUEUE_CONNECTION`                                        | Set to `database` or `redis` in production |

> Switch between Paystack test/live keys by toggling the commented values in `.env`.

---

## Key Features & Flows

### Customer Flow

1. **Register / Login** — Standard Laravel auth + Microsoft 365 SSO option.
2. **Create Event** — Minimum 50 estimated guests. Events can be corporate or social and carry a logo, description, and RSVP expiry date.
3. **Select Delivery Service** — Customer picks one of:
    - _Only Aso Ebi & Invitation_ — ₦15,000
    - _Aso Ebi, Invitation & Packaging_ — ₦25,000
    - _Invitation Card_ (corporate only) — ₦8,000
    - _Packages Only_ — ₦12,000
4. **Customise Package** (if applicable) — Choose materials, fonts, colours, and other options via `PackageCustomizer`.
5. **Pay** — Online via Paystack, or offline by uploading a receipt. Admin must approve offline payments. A confirmation email is sent on approval.
6. **Upload Guest List** — CSV import unlocked after delivery payment is confirmed.
7. **Send RSVP / Invitations** — WhatsApp/SMS messages dispatched via Twilio to guests with a unique token link.

### Guest Flow

1. Guest receives an SMS/WhatsApp with a personalised RSVP link (`/rsvp/{token}`).
2. Guest confirms attendance and selects additional items (Aso Ebi Fabric, Gele, Fila, Invitation Card).
3. If any item beyond an Invitation Card is selected, guest is directed to the payment gateway and provides a delivery address.
4. Guest uploads proof of payment or pays online.

### Admin Panel

Accessible at `/admin` (Filament). Roles and permissions are managed via Filament Shield.

**Key capabilities:**

- Full CRUD for Customers, Events, Guests, Packages, Deliveries, Invoices
- Approve/reject offline payment receipts
- View Twilio message logs
- Send bulk newsletters to customers
- Export data (Filament exports)
- Manage fabric types, package materials, fonts, and colours
- Settings hub for site-wide configuration

---

## Models & Database

| Model                                              | Description                                           |
| -------------------------------------------------- | ----------------------------------------------------- |
| `User`                                             | Admin users                                           |
| `Customer`                                         | Platform customers (event organisers)                 |
| `Event`                                            | An event created by a customer                        |
| `EventGuest`                                       | Pivot between events and guests                       |
| `Guest`                                            | Individual guests on an event                         |
| `Delivery`                                         | A delivery order linked to an event                   |
| `DeliveryService`                                  | Available delivery service options                    |
| `AsoEbiSubscription`                               | A guest's aso ebi subscription                        |
| `Package`                                          | Customisable package template                         |
| `PackageCustomization`                             | A customer's specific customisation of a package      |
| `PackageColor` / `PackageFont` / `PackageMaterial` | Package attribute options                             |
| `GuestPackageSelection`                            | A guest's selected package                            |
| `GuestFabricSelection`                             | A guest's fabric/aso ebi selection + delivery details |
| `FabricType`                                       | Available fabric types (soft-deletable)               |
| `PackagePayment`                                   | Payment record for a package order                    |
| `PaymentReceipt`                                   | Offline receipt uploaded by a customer                |
| `PaymentRecord`                                    | DB view aggregating payment data                      |
| `Invoice` / `InvoiceItem`                          | Invoices generated for customers                      |
| `Category`                                         | Event categories                                      |
| `State` / `City`                                   | Nigerian states and cities (with lat/lng)             |
| `ContactEnquiry`                                   | Messages from the contact form                        |
| `NewsletterSubscriber`                             | Email newsletter subscribers                          |
| `TwilioMessageLog`                                 | Log of every Twilio message sent                      |

Migrations are in `database/migrations/` and follow chronological naming.

---

## Services & Integrations

Located in `app/Services/`:

| Service               | File                            | Purpose                                                                |
| --------------------- | ------------------------------- | ---------------------------------------------------------------------- |
| `TwilioService`       | `TwilioService.php` + `Twilio/` | Send SMS, WhatsApp messages; log all messages to `twilio_message_logs` |
| `VonageService`       | `VonageService.php`             | Alternative SMS provider                                               |
| `PaystackService`     | `PaystackService.php`           | Initialise transactions, verify payments                               |
| `DeliveryZoneService` | `DeliveryZoneService.php`       | Calculate delivery zones and fees                                      |
| `ResendMailService`   | `ResendMailService.php`         | Send transactional emails via Resend API                               |

**Webhooks handled by controllers:**

- `PaystackWebhookController` — Receives Paystack payment events, marks deliveries as paid
- `TwilioStatusWebhookController` — Receives Twilio delivery status callbacks, updates logs

---

## Routing

`routes/web.php` covers:

- **Public pages**: Home, Who We Are, Blog, Services, FAQ, Terms, Privacy, Refund Policy, Contact
- **RSVP**: `GET/POST /rsvp/{token}` — Guest RSVP flow
- **Payment flow**: `/payment/preview`, `/payment/confirm`, `/payment/summary`, `/payment/offline`, `/payment/paystack/*`
- **Auth routes**: in `routes/auth.php`
- **Authenticated customer portal**: Events, Guests, Packages, Delivery Services, Cart/Checkout, Invoices — all Livewire components behind `auth` middleware
- **Webhooks**: Paystack and Twilio status callbacks
- **Admin**: Filament at `/admin`

---

## Livewire Components

Customer-facing UI lives in `app/Livewire/`:

| Directory                                                           | Components                                                              |
| ------------------------------------------------------------------- | ----------------------------------------------------------------------- |
| `Events/`                                                           | `EventList`, `EventCreate`, `EventEdit`                                 |
| `Guests/`                                                           | `GuestList`, `GuestCreate`, `GuestEdit`, `GuestImport`                  |
| `Packages/`                                                         | `PackageList`, `PackageCustomizer`                                      |
| `Delivery/`                                                         | `DeliveryServices`                                                      |
| `Cart/`                                                             | `CartSummary`, `Checkout`, `OrderConfirmation`, `OrderConfirmationLive` |
| `Dashboard/`                                                        | Customer dashboard widgets                                              |
| `Pages/`                                                            | `HomePage`                                                              |
| `Frontend/`                                                         | `CustomerSetup` (post-registration setup wizard)                        |
| `ContactPage/`, `Faq/`, `Terms/`, `PrivacyPolicy/`, `RefundPolicy/` | Static/marketing pages                                                  |
| `Components/`                                                       | Shared sub-components                                                   |
| `Actions/`                                                          | Reusable action components                                              |

---

## Filament Resources

Admin resources in `app/Filament/Resources/`:

`CustomerResource`, `EventResource`, `GuestResource`, `GuestOrderResource`, `PackageResource`, `PackageColorResource`, `PackageFontResource`, `PackageMaterialResource`, `PackagePaymentResource`, `InvoiceResource`, `PaymentReceiptResource`, `ContactEnquiryResource`, `NewsletterSubscriberResource`, `TwilioMessageLogResource`, `UserResource`, `FabricTypeResource`

`ProjectInfoResource` — displays project metadata in the admin panel.

Bulk actions include: **Send Newsletter** (on `CustomerResource`), **Import/Export** (Filament native).

---

## Queue & Jobs

`QUEUE_CONNECTION=sync` is set for local development (jobs run synchronously).

For production, switch to `database` or `redis` and run:

```bash
php artisan queue:work
```

Newsletter emails and Twilio dispatches support queuing via standard Laravel `Mail::queue()` and job classes.

---

## Testing

Tests use **Pest** (`pestphp/pest`).

```bash
composer run test
# or
php artisan test
```

Test files live in `tests/Feature/` and `tests/Unit/`.

---

## Useful Commands

```bash
# Run dev environment (server + queue + logs + vite)
composer run dev

# Generate IDE helpers
php artisan ide-helper:generate
php artisan ide-helper:models

# Generate Filament Shield permissions
php artisan shield:generate --all

# Clear all caches
php artisan optimize:clear

# Run database migrations fresh with seeders
php artisan migrate:fresh --seed

# Tail logs
php artisan pail
```

---

## Additional Documentation

Detailed feature docs are in the `docs/` folder:

- [Delivery Service Flow](docs/DELIVERY_SERVICE_FLOW.md)
- [Updated Delivery Flow](docs/UPDATED_DELIVERY_FLOW.md)
- [Offline Payment Receipt Flow](docs/OFFLINE_PAYMENT_RECEIPT_FLOW.md)
- [Newsletter Feature](docs/NEWSLETTER_FEATURE.md)
- [Email Setup Guide](docs/EMAIL_SETUP_GUIDE.md)
- [Conditional Access Setup](docs/CONDITIONAL_ACCESS_SETUP.md)
