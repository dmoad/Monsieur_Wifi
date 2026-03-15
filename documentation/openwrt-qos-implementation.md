# OpenWRT QoS Implementation Guide
## Multi-Network / Multi-SSID with SNI-Based DSCP Marking

---

## 1. Overview

This document describes how to implement QoS on an OpenWRT router with multiple networks and SSIDs using:

- **SNI inspection** — classify HTTPS traffic by hostname without decryption
- **DSCP marking** — tag packets with priority class
- **CAKE / SQM** — enforce prioritization at the WAN uplink

No IP-based mapping is used. Rules are maintainable, stable, and CDN-agnostic.

---

## 2. Network Topology

```
SSID: Home       (br-home,  192.168.1.0/24)  ─┐
SSID: Guest      (br-guest, 192.168.2.0/24)  ──┼── Router ── WAN ── Internet
SSID: IoT        (br-iot,   192.168.3.0/24)  ─┘

Traffic Flow:
  LAN devices → [prerouting: SNI inspect + mark] → [postrouting: DSCP apply] → WAN
```

---

## 3. DSCP Classes Used

| Class  | Value | Priority     | Use Case                        |
|--------|-------|--------------|---------------------------------|
| EF     | 46    | Highest      | VoIP, video calls, gaming       |
| AF41   | 34    | High         | Video streaming                 |
| BE     | 0     | Normal       | General browsing (default)      |
| CS1    | 8     | Scavenger    | Backups, torrents, IoT, Guest   |

Under **CAKE diffserv4**, these map to tins automatically:
- EF → Voice tin
- AF41 → Video tin
- BE → Best Effort tin
- CS1 → Bulk tin

---

## 4. Per-Network Policy

| Network | SNI Inspect | Trust Client DSCP | Default Class | Rationale                    |
|---------|-------------|-------------------|---------------|------------------------------|
| Home    | ✅ Yes      | ✅ Yes            | BE            | Full QoS, trusted devices    |
| Guest   | ❌ No       | ❌ No             | CS1           | Deprioritize, no trust       |
| IoT     | ❌ No       | ❌ No             | CS1           | Scavenger, no inspection needed |

---

## 5. nftables Implementation

Save as `/etc/nftables.d/qos-sni.nft`

```nft
#!/usr/sbin/nft -f

table inet qos_sni {

    ##
    ## PREROUTING — inspect and mark incoming LAN traffic
    ##
    chain prerouting {
        type filter hook prerouting priority mangle; policy accept;

        # Route each network to its policy chain
        iifname "br-home"  jump policy_home
        iifname "br-guest" jump policy_guest
        iifname "br-iot"   jump policy_iot

        # Restore conntrack mark on established flows (all networks)
        ct state established,related ct mark != 0 meta mark set ct mark
    }

    ##
    ## HOME — full QoS with SNI inspection
    ##
    chain policy_home {
        # Inspect new TLS connections
        tcp dport 443 ct state new jump sni_inspect

        # Trust DSCP marks set by home devices (phones, consoles, PCs)
        ct mark != 0 meta mark set ct mark
    }

    ##
    ## GUEST — blanket deprioritize, no inspection
    ##
    chain policy_guest {
        # Mark all guest traffic as scavenger
        ct mark set 0x08
        meta mark set 0x08
    }

    ##
    ## IOT — blanket deprioritize, no inspection
    ##
    chain policy_iot {
        # Mark all IoT traffic as scavenger
        ct mark set 0x08
        meta mark set 0x08
    }

    ##
    ## SNI INSPECTION — match hostname and assign DSCP mark
    ##
    chain sni_inspect {

        # --- Real-time: Video Calls → EF (46 / 0x2e) ---
        tls sni "meet.google.com"              ct mark set 0x2e comment "Google Meet"
        tls sni "*.zoom.us"                    ct mark set 0x2e comment "Zoom"
        tls sni "*.teams.microsoft.com"        ct mark set 0x2e comment "MS Teams"
        tls sni "*.webex.com"                  ct mark set 0x2e comment "Webex"
        tls sni "*.discord.com"                ct mark set 0x2e comment "Discord voice"

        # --- Streaming → AF41 (34 / 0x22) ---
        tls sni "*.netflix.com"                ct mark set 0x22 comment "Netflix"
        tls sni "*.nflxvideo.net"              ct mark set 0x22 comment "Netflix CDN"
        tls sni "*.youtube.com"                ct mark set 0x22 comment "YouTube"
        tls sni "*.googlevideo.com"            ct mark set 0x22 comment "YouTube CDN"
        tls sni "*.primevideo.com"             ct mark set 0x22 comment "Prime Video"
        tls sni "*.hotstar.com"                ct mark set 0x22 comment "Hotstar"

        # --- Bulk / Background → CS1 (8 / 0x08) ---
        tls sni "*.dropbox.com"                ct mark set 0x08 comment "Dropbox"
        tls sni "*.onedrive.com"               ct mark set 0x08 comment "OneDrive"
        tls sni "*.drive.google.com"           ct mark set 0x08 comment "Google Drive"
        tls sni "*.amazonaws.com"              ct mark set 0x08 comment "AWS S3 / backups"

        # Unmatched flows get no mark — BE (default) applies
    }

    ##
    ## POSTROUTING — apply DSCP to packets leaving via WAN
    ##
    chain postrouting {
        type filter hook postrouting priority mangle; policy accept;

        # Single WAN egress point — all networks funnel here
        oifname "wan" ct mark != 0 ip dscp set ct mark

        # Also handle IPv6 if needed
        # oifname "wan" ct mark != 0 ip6 dscp set ct mark
    }
}
```

> **Note:** Replace `"wan"` with your actual WAN interface name.
> Check with: `ip link show` — common names are `eth1`, `pppoe-wan`, `wan`.

---

## 6. QUIC / HTTP3 Heuristic (UDP 443)

SNI inspection only works on TCP 443. For QUIC (UDP 443), add a heuristic rule:

```nft
chain prerouting {
    # ... existing rules ...

    # QUIC traffic from home network — treat as streaming priority
    iifname "br-home" udp dport 443 ct state new ct mark set 0x22
    iifname "br-home" udp dport 443 ct state established meta mark set ct mark
}
```

---

## 7. SQM / CAKE Configuration

Edit `/etc/config/sqm`:

```
config queue 'wan_qos'
    option enabled      '1'
    option interface    'wan'
    option download     '100000'    # Your actual download speed in kbps
    option upload       '20000'     # Your actual upload speed in kbps
    option qdisc        'cake'
    option qdisc_opts   'diffserv4' # Honour EF / AF41 / BE / CS1 tins
    option script       'piece_of_cake.qos'
```

Install required packages if not present:

```bash
opkg update
opkg install sqm-scripts luci-app-sqm kmod-sched-cake
```

---

## 8. Loading and Persisting the Rules

### Load immediately
```bash
nft -f /etc/nftables.d/qos-sni.nft
```

### Verify rules loaded
```bash
nft list table inet qos_sni
```

### Auto-load on boot
Add to `/etc/rc.local`:
```bash
nft -f /etc/nftables.d/qos-sni.nft
```

Or include in `/etc/nftables.conf`:
```bash
include "/etc/nftables.d/qos-sni.nft"
```

---

## 9. Debugging & Verification

### Check conntrack marks on active flows
```bash
conntrack -L | grep mark
```

### Monitor CAKE queue stats per tin
```bash
tc -s qdisc show dev wan
```

### Watch packet marks in real time
```bash
nft monitor trace
```

### Test SNI matching (check a flow is being marked)
```bash
# From a LAN device, start a connection to zoom
# Then on router:
conntrack -L | grep 443 | grep mark
```

---

## 10. Limitations & Future Considerations

| Limitation | Impact | Mitigation |
|---|---|---|
| ECH (Encrypted Client Hello) | SNI will be hidden | Fall back to port/protocol heuristics |
| SNI only on first packet | Must mark conntrack, not per-packet | Already handled via `ct mark` |
| QUIC / HTTP3 (UDP 443) | Not caught by TCP SNI rules | Add separate UDP heuristic rules |
| Download shaping | Congestion happens upstream | CAKE ingress mirroring partially helps |
| Wildcard depth | `*.x.com` works, `*.*.x.com` does not | List specific subdomains if needed |

---

## 11. Adding Custom SNI Rules (User-Facing)

To add a new service rule without editing nftables directly, use a UCI-style config
and a script that regenerates the nftables chain. Example user config:

```
config sni_rule
    option name     'My Service'
    option sni      '*.myservice.com'
    option dscp     'ef'
    option network  'home'
    option enabled  '1'
```

A generation script reads this config and rebuilds the `sni_inspect` chain dynamically,
making it suitable for a LuCI UI frontend.

---

*Document version: 1.0 — OpenWRT 22.03+ / nftables / CAKE*
