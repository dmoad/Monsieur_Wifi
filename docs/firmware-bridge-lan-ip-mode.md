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

**`bridge_lan` + `dhcp_client` is not permitted for Captive Portal networks.**

The captive portal requires a routable IP on the interface to redirect unauthenticated clients. The UI enforces this — the `dhcp_client` sub-mode option is hidden/disabled when the network type is `captive_portal`, and any saved `dhcp_client` value is coerced to `dhcp_server` on load.

> **Firmware defensive check:** if a payload ever arrives with `type == "captive_portal"` and `bridge_lan_dhcp_mode == "dhcp_client"`, treat it as `dhcp_server` and log a warning. This combination should never reach the firmware but is worth guarding against.

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
| `static` | — | password / open | populated if `dhcp_enabled: true` |
| `bridge_lan` | `dhcp_server` | password / open | always populated (may be `[]`) |
| `bridge_lan` | `dhcp_client` | any | `null` |
| `bridge` | — | any | `null` |
| any | any | `captive_portal` | `null` |

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
- When no reservations exist the field is an **empty array** `[]`, not `null` (unless the network is not a DHCP server, in which case it is `null`)

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

## API routes

| Route | Payload key |
|---|---|
| `GET /api/devices/{key}/{secret}/v2-settings` | `response.networks[*]` |
| `GET /api/devices/{key}/{secret}/settings` | `response.networks[*]` |

Both routes serialise `LocationNetwork` model rows via `toArray()`.
`bridge_lan_dhcp_mode` and `dhcp_reservations` are included automatically in every network object — no special mapping needed on the API side.
