# Firmware Update Note — `ip_mode: bridge_lan`

> **Introduced:** 2026-04-13
> **Affects:** `GET /api/devices/{key}/{secret}/v2-settings` and `GET /api/devices/{key}/{secret}/settings`
> **Field:** `networks[*].ip_mode`

---

## What changed

A new `ip_mode` value `bridge_lan` has been added to replace the retired `dhcp` value.

The old `dhcp` value (DHCP client on LAN) is **retired**. No production devices should carry it, but treat it as an alias for `bridge_lan` + `dhcp_client` if encountered in the wild.

---

## `ip_mode` values — old vs new

| `ip_mode` | Status | Meaning |
|---|---|---|
| `static` | unchanged | Manual IP assignment, optional DHCP server |
| `dhcp` | **retired** | _(was: DHCP client on LAN interface — treat as `bridge_lan` + `dhcp_client`)_ |
| `bridge_lan` | **new** | Bridge to LAN port — sub-mode controlled by `bridge_lan_dhcp_mode` |
| `bridge` | unchanged | Bridge to WAN port, no IP stack on this interface |

---

## New field: `bridge_lan_dhcp_mode`

Only present (non-null) when `ip_mode == "bridge_lan"`.

| Value | Firmware behaviour |
|---|---|
| `dhcp_client` | Bridge to LAN port and obtain IP from upstream via DHCP. No DHCP server. IP/DHCP fields will be `null`. |
| `dhcp_server` | Bridge to LAN port and serve IPs to downstream clients. `ip_address` / `netmask` are set. `dhcp_enabled: true`, `dhcp_start` and `dhcp_end` (pool size) are populated. |

### Captive Portal constraint

**`bridge_lan` is not permitted for Captive Portal networks.**

The captive portal requires a routable IP on the interface to redirect unauthenticated clients. Both `bridge` (WAN) and `bridge_lan` (LAN) are hidden and disabled in the UI for `captive_portal` networks. Any network whose stored `ip_mode` is `bridge` or `bridge_lan` but whose type is `captive_portal` is coerced to `static` on load in the UI and rejected with a 422 error at the API layer.

> **Firmware defensive check:** if a payload ever arrives with `type == "captive_portal"` and `ip_mode == "bridge_lan"` or `ip_mode == "bridge"`, treat it as `ip_mode == "static"` and log a warning.

### Per-location bridge exclusivity

Each bridge mode (`bridge` and `bridge_lan`) may be used by **at most one network per location**. The API returns a 422 error if a second network attempts to claim an already-used bridge mode. The UI disables the conflicting option and shows an explanatory note on any pane that cannot use it.

---

## Payload shapes

### `bridge_lan` + `dhcp_client`

```json
{
  "ip_mode": "bridge_lan",
  "bridge_lan_dhcp_mode": "dhcp_client",
  "ip_address": null,
  "netmask": null,
  "gateway": null,
  "dns1": null,
  "dns2": null,
  "dhcp_enabled": false,
  "dhcp_start": null,
  "dhcp_end": null,
  "dhcp_end_ip": null
}
```

### `bridge_lan` + `dhcp_server`

```json
{
  "ip_mode": "bridge_lan",
  "bridge_lan_dhcp_mode": "dhcp_server",
  "ip_address": "192.168.x.1",
  "netmask": "255.255.255.0",
  "gateway": null,
  "dns1": "8.8.8.8",
  "dns2": "8.8.4.4",
  "dhcp_enabled": true,
  "dhcp_start": "192.168.x.100",
  "dhcp_end": 101,
  "dhcp_end_ip": "192.168.x.200",
  "dhcp_reservations": [
    { "mac": "AA:BB:CC:DD:EE:FF", "ip": "192.168.x.50" }
  ]
}
```

> **`dhcp_end` is a pool size** (integer count), not an end address.
> The last IP in the pool = `dhcp_start + dhcp_end - 1`.
> `dhcp_end_ip` is pre-computed by the API as a convenience if an end address is easier for the firmware to consume.

---

## Suggested firmware logic

```
switch ip_mode:

  "static":
    assign ip_address / netmask / gateway / dns statically
    if dhcp_enabled:
      apply dhcp_reservations (see below)
      run DHCP server over pool [dhcp_start .. dhcp_end_ip]

  "bridge_lan":
    bridge interface to LAN port
    effective_mode = bridge_lan_dhcp_mode ?? "dhcp_client"

    // Defensive: captive portal must never use dhcp_client
    if type == "captive_portal" and effective_mode == "dhcp_client":
      log WARNING "captive_portal + bridge_lan + dhcp_client is invalid — coercing to dhcp_server"
      effective_mode = "dhcp_server"

    if effective_mode == "dhcp_client":
      run DHCP client on bridged interface
      // ip_address / dhcp_start / dhcp_end will be null — do not configure them
    if effective_mode == "dhcp_server":
      assign ip_address / netmask to bridged interface
      apply dhcp_reservations (see below)
      run DHCP server over pool [dhcp_start .. dhcp_end_ip]

  "bridge":
    bridge interface to WAN port
    no IP stack / no DHCP (unchanged behaviour)

  "dhcp"  ← retired, treat as bridge_lan + dhcp_client
```

---

## New field: `dhcp_reservations`

> **Introduced:** 2026-04-13

A list of static MAC → IP mappings the DHCP server must honour. Present on any network that acts as a DHCP server.

### When it is populated

| `ip_mode` | `bridge_lan_dhcp_mode` | `type` | `dhcp_reservations` |
|---|---|---|---|
| `static` | — | any (incl. captive portal) | populated if `dhcp_enabled: true` (may be `[]`) |
| `bridge_lan` | `dhcp_server` | any | always populated (may be `[]`) |
| `bridge_lan` | `dhcp_client` | any | `null` |
| `bridge` | — | any | `null` |

> **Note:** Captive portal networks _can_ act as DHCP servers (e.g. `static` + `dhcp_enabled: true`). In that case `dhcp_reservations` will be present. Firmware should apply reservations regardless of network type.

### Payload shape

```json
{
  "dhcp_reservations": [
    { "mac": "AA:BB:CC:DD:EE:FF", "ip": "192.168.10.50" },
    { "mac": "11:22:33:44:55:66", "ip": "192.168.10.51" }
  ]
}
```

- `mac` — colon-separated uppercase hex, e.g. `"AA:BB:CC:DD:EE:FF"`
- `ip` — IPv4 address string; guaranteed by the API to be within the network's subnet
- When no reservations exist the field is an **empty array** `[]`
- The field is `null` only when the network cannot be a DHCP server: `bridge` mode, or `bridge_lan + dhcp_client`

### Firmware handling

```
for each reservation in network.dhcp_reservations:
    add static DHCP lease: bind reservation.mac → reservation.ip

// Host is authoritative: if a device with a listed MAC requests an
// address, always hand out the reserved IP regardless of its requested IP.
// All reserved IPs are excluded from the dynamic pool
// [dhcp_start .. dhcp_end_ip] to prevent conflicts.
```

> **Conflict guard:** the cloud UI validates that each reserved IP is within the subnet and is not the gateway address, and rejects duplicate MACs/IPs. Firmware should still skip any entry whose IP falls outside [dhcp_start .. dhcp_end_ip] and log a warning if encountered.

---

## MAC Address Filtering

> **Fields:** `networks[*].mac_filter_mode` and `networks[*].mac_filter_list`
> Both fields are always present in the payload regardless of `ip_mode` or `type`.

### Concept

MAC filtering is an **association-layer modifier** that sits on top of the network's normal authentication flow. By default every device goes through whatever auth the network requires (WPA2-PSK handshake, or captive portal redirect). The MAC list can either:

- **Hard-block** specific devices before auth even begins (`block-listed`)
- **Pre-authorise** specific devices so they skip the PSK or portal step (`allow-listed`) — **only relevant for `password` networks** (see captive portal note below)

### `mac_filter_list` format — unified per-entry type

Each entry in the list is an object with a `mac` and a `type` field:

```json
{
  "mac_filter_mode": "mixed",
  "mac_filter_list": [
    { "mac": "AA:BB:CC:DD:EE:01", "type": "bypass" },
    { "mac": "AA:BB:CC:DD:EE:02", "type": "bypass" },
    { "mac": "DE:AD:BE:EF:00:01", "type": "block"  }
  ]
}
```

| `type` | Meaning |
|---|---|
| `bypass` | This MAC is pre-authorised — it associates and gets internet access without going through PSK or captive portal. |
| `block`  | This MAC is hard-rejected at association — it cannot connect regardless of credentials. |

**`mac_filter_mode`** is a derived summary field computed from the list:

| Value | Meaning |
|---|---|
| `none` | List is empty — no MAC rules active |
| `allow-listed` | All entries are `bypass` |
| `block-listed` | All entries are `block` |
| `mixed` | Both `bypass` and `block` entries coexist |
| `allow-all` | *(retired legacy value — treat as `none`)* |

> Firmware should **not** rely on `mac_filter_mode` to decide behaviour. Read the `type` field on each entry directly.

- The field is always an array (never `null`) in the API response — the model casts it. An empty list `[]` means no rules are active.
- MACs are transmitted in **uppercase colon-notation**. Normalise client MACs before comparison.

### Captive portal networks — bypass is handled server-side, not by firmware

For `type == "captive_portal"` networks, **the firmware does not act on `bypass` entries**. The captive portal/RADIUS server manages which MACs are pre-approved. The firmware simply runs the normal association flow for all clients; the portal backend decides whether to redirect or grant access.

The **only** thing firmware does for captive portal + MAC filtering is the **`block` hard reject** at the 802.11 layer.

### Firmware handling

```
// Build lookup sets from the unified list
blocked_macs = {}
bypass_macs  = {}

for entry in (network.mac_filter_list ?? []):
    mac = uppercase(entry.mac)
    if entry.type == "block":
        blocked_macs.add(mac)
    elif entry.type == "bypass" and network.type != "captive_portal":
        // Only pre-authorise at firmware level for password/open networks.
        // Captive portal bypass is handled by the portal/RADIUS server.
        bypass_macs.add(mac)


// ── Per association attempt ──────────────────────────────────────────────

on_client_connect(client_mac, network):
    mac = uppercase(client_mac)

    // 1. Hard block — deny before any auth (all network types)
    if mac IN blocked_macs:
        REJECT association
        return

    // 2. Pre-authorise bypass (password / open networks only)
    if mac IN bypass_macs:
        ACCEPT association, grant full internet access immediately
        return

    // 3. Normal auth path (unchanged)
    proceed with standard WPA2/WPA3 handshake or captive portal redirect
```

> **Interaction with DHCP reservations:** hard-blocked devices never reach DHCP. Bypassed devices receive an IP normally and will get their reserved IP if a matching `dhcp_reservations` entry exists.

---

## API routes

| Route | Payload key | Notes |
|---|---|---|
| `GET /api/devices/{device_key}/{device_secret}/v2-settings` | `response.networks[*]` | Recommended — full structured payload |
| `GET /api/devices/{device_key}/{device_secret}/settings` | `response.networks[*]` | Legacy v1 — also includes `networks` array |

Both routes serialise `LocationNetwork` model rows via `toArray()`. All fields — `bridge_lan_dhcp_mode`, `dhcp_reservations`, `mac_filter_mode`, `mac_filter_list` — are included automatically in every network object. No special mapping is needed on the API side.

### Zone network coalescing (v2-settings only)

If a location belongs to a **zone** and is **not** the primary location, `getSettingsV2` automatically substitutes the primary location's `networks` rows in the response. This means all APs in a zone broadcast identical SSIDs and share the same captive portal configuration (enabling seamless roaming). WAN and radio settings remain per-device. Firmware does not need to do anything special — it simply processes whatever `networks` array it receives.

### Full network object reference (fields relevant to this document)

Example — captive portal with a DHCP reservation and a blocked device:

```json
{
  "id": 1,
  "ssid": "GuestPortal",
  "type": "captive_portal",
  "enabled": true,
  "ip_mode": "static",
  "bridge_lan_dhcp_mode": null,
  "ip_address": "192.168.10.1",
  "netmask": "255.255.255.0",
  "gateway": "192.168.1.1",
  "dns1": "8.8.8.8",
  "dns2": "8.8.4.4",
  "dhcp_enabled": true,
  "dhcp_start": "192.168.10.100",
  "dhcp_end": 101,
  "dhcp_end_ip": "192.168.10.200",
  "dhcp_reservations": [
    { "mac": "AA:BB:CC:DD:EE:FF", "ip": "192.168.10.50" }
  ],
  "mac_filter_mode": "block-listed",
  "mac_filter_list": [
    { "mac": "BA:D0:BA:D0:BA:D0", "type": "block" }
  ]
}
```

**Reading this example:**
- `BA:D0:BA:D0:BA:D0` has `type: "block"` — firmware rejects it at the 802.11 layer
- `AA:BB:CC:DD:EE:FF` is not in the list, so it associates normally, goes through the captive portal, and receives `192.168.10.50` from DHCP
- Captive portal pre-approval (if any) is managed by the portal/RADIUS server — firmware has no role in that

Example — password network with a blocked device:

```json
{
  "id": 2,
  "ssid": "OfficeWPA",
  "type": "password",
  "enabled": true,
  "ip_mode": "static",
  "ip_address": "10.0.0.1",
  "netmask": "255.255.255.0",
  "dhcp_enabled": true,
  "dhcp_start": "10.0.0.100",
  "dhcp_end": 50,
  "dhcp_end_ip": "10.0.0.149",
  "dhcp_reservations": [],
  "mac_filter_mode": "block-listed",
  "mac_filter_list": [
    { "mac": "DE:AD:BE:EF:00:01", "type": "block" }
  ]
}
```

**Reading this example:**
- `DE:AD:BE:EF:00:01` has `type: "block"` — rejected at the 802.11 layer even if it knows the WPA2 password
- All other devices authenticate via PSK as normal
