# Venture Visionary CRM & Business Consultancy Management Platform (v2.0)

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-Proprietary-0F2942?style=for-the-badge)](LICENSE)

An enterprise-grade, multi-tenant visa operations, corporate structuring, and client relationship management system built specifically for **Venture Visionary Visa and Business Consultancy, Doha, Qatar**. Built strictly to comply with Developer Handover Specification Blueprint v2.0 (100% open-source foundation, zero proprietary lock-in).

---

## Table of Contents

1. [Executive Summary & System Architecture](#executive-summary--system-architecture)
2. [The 3 Multi-Tenant Portals](#the-3-multi-tenant-portals)
3. [15 Developer Non-Negotiable Rules](#15-developer-non-negotiable-rules)
4. [Technology Stack](#technology-stack)
5. [Docker Setup & Quickstart](#docker-setup--quickstart)
6. [Default Test Accounts & Credentials Directory](#default-test-accounts--credentials-directory)
7. [API Endpoints & Omnichannel Webhooks](#api-endpoints--omnichannel-webhooks)
8. [Codebase Directory Layout](#codebase-directory-layout)
9. [Automated & Browser Testing](#automated--browser-testing)

---

## Executive Summary & System Architecture

Unlike generic CRM platforms or basic contact managers, **Venture Visionary CRM** is custom-engineered to handle the complex real-world workflows of immigration consultancy, embassy submissions, and business setup in the State of Qatar.

```
                           +------------------------------------------+
                           |           Omnichannel Ingestion          |
                           |  Website API / Meta Ads / WhatsApp Hub   |
                           +------------------------------------------+
                                                 |
                                                 v
                           +------------------------------------------+
                           |          Lead & Duplicate Engine         |
                           |   Round-Robin Assignment & Scoring       |
                           +------------------------------------------+
                                                 |
                                                 v
  +---------------------------------------------------------------------------------------+
  |                                 Three Dedicated Portals                               |
  |                                                                                       |
  |   +--------------------------+   +--------------------------+   +-------------------+ |
  |   |    Staff Admin Panel     |   |    B2B Partner Portal    |   |   Client Portal   | |
  |   |         (/staff)         |   |        (/partner)        |   |     (/portal)     | |
  |   |--------------------------|   |--------------------------|   |-------------------| |
  |   | • 12-Role RBAC           |   | • Multi-Tenant Isolation |   | • Mobile OTP Auth | |
  |   | • Lead & Client 360      |   | • Wholesale Net Rates    |   | • Real-Time Track | |
  |   | • Case QC & Vault        |   | • Prepaid Wallet / Limit |   | • Doc Uploads     | |
  |   | • 193-Country Visa DB    |   | • Agency File Lodging    |   | • 100% Client-Safe| |
  |   | • Quotations & Margins   |   |                          |   |   (Notes Hidden)  | |
  |   +--------------------------+   +--------------------------+   +-------------------+ |
  +---------------------------------------------------------------------------------------+
                                                 |
                                                 v
                           +------------------------------------------+
                           |     Immutable JSONB Snapshots & Vault    |
                           |  Pricing, Checklists, & S3 Document QC   |
                           +------------------------------------------+
```

### Core Problems Solved
- **Zero Lost Inquiries**: Inbound leads from Meta Ads, WhatsApp, website forms, and walk-ins are captured into a unified pipeline with automatic Round-Robin agent routing.
- **Strict Duplicate Detection**: Real-time cross-referencing on Qatar ID (QID), Passport Numbers, and phone/WhatsApp numbers flags duplicates before redundant work occurs.
- **Client ≠ Case Relational Model**: 1 Client Profile supports multiple historical or concurrent cases (e.g. UK Tourist Visa + Qatar LLC Company Formation).
- **Quality Control (QC) Gatekeeping**: Document Vault features a formal QC review cycle (`requested -> uploaded -> under_review -> verified / rejected`) to prevent embassy visa rejections.
- **Fee Transparency & Profit Margins**: Itemized splits between Government/Embassy fees, VAC (VFS/TLS) fees, and Venture Visionary Consultancy fees to guarantee transparent margin tracking.

---

## The 3 Multi-Tenant Portals

| Portal | URL Path | Primary Audience | Key Capabilities |
| :--- | :--- | :--- | :--- |
| **Staff Admin** | `/staff` | Management, Sales Agents, Visa Officers, QC & Accounts | 12-role Spatie RBAC, Lead pipeline, Client 360, Visa case workflows, dynamic checklists, document vault, quotations, invoicing, and CEO KPI dashboard. |
| **B2B Partner** | `/partner` | Travel Agencies, Sub-Agents, Corporate Sponsors | Strict tenant data isolation, wholesale negotiated net rates, prepaid credit wallet, applicant case submission, and live filing tracking. |
| **Client Portal** | `/portal` | Direct Applicants & Qatar Residents | Secure OTP mobile authentication, live progress milestone tracker, document upload slots, consultant instructions. Staff internal notes are 100% hidden. |

---

## 15 Developer Non-Negotiable Rules

The system architecture strictly adheres to the 15 Developer Non-Negotiable Rules defined in **Blueprint v2.0**:

1. **Zero Hardcoded Visa Rules**: All 193-country visa requirements, checklists, and embassy rules reside in database tables configured via UI.
2. **Admin-Configurable Workflows**: Status transitions, fee schedules, and checklists are configurable without redeploying code.
3. **Full Action Auditability**: System actions, status transitions, role changes, and exports are tracked with user ID, timestamp, and IP address.
4. **Client ≠ Case Separation**: Clients represent human identities; Cases represent service units. One client can have unlimited cases.
5. **No Case Without Client Profile**: Every case must link to a valid master client record.
6. **Strict Multi-Tenant B2B Isolation**: Partners only ever see their own submitted cases and agency wallet data.
7. **Strict Role-Based Access Control (RBAC)**: 12 defined roles prevent privilege escalation and unauthorized financial overrides.
8. **Client Portal Data Isolation**: Staff internal notes, QC discussions, and confidential remarks are never exposed to applicant views.
9. **Documented REST APIs Only**: All inbound lead sources interact via documented REST endpoints or webhooks.
10. **Fee Transparency**: Government fees, VAC fees, and consultancy charges are tracked separately in quotations and invoices.
11. **Immutable Historical Snapshots**: When a case or quote is created, an immutable JSONB snapshot freezes the active pricing and checklist rules.
12. **Document Expiry Warnings**: Mandatory tracking of passport and QID expiration dates with automated alerts.
13. **Automated Backups & Disaster Recovery**: PostgreSQL backups and S3 file replication.
14. **Document Versioning**: Files uploaded to the document vault are never overwritten; every revision creates a new version.
15. **Maker-Checker Governance**: Sensitive country rule updates and refund requests require dual-authorization before going live.

---

## Technology Stack

- **Framework**: Laravel 11 (PHP 8.3-FPM)
- **Admin Panel & Portals**: FilamentPHP v3 (Blade, Livewire 3, Tailwind CSS, Alpine.js)
- **Database**: PostgreSQL 16 Alpine (JSONB snapshots, soft deletes, UUID primary keys)
- **Cache & Session**: Redis 7 Alpine
- **Web Server**: Nginx (Alpine Linux)
- **API Documentation**: Scramble OpenAPI v3 (`/docs/api`)
- **Containerization**: Docker & Docker Compose
- **Browser Automation**: Playwright MCP integration for end-to-end route testing

---

## Docker Setup & Quickstart

### Prerequisites
- Docker Engine `>= 24.0`
- Docker Compose `>= 2.20`

### 1. Clone & Configure Environment
```bash
git clone <repository-url> crm
cd crm
cp .env.example .env
```

### 2. Launch Docker Containers
```bash
docker compose up -d --build
```

Container services will spin up:
- `vv_crm_app`: Nginx + PHP 8.3 FPM on port `8090`
- `vv_crm_postgres`: PostgreSQL 16 on port `5433`
- `vv_crm_redis`: Redis 7 on port `6379`

### 3. Initialize Database & Seed Master Data
```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
```

### 4. Access the Application
- **Public Lead Generation & Consultation Portal**: [http://localhost:8090/lead-generation](http://localhost:8090/lead-generation) (Aliases: `/inquiry`, `/apply`)
- **Public Interactive Appointment Booking Portal**: [http://localhost:8090/book-appointment](http://localhost:8090/book-appointment) (Aliases: `/book`, `/consultation`)
- **Staff Portal**: [http://localhost:8090/staff](http://localhost:8090/staff)
  - *Visual Sales Pipeline (Kanban)*: [http://localhost:8090/staff/lead-pipeline-board](http://localhost:8090/staff/lead-pipeline-board)
  - *Appointment Calendar*: [http://localhost:8090/staff/appointments](http://localhost:8090/staff/appointments)
  - *Workflow Automation Engine*: [http://localhost:8090/staff/workflows](http://localhost:8090/staff/workflows)
  - *Reputation & 5-Star Review Logs*: [http://localhost:8090/staff/review-requests](http://localhost:8090/staff/review-requests)
- **B2B Partner Portal**: [http://localhost:8090/partner](http://localhost:8090/partner)
- **Client Portal**: [http://localhost:8090/portal](http://localhost:8090/portal)
- **Interactive API Documentation**: [http://localhost:8090/docs/api](http://localhost:8090/docs/api)
- **Platform Health Status**: [http://localhost:8090/api/v1/health](http://localhost:8090/api/v1/health)

---

## GoHighLevel Core Sales & Automation Engine

The platform integrates native open-source equivalents to high-ticket GoHighLevel marketing and automation tools:

1. **Visual Drag-and-Drop Pipeline (Kanban) & Smart Lists (`/staff/lead-pipeline-board`)**:
   - 8-stage sales progression (`new` -> `contacted` -> `qualified` -> `consultation` -> `quotation_sent` -> `follow_up` -> `converted` -> `lost`).
   - Livewire 3 + Alpine.js HTML5 drag-and-drop card movements.
   - GoHighLevel dynamic Smart Lists: `All Leads`, `🔥 Hot Leads (≥70 Score)`, `⏰ Follow-up Needed`, `👤 Unassigned`, `⭐ VIP Destinations`.
   - Card-level one-click actions: direct WhatsApp chat, quick stage selector, and one-click conversion to Client 360.

2. **The Workflow Automation Engine (The "Brain") (`/staff/workflows`)**:
   - Event-driven automation replacing Zapier/Make and ActiveCampaign subscriptions.
   - Supported triggers: `lead.created`, `lead.stage_changed`, `appointment.scheduled`, `appointment.completed`, `case.status_changed`.
   - Dynamic JSON criteria filters (e.g. `status == qualified`, `lead_score_gte >= 70`).
   - Sequential actions: task assignment, internal staff notifications, automated WhatsApp outbound messages, and review invitation dispatch.
   - Full immutable execution logs stored in PostgreSQL (`workflow_logs`).

3. **Advanced Appointment Scheduling Calendar (`/staff/appointments` & `/book-appointment`)**:
   - Replaces Calendly / Acuity.
   - Public-facing booking portal allowing prospective travelers and investors to pick destination country, choose from real-time available working slots (Qatar time), and reserve instantly.
   - Automatically provisions/links Lead profile and advances stage to `consultation`.

4. **Reputation Management & Automated Review Requests (`/staff/review-requests` & `/review/{token}`)**:
   - Automated post-service review invitations sent via WhatsApp/SMS/Email upon visa approval.
   - Smart feedback funnel: 4–5★ reviews are redirected to post on Google Business Profile; 1–3★ ratings are intercepted internally for CEO & QC customer recovery.


---

## Default Test Accounts & Credentials Directory

The database seeder initializes 10 predefined roles with pre-configured operational access:

| Role Code | Full Name / Designation | Email Address | Default Password | Panel Access |
| :--- | :--- | :--- | :--- | :--- |
| `super_admin` | Executive Super Admin (CEO) | `ceo@venturevisionary.qa` | `Admin@VV2026!` | Staff (All Panels) |
| `general_manager` | Tariq Al-Mansoor (GM) | `gm@venturevisionary.qa` | `Admin@VV2026!` | Staff |
| `operations_manager`| Rashid Al-Kuwari (Ops Director) | `ops@venturevisionary.qa` | `Admin@VV2026!` | Staff |
| `sales_agent` | Sarah Jenkins (Senior Consultant) | `sales@venturevisionary.qa` | `Admin@VV2026!` | Staff |
| `visa_officer` | Mohammed Hassan (Visa Officer) | `visa@venturevisionary.qa` | `Admin@VV2026!` | Staff |
| `qc_officer` | Elena Rostova (QC Specialist) | `qc@venturevisionary.qa` | `Admin@VV2026!` | Staff |
| `accounts_manager` | Farhan Qureshi (Finance Controller) | `accounts@venturevisionary.qa` | `Admin@VV2026!` | Staff |
| `research_officer` | Dr. Ziad Nabil (Immigration Intel) | `research@venturevisionary.qa` | `Admin@VV2026!` | Staff |
| `b2b_agent` | Jassim Al-Sulaiti (Doha Global Travels)| `partner@dohaglobaltravel.qa`| `Partner@2026!` | Partner (`/partner`) |
| `client` | Sheikh Khalid Al-Kuwari | `client@example.qa` | `Client@2026!` | Client (`/portal`) |

---

## API Endpoints & Omnichannel Webhooks

All public API endpoints are documented with OpenAPI schemas at `/docs/api`.

### 1. Inbound Public Website Lead Capture
```http
POST /api/v1/leads
Content-Type: application/json

{
  "name": "Nasser Al-Khelaifi",
  "phone": "+97455112233",
  "whatsapp": "+97455112233",
  "email": "nasser@example.qa",
  "nationality": "Qatari",
  "destination_country": "United Kingdom",
  "visa_or_service_type": "Tourist Visa",
  "planned_travel_date": "2026-12-15",
  "source_code": "website"
}
```

### 2. Meta Ads (Facebook & Instagram) Webhook
```http
POST /api/v1/webhooks/meta-leads
Content-Type: application/json

{
  "full_name": "Fatima Al-Marri",
  "phone_number": "+97466778899",
  "email": "fatima@example.qa",
  "destination": "France (Schengen)",
  "service_interest": "Tourist Visa",
  "campaign_name": "Winter Europe Campaign Q4"
}
```

### 3. WhatsApp Direct Inbound Webhook (Evolution API Format)
```http
POST /api/v1/webhooks/whatsapp
Content-Type: application/json

{
  "data": {
    "key": {
      "remoteJid": "97455889900@s.whatsapp.net"
    },
    "pushName": "Ali Al-Hammadi",
    "message": {
      "conversation": "Hello, I want to inquire about UK Visa processing."
    }
  }
}
```

### 4. Public Destinations & Services (For Lead Forms)
```http
GET /api/v1/destinations
Accept: application/json
```

### 5. Inbound Leads Fetch & Live Stream
```http
GET /api/v1/leads?limit=15
Accept: application/json
```

### 6. Public Lead Status Lookup
```http
GET /api/v1/leads/lookup?query=LD-2026-0001
Accept: application/json
```

---

## Codebase Directory Layout

```
├── app/
│   ├── Filament/
│   │   ├── Client/                  # Client Portal Resources & Pages (/portal)
│   │   │   └── Resources/VisaCaseResource/
│   │   ├── Partner/                 # B2B Partner Portal Resources & Pages (/partner)
│   │   │   ├── Resources/VisaCaseResource/
│   │   │   └── Widgets/PartnerStatsOverview.php
│   │   ├── Resources/               # Staff Admin Resources (/staff)
│   │   │   ├── ClientResource.php   # Client 360 Profiles
│   │   │   ├── CountryResource.php  # 193-Country Visa Directory
│   │   │   ├── LeadResource.php     # Lead Pipeline & Conversion
│   │   │   ├── PartnerResource.php  # B2B Corporate Agency Accounts
│   │   │   ├── QuotationResource.php# Fee Breakdown & Quotations
│   │   │   ├── ServiceResource.php  # Visa & Consultancy Service Master
│   │   │   ├── UserResource.php     # Staff Directory & RBAC Assignment
│   │   │   └── VisaCaseResource.php # Case Processing & Document QC
│   │   └── Widgets/
│   │       └── StatsOverview.php    # Executive KPI Dashboard Widgets
│   ├── Http/
│   │   └── Middleware/              # Adaptive Cross-Portal Auth Middleware
│   ├── Models/                      # Eloquent UUID Models (Clients, Cases, Leads, etc.)
│   ├── Providers/
│   │   └── Filament/                # StaffPanelProvider, PartnerPanelProvider, ClientPanelProvider
│   └── Services/
│       └── LeadService.php          # Lead Ingestion, Duplicate Detection, & Round Robin
├── database/
│   ├── migrations/                  # Relational Schema with JSONB Snapshots
│   └── seeders/
│       └── DatabaseSeeder.php       # Seed 12 Roles, 10 Users, Services, Countries, Cases
├── docker/
│   ├── Dockerfile                   # Alpine PHP 8.3 FPM + Extensions
│   └── nginx.conf                   # High-Performance Nginx Config
├── routes/
│   ├── api.php                      # Public Ingestion APIs & Webhooks
│   └── web.php                      # System Status Route
├── tests/
│   └── Feature/                     # PHPUnit Automation Test Suite
└── docker-compose.yml               # Multi-Container Orchestration
```

---

## Automated & Browser Testing

### Running the Automated PHPUnit Suite
```bash
docker compose exec app php artisan test
```

### Test Coverage Highlights
- `LeadPipelineTest`: Verifies API ingestion, WhatsApp webhook parsing, sequential lead formatting (`LD-YYYY-XXXX`), duplicate detection, and conversion into Master Client profiles.
- `PartnerIsolationTest`: Validates row-level isolation ensuring partner agencies cannot view external tenant records.
- `PortalsTest`: Checks that `/staff`, `/partner`, `/portal`, and `/docs/api` render with correct authentication and redirection guards.
- `VisaCaseSnapshotTest`: Verifies that case creation freezes immutable pricing and checklist snapshots, protecting against retroactive master price adjustments.

---

## License

Proprietary & Confidential. All rights reserved by **Venture Visionary Visa and Business Consultancy**, Doha, Qatar.
