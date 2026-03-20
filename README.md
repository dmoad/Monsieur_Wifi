# Monsieur WiFi — Cloud Controller

A Laravel-based cloud management platform for WiFi routers. It handles multi-location provisioning, captive portal management, guest authentication, traffic policy (QoS), domain blocking, firmware delivery, and e-commerce — all served to OpenWRT-based routers via a device API.

---

## Tech Stack

- **Backend:** Laravel (PHP), MySQL
- **Auth:** Zitadel JWT (JWKS validation) + local role resolution via `AuthzService`
- **RADIUS:** FreeRADIUS with MySQL backend (`radcheck` / `radacct`)
- **Frontend:** Blade templates + vanilla JS (jQuery, Feather Icons, Toastr)
- **Payments:** Stripe (subscriptions + one-time orders)
- **Localisation:** English (`/en/`) and French (`/fr/`) routes throughout

---

## Key Features

### Device & Location Management
- Devices are provisioned via a key/secret pair and pull their full configuration from the `v2-settings` endpoint
- Locations group one or more networks (SSIDs); each network has independent settings (VLAN, captive portal, bandwidth limits, QoS policy)
- Locations are organised into Zones, which belong to Organisations

### Multi-Network Support (`location_networks`)
- Each location supports multiple networks, each with its own SSID, VLAN, captive portal design, idle timeout, bandwidth limits, and QoS policy (`full` / `scavenger`)
- Networks replaced the legacy flat `location_settings` columns

### Captive Portal
- Per-network captive portal designs (logo, colours, terms, gradients)
- Working hours and hourly schedule control when the portal is active
- Guest users authenticate via MAC address + OTP; sessions are tracked in `radacct`

### Guest Authentication (RADIUS)
- Guest users are scoped by `network_id` in the `radcheck` table (previously `location_id`)
- FreeRADIUS `unlang` policy strips the `n` prefix from `NAS-Identifier` to resolve `network_id`, then looks up credentials and applies per-network bandwidth limits (`download_limit` / `upload_limit`, defaulting to 5 Mbps via `COALESCE`)

### Traffic Prioritisation (QoS)
- SNI-based DSCP marking on the router; no bandwidth shaping — priority only
- Four fixed DSCP classes, managed globally by SuperAdmins:

  | Class | Label       | Priority                                     | DSCP |
  |-------|-------------|----------------------------------------------|------|
  | EF    | Real-Time   | Highest — lowest latency guaranteed          | 46   |
  | AF41  | Streaming   | High — less than Real-Time                   | 34   |
  | BE    | Default     | Normal — unmatched traffic & QoS-disabled    | 0    |
  | CS1   | Background  | Lowest — deferred when congested             | 8    |

- SuperAdmins manage the global domain-to-class mapping at `/en/qos-settings` (or `/fr/parametres-qos`)
- Admins toggle QoS on/off per location; each network independently selects `full` or `scavenger` policy
- The `v2-settings` device API compiles and delivers the full QoS config block (classes, domains, per-network policy, config version hash) to the router

### Domain Blocking
- Admins can block domains per organisation, organised into categories
- Categories can be toggled on/off; individual domains can be added, imported (CSV), or bulk-deleted

### Firmware Management
- SuperAdmins upload firmware builds per router model
- Routers pull the correct firmware via the device API using their model identifier

### E-Commerce & Subscriptions
- Shop with product models, inventory tracking, cart, orders, and Stripe payment
- Stripe subscription management (checkout, cancel, resume, billing portal)
- Admin order management: tracking, status updates, inventory assignment

### Dashboard & Analytics
- Per-organisation overview: device count, location count, active sessions
- Data usage trends (daily download/upload GB per location)
- Requires `X-Org-Id` header or `current_organization_id` on the user for the authz middleware to resolve the correct org scope

---

## Role Hierarchy

| Role        | Scope                                                                 |
|-------------|-----------------------------------------------------------------------|
| superadmin  | Full access — QoS domain management, firmware, system settings        |
| admin       | Org-level — locations, networks, accounts, domain blocking, QoS toggle |
| operator    | Write access on zones/locations/devices                               |
| viewer      | Read-only                                                             |
| partner     | Device-level write, no org manage                                     |

Roles are resolved at request time from the Zitadel authz service and cached for 60 seconds per user.

---

## Device API (unauthenticated, key/secret auth)

| Endpoint | Description |
|----------|-------------|
| `GET /api/devices/{key}/{secret}/v2-settings` | Full router config: networks, captive portal, RADIUS, QoS block |
| `GET /api/devices/{key}/{secret}/settings` | Legacy settings endpoint |
| `GET /api/devices/{key}/{secret}/heartbeat` | Router heartbeat / online status |
| `GET /api/devices/{key}/{secret}/firmware` | Firmware info for this device's model |
| `POST /api/devices/{key}/{secret}/clients` | Push connected client list |

---

## Getting Started

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Configure `.env` with:
- `DB_*` — MySQL connection
- `ZITADEL_ISSUER`, `ZITADEL_PROJECT_ID` — JWT auth
- `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` — payments
- `AUTHZ_*` — permission service connection

---

## Localisation

All user-facing pages exist in English (`/en/`) and French (`/fr/`). The navbar language switcher translates route slugs automatically via a mapping table in `navbar.blade.php`.
