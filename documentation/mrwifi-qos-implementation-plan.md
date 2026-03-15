# MrWiFi QoS Implementation Plan
## Cloud Controller + OpenWRT Device

> **Scope:** SNI-based traffic prioritization using DSCP marking across multi-tenant cloud controller and OpenWRT devices. No bandwidth limiting. CAKE not available on current hardware — using nftables + mqprio.

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────┐
│              Cloud Controller (Multi-Tenant)         │
│                                                      │
│  SuperAdmin → Manage global DSCP classes & domains   │
│  Admin      → Enable/disable QoS per location        │
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
| SuperAdmin | Add/edit/delete domains within pre-defined DSCP classes. Classes are fixed.   |
| Admin      | Enable/disable QoS per location. Read-only view of global classes & domains.  |
| User       | Enable/disable QoS per location. Read-only view of global classes & domains.  |

---

## 1.2 Data Models

### Global DSCP Classes (Pre-defined, fixed — SuperAdmin adds domains only)

Classes are pre-defined and cannot be created or deleted. SuperAdmin can only manage the domain list within each class.

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
    "config_version": "v1.4",
    "networks": {
      "br-home":  { "policy": "full",      "trust_client_dscp": true  },
      "br-guest": { "policy": "scavenger", "trust_client_dscp": false },
      "br-iot":   { "policy": "scavenger", "trust_client_dscp": false }
    },
    "rules": [
      {
        "class": "EF",
        "domains": ["meet.google.com", "*.zoom.us", "*.teams.microsoft.com", "*.webex.com"]
      },
      {
        "class": "AF41",
        "domains": ["*.netflix.com", "*.nflxvideo.net", "*.youtube.com", "*.googlevideo.com", "*.hotstar.com"]
      },
      {
        "class": "CS1",
        "domains": ["*.dropbox.com", "*.onedrive.com", "*.drive.google.com", "*.amazonaws.com"]
      }
    ]
  }
}
```

> The `rules` block is compiled from the global DSCP class domain lists managed by SuperAdmin. Admin and User both control `qos.enabled` via location settings. Neither can edit rules or domains.

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
    "config_version": "v1.4",
    "networks": {
      "br-home":  { "policy": "full",      "trust_client_dscp": true  },
      "br-guest": { "policy": "scavenger", "trust_client_dscp": false },
      "br-iot":   { "policy": "scavenger", "trust_client_dscp": false }
    },
    "rules": [
      {
        "id": "rule_001",
        "dscp_class": "EF",
        "domains": ["meet.google.com", "*.zoom.us", "*.teams.microsoft.com", "*.webex.com"]
      },
      {
        "id": "rule_002",
        "dscp_class": "AF41",
        "domains": ["*.netflix.com", "*.nflxvideo.net", "*.youtube.com", "*.googlevideo.com", "*.hotstar.com"]
      },
      {
        "id": "rule_003",
        "dscp_class": "CS1",
        "domains": ["*.dropbox.com", "*.onedrive.com", "*.drive.google.com", "*.amazonaws.com"]
      }
    ]
  }
}
```

When `qos.enabled` is `false`, the `rules` and `networks` blocks may be omitted. The device is responsible for flushing any existing QoS rules on receiving `enabled: false`.

---

## 1.4 API Endpoints

### SuperAdmin — Global QoS Class Management

```
GET    /api/v1/qos/classes                         List all DSCP classes with their domains
GET    /api/v1/qos/classes/:id                     Get single class with full domain list
POST   /api/v1/qos/classes/:id/domains             Add a domain to a class
DELETE /api/v1/qos/classes/:id/domains/:domain     Remove a domain from a class
```

> Classes themselves (EF, AF41, BE, CS1) are pre-defined and cannot be created or deleted via the API. Only the domain lists are editable. Saving any domain change bumps `config_version` globally.

### Admin & User — Location Level

```
GET    /api/v1/locations/:id/settings          Get full location settings (includes qos block)
PUT    /api/v1/locations/:id/settings/qos      Enable/disable QoS for location { "enabled": true/false }
```

> No separate QoS config delivery endpoint is needed. Config reaches the device through the existing `/settings` pull.

---

## 1.5 Config Delivery Flow

```
SuperAdmin edits / adds SNI rule
        ↓
Controller bumps config_version
        ↓
Rules compiled into qos.rules block in location settings
        ↓
Device calls /settings (existing on-demand logic)
        ↓
Device receives full settings payload including qos block
        ↓
Device compares qos.config_version with stored version
        ↓
If changed (or first apply): regenerate nftables + tc rules
If unchanged: skip, no-op
```

> **Versioning:** `config_version` is a string (e.g. `v1.4`) that increments whenever SuperAdmin saves a rule change or an Admin/User toggles QoS. The device stores the last applied version and skips re-applying if unchanged.

---

## 1.6 UI Screens

### SuperAdmin — Global QoS Settings

Accessible under a global "QoS Settings" section, not per-location.

- Four pre-defined class cards: Real-time (EF), Streaming (AF41), Default (BE), Background (CS1)
- Each card shows the class label, DSCP value, priority level, and its domain list
- SuperAdmin can add or remove domains within each class
- BE card has no domain list (informational only — unmatched traffic)
- No option to create, delete, or reorder classes
- Save button bumps `config_version`

### Admin & User — Location QoS

- Per-location toggle: Enable / Disable QoS
- Read-only list of active classes and their domains (what will be enforced if enabled)
- No editing of any rule or domain

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
# Config version: v1.4
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
