# MrWiFi QoS Implementation Plan
## Cloud Controller + OpenWRT Device

> **Scope:** SNI-based traffic prioritization using DSCP marking across multi-tenant cloud controller and OpenWRT devices. No bandwidth limiting. CAKE not available on current hardware — using nftables + mqprio.

**Per-network domain lists (2026):** SNI → DSCP class domain lists are stored per **location WiFi network** (`location_network_qos_domains`), not as a single global list. The device JSON carries **`rules` inside each bridge** (`qos.networks[br-*].rules`). Top-level `qos.rules` is always `[]` (deprecated). `config_version` is an `md5` of all per-network domain rows for the location’s networks so any domain change forces re-apply.

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────┐
│              Cloud Controller (Multi-Tenant)         │
│                                                      │
│  SuperAdmin → Class metadata (DSCP, nft mark)        │
│  Admin      → Per-network domain lists, QoS on/off   │
│  User       → View only                              │
│                                                      │
│  QoS config stored in location settings              │
│  Served via existing /settings endpoint              │
└──────────────────────┬──────────────────────────────┘
                       │ existing /settings pull
                       │ (on-demand, controlled by
                       │  existing device logic)
┌──────────────────────▼──────────────────────────────┐
│              OpenWRT Device (MrWiFi Router)          │
│                                                      │
│  Existing settings handler → apply_qos() hook        │
│  nftables     → SNI inspect → DSCP mark              │
│  mqprio       → enforce priority order on WAN        │
│                                                      │
│  br-home / br-guest / br-iot → per-network policy    │
└─────────────────────────────────────────────────────┘
```

---

# Part 1 — Cloud Controller

## 1.1 Role Hierarchy & Permissions

| Role       | Can Do                                                                        |
|------------|-------------------------------------------------------------------------------|
| SuperAdmin | View fixed DSCP class metadata (read-only in UI; global domain POST/DELETE return **410**). |
| Admin      | Enable/disable QoS per location; **add/remove domains per WiFi network** in the location Networks drawer. |
| User       | Enable/disable QoS per location. Read-only; domain lists as enforced per network.  |

---

## 1.2 Data Models

### Global DSCP Classes (Pre-defined, fixed; domains are per network)

Classes are pre-defined and cannot be created or deleted. **Domain lists** are **not** global: each `location_networks` row has its own rows in `location_network_qos_domains` (EF, AF41, CS1; BE has no list). The JSON below is illustrative; the controller may return **`domains: []`** on `GET /api/qos/classes` because lists are edited per network.

```json
{
  "classes": [
    {
      "id": "EF",
      "value": 46,
      "label": "Real-time",
      "priority": 0,
      "description": "Video calls, VoIP — highest priority",
      "editable": false,
      "domains": [
        "meet.google.com",
        "*.zoom.us",
        "*.teams.microsoft.com",
        "*.webex.com",
        "*.discord.com"
      ]
    },
    {
      "id": "AF41",
      "value": 34,
      "label": "Streaming",
      "priority": 1,
      "description": "Video streaming",
      "editable": false,
      "domains": [
        "*.netflix.com",
        "*.nflxvideo.net",
        "*.youtube.com",
        "*.googlevideo.com",
        "*.hotstar.com"
      ]
    },
    {
      "id": "BE",
      "value": 0,
      "label": "Default",
      "priority": 2,
      "description": "General browsing — no explicit marking needed",
      "editable": false,
      "domains": []
    },
    {
      "id": "CS1",
      "value": 8,
      "label": "Background",
      "priority": 3,
      "description": "Backups, cloud sync, IoT, Guest networks",
      "editable": false,
      "domains": [
        "*.dropbox.com",
        "*.onedrive.com",
        "*.drive.google.com",
        "*.amazonaws.com"
      ]
    }
  ]
}
```

> `BE` (Best Effort) has no domain list — unmatched traffic falls into this class automatically. No SNI rules are generated for it.

### Location Settings — QoS Block (Admin managed)

QoS enable/disable is stored as part of the location settings object. Both **Admin and User** can toggle this — it applies to all devices at the location.

```json
{
  "location_id": "loc_xyz",
  "tenant_id": "tenant_001",
  "wifi": { "...": "..." },
  "captive_portal": { "...": "..." },
  "qos": {
    "enabled": true,
    "config_version": "a1b2c3d4e5f6789012345678abcdef12",
    "networks": {
      "br-home": {
        "policy": "full",
        "trust_client_dscp": true,
        "rules": [
          { "id": "rule_ef", "dscp_class": "EF", "nft_mark": 46, "domains": ["meet.google.com"] },
          { "id": "rule_af41", "dscp_class": "AF41", "nft_mark": 34, "domains": ["*.youtube.com"] },
          { "id": "rule_cs1", "dscp_class": "CS1", "nft_mark": 8, "domains": [] }
        ]
      },
      "br-guest": {
        "policy": "scavenger",
        "trust_client_dscp": false,
        "rules": [
          { "id": "rule_ef", "dscp_class": "EF", "nft_mark": 46, "domains": [] },
          { "id": "rule_af41", "dscp_class": "AF41", "nft_mark": 34, "domains": [] },
          { "id": "rule_cs1", "dscp_class": "CS1", "nft_mark": 8, "domains": ["*.dropbox.com"] }
        ]
      }
    },
    "rules": []
  }
}
```

> **`rules` at the top level is always an empty array.** Per-bridge `rules` are compiled from `location_network_qos_domains` for each WiFi network. `config_version` is an MD5 of the sorted snapshot of all domain rows for networks at the location. Admin and User control `qos.enabled` via location settings; **domain lists** are edited per network in the Networks UI (for authorized roles).

---

## 1.3 Settings Endpoint — QoS Delivery

QoS config is delivered as part of the existing `/settings` endpoint response. No separate QoS endpoint or push mechanism is needed.

```
GET /settings?device_id=device_abc123
```

The device calls this on demand, controlled by existing device logic. The response includes the full location settings with the `qos` block embedded:

```json
{
  "device_id": "device_abc123",
  "location_id": "loc_xyz",
  "wifi": { "...": "..." },
  "captive_portal": { "...": "..." },
  "qos": {
    "enabled": true,
    "config_version": "a1b2c3d4e5f6789012345678abcdef12",
    "networks": {
      "br-home": {
        "policy": "full",
        "trust_client_dscp": true,
        "rules": [
          {
            "id": "rule_ef",
            "dscp_class": "EF",
            "nft_mark": 46,
            "domains": ["meet.google.com", "*.zoom.us"]
          },
          {
            "id": "rule_af41",
            "dscp_class": "AF41",
            "nft_mark": 34,
            "domains": ["*.youtube.com"]
          },
          {
            "id": "rule_cs1",
            "dscp_class": "CS1",
            "nft_mark": 8,
            "domains": ["*.dropbox.com"]
          }
        ]
      },
      "br-guest": {
        "policy": "scavenger",
        "trust_client_dscp": false,
        "rules": [
          { "id": "rule_ef", "dscp_class": "EF", "nft_mark": 46, "domains": [] },
          { "id": "rule_af41", "dscp_class": "AF41", "nft_mark": 34, "domains": [] },
          { "id": "rule_cs1", "dscp_class": "CS1", "nft_mark": 8, "domains": [] }
        ]
      }
    },
    "rules": []
  }
}
```

Firmware should use **per-bridge** `rules` only (ignore or treat top-level `rules` as deprecated). When `qos.enabled` is `false`, the `rules` and `networks` blocks may be omitted. The device is responsible for flushing any existing QoS rules on receiving `enabled: false`.

---

## 1.4 API Endpoints

### SuperAdmin — Global QoS Class (metadata only; domains per network)

```
GET    /api/qos/classes                            List all DSCP classes (domain arrays may be empty; use per-network API)
GET    /api/qos/classes/:id                        Get single class
POST   /api/qos/classes/:id/domains                **410 Gone** — use per-network endpoints
DELETE /api/qos/classes/:id/domains/:domain        **410 Gone** — use per-network endpoints
```

> Classes (EF, AF41, BE, CS1) are pre-defined. **Domain mutation** is via location network routes (same auth as other network operations):

```
GET    /api/locations/:locationId/networks/:networkId/qos-domains
POST   /api/locations/:locationId/networks/:networkId/qos-domains
DELETE /api/locations/:locationId/networks/:networkId/qos-domains/:classId?domain=...
```

> Successful domain add/delete increments the device **configuration version** (same as other network field updates).

### Admin & User — Location Level

```
GET    /api/v1/locations/:id/settings          Get full location settings (includes qos block)
PUT    /api/v1/locations/:id/settings/qos      Enable/disable QoS for location { "enabled": true/false }
```

> No separate QoS config delivery endpoint is needed. Config reaches the device through the existing `/settings` pull.

---

## 1.5 Config Delivery Flow

```
Admin edits per-network SNI domain (or new network gets defaults copied from old global table at migration)
        ↓
location_network_qos_domains updated; configuration_version on device/location bumps
        ↓
Per-bridge rules compiled into qos.networks[br-*].rules; qos.rules = []
        ↓
Device calls /settings (existing on-demand logic)
        ↓
Device receives full settings payload including qos block
        ↓
Device compares qos.config_version with stored version
        ↓
If changed (or first apply): regenerate nftables + tc rules per bridge
If unchanged: skip, no-op
```

> **Versioning:** `config_version` is a **32-char hex string** (MD5 of the serialized per-network domain rows for that device’s networks). It changes when any of those domain rows change. Toggle QoS enable/disable and other location settings use the existing `configuration_version` / apply logic.

---

## 1.6 UI Screens

### SuperAdmin — Global QoS Settings

Accessible under a global "QoS Settings" section, not per-location.

- Four pre-defined class cards: Real-time (EF), Streaming (AF41), Default (BE), Background (CS1)
- **Read-only** class metadata: label, DSCP value, `nft_mark`, priority description
- **No** add/remove domain UI; copy explains that domain lists are managed **per WiFi network** on each Location
- No option to create, delete, or reorder classes

### Admin & User — Location QoS and Networks

- Per-location toggle: Enable / Disable QoS (and bandwidth fields as before)
- **Per WiFi network** (Networks drawer, Advanced / QoS section): add/remove SNI domains for EF, AF41, and CS1 for that SSID/bridge; BE remains catch-all
- Users with view-only access: no domain editing
- Router tab may show class **definitions** from `GET /api/qos/classes` with copy that **domain lists are per network**

---

# Part 2 — OpenWRT Device

## 2.1 Settings Handler — QoS Hook

No separate agent or polling loop is needed. QoS apply logic hooks directly into the existing settings download handler.

```php
// Existing settings apply function (simplified)
function applySettings(array $payload): void
{
    applyWifiSettings($payload['wifi']);
    applyCaptivePortal($payload['captive_portal']);

    // New — QoS handler
    if (isset($payload['qos'])) {
        applyQos($payload['qos']);
    }
}
```

```php
function applyQos(array $qos): void
{
    if (!$qos['enabled']) {
        flushQosRules();
        file_put_contents('/etc/mrwifi/qos_version', 'disabled');
        return;
    }

    $currentVersion = @file_get_contents('/etc/mrwifi/qos_version');
    if (trim($currentVersion) === $qos['config_version']) {
        return; // Already applied, skip
    }

    $nftRules  = generateNftables($qos);
    $tcScript  = generateTc($qos);

    file_put_contents('/etc/nftables.d/mrwifi-qos.nft', $nftRules);
    file_put_contents('/etc/mrwifi-qos-tc.sh', $tcScript);

    shell_exec('nft -f /etc/nftables.d/mrwifi-qos.nft');
    shell_exec('sh /etc/mrwifi-qos-tc.sh');

    file_put_contents('/etc/mrwifi/qos_version', $qos['config_version']);
}

function flushQosRules(): void
{
    shell_exec('nft delete table inet mrwifi_qos 2>/dev/null');
    shell_exec('tc qdisc del dev wan root 2>/dev/null');
}
```

---

## 2.2 nftables Rules (Generated from Config)

The agent generates this file dynamically from the JSON payload:

```
/etc/nftables.d/mrwifi-qos.nft   ← auto-generated, do not edit manually
```

### Generated Output Example

```nft
#!/usr/sbin/nft -f
# Auto-generated by mrwifi-qos-agent
# Config version: <md5 from qos.config_version>
# Generated: 2025-03-10T08:01:00Z

table inet mrwifi_qos {

    chain prerouting {
        type filter hook prerouting priority mangle; policy accept;

        # Per-network policy routing
        iifname "br-home"  jump policy_full
        iifname "br-guest" jump policy_scavenger
        iifname "br-iot"   jump policy_scavenger

        # Restore conntrack mark on established flows
        ct state established,related ct mark != 0 meta mark set ct mark
    }

    # Full policy — SNI inspect + honour client DSCP
    chain policy_full {
        tcp dport 443 ct state new jump sni_inspect
        ct mark != 0 meta mark set ct mark
    }

    # Scavenger policy — blanket CS1 (background/lowest priority)
    chain policy_scavenger {
        ct mark set 0x08
        meta mark set 0x08
    }

    # SNI inspection chain — generated from cloud rules
    chain sni_inspect {

        # rule_001 — EF (Real-time)
        tls sni "meet.google.com"          ct mark set 0x2e
        tls sni "*.zoom.us"                ct mark set 0x2e
        tls sni "*.teams.microsoft.com"    ct mark set 0x2e
        tls sni "*.webex.com"              ct mark set 0x2e

        # rule_002 — AF41 (Streaming)
        tls sni "*.netflix.com"            ct mark set 0x22
        tls sni "*.nflxvideo.net"          ct mark set 0x22
        tls sni "*.youtube.com"            ct mark set 0x22
        tls sni "*.googlevideo.com"        ct mark set 0x22
        tls sni "*.hotstar.com"            ct mark set 0x22

        # rule_003 — CS1 (Background)
        tls sni "*.dropbox.com"            ct mark set 0x08
        tls sni "*.onedrive.com"           ct mark set 0x08
        tls sni "*.drive.google.com"       ct mark set 0x08
        tls sni "*.amazonaws.com"          ct mark set 0x08

        # Unmatched → no mark, BE (default) applies
    }

    chain postrouting {
        type filter hook postrouting priority mangle; policy accept;

        # Apply DSCP on all packets leaving via WAN
        oifname "wan" ct mark != 0 ip dscp set ct mark
    }
}
```

---

## 2.3 Priority Enforcement (mqprio)

No bandwidth limits — pure priority ordering on WAN egress:

```bash
#!/bin/sh
# /etc/mrwifi-qos-tc.sh
# Auto-applied by agent alongside nftables rules

WAN="wan"   # resolved from UCI at runtime

tc qdisc del dev $WAN root 2>/dev/null

# 4-band priority queue, no rate limits
# map: 16 DSCP bins → band (0=highest, 3=lowest)
tc qdisc add dev $WAN root handle 1: mqprio \
    num_tc 4 \
    map 3 3 3 3 2 3 3 3 1 3 3 3 3 3 3 1 \
    queues 1@0 1@1 1@2 1@3 \
    hw 0

# Band → DSCP mapping:
#  Band 0 (highest) ← EF   (46)  Real-time
#  Band 1           ← AF41 (34)  Streaming
#  Band 2           ← BE   ( 0)  Default
#  Band 3 (lowest)  ← CS1  ( 8)  Background / Guest / IoT
```

---

## 2.4 QUIC / HTTP3 Heuristic

SNI inspection only works on TCP 443. For QUIC (UDP 443), add a heuristic for home network:

```nft
    chain prerouting {
        # ... existing rules ...

        # QUIC from home network — treat as streaming priority
        iifname "br-home" udp dport 443 ct state new     ct mark set 0x22
        iifname "br-home" udp dport 443 ct state established meta mark set ct mark
    }
```

---

## 2.5 Persistence on Reboot

```bash
# /etc/rc.local — runs on every boot

# Re-apply nftables QoS rules if present
if [ -f /etc/nftables.d/mrwifi-qos.nft ]; then
    nft -f /etc/nftables.d/mrwifi-qos.nft
fi

# Re-apply tc priority rules
if [ -f /etc/mrwifi-qos-tc.sh ]; then
    sh /etc/mrwifi-qos-tc.sh
fi
```

---

## 2.6 Debugging & Verification

```bash
# Check active nftables QoS table
nft list table inet mrwifi_qos

# Check conntrack marks on active flows
conntrack -L | grep mark | grep -v "mark=0"

# Check WAN priority queue
tc qdisc show dev wan
tc -s qdisc show dev wan    # with packet counters per band

# Watch SNI matching in real time
nft monitor trace

# Confirm current config version on device
cat /etc/mrwifi/qos_version
```

---

## 2.7 Known Limitations

| Limitation | Impact | Notes |
|---|---|---|
| ECH (Encrypted Client Hello) | SNI will be hidden when ECH is widespread | Fall back to port/protocol heuristics |
| QUIC / HTTP3 (UDP 443) | Not caught by TCP SNI chain | Handled by separate heuristic rule |
| Prioritization only | No bufferbloat control | CAKE would solve this — consider building with it |
| Download-side priority | Limited effect without shaping | Bottleneck is upstream of router |
| First-packet SNI only | Must rely on conntrack for rest of flow | Already handled via `ct mark` |

---

## Summary

| Concern | Owner | Mechanism |
|---|---|---|
| DSCP classes (fixed) | Pre-defined | EF / AF41 / BE / CS1 — not configurable |
| Domains within each class | SuperAdmin | Global QoS settings — add/remove domains |
| QoS enable/disable | Admin & User | Per-location toggle in location settings |
| Config delivery to device | Existing `/settings` pull | QoS block compiled into settings response |
| SNI inspection | Device | nftables `tls sni` match |
| DSCP marking | Device | nftables `ct mark` + `ip dscp set` |
| Priority enforcement | Device | `mqprio` on WAN egress |
| Per-network policy | Device | Per-bridge nftables chains |

---

*Document version: 4.0 — MrWiFi QoS / OpenWRT 24.10 / nftables / mqprio*
