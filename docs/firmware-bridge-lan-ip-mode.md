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
  "dhcp_end_ip": "192.168.x.200"
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
      run DHCP server over pool [dhcp_start .. dhcp_end_ip]

  "bridge":
    bridge interface to WAN port
    no IP stack / no DHCP (unchanged behaviour)

  "dhcp"  ← retired, treat as bridge_lan + dhcp_client
```

---

## API routes

| Route | Payload key |
|---|---|
| `GET /api/devices/{key}/{secret}/v2-settings` | `response.networks[*]` |
| `GET /api/devices/{key}/{secret}/settings` | `response.networks[*]` |

Both routes serialise `LocationNetwork` model rows via `toArray()`.
`bridge_lan_dhcp_mode` is included automatically in every network object — no special mapping needed on the API side.
