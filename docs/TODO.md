# Monsieur WiFi — Test Report Todo List

Source: `[Test_Report_Monsieur_WiFi.pdf](Test_Report_Monsieur_WiFi.pdf)`  
Firmware: V-1.036 · URL tested: [https://dev.monsieur-wifi.com](https://dev.monsieur-wifi.com) · Role: Super Admin

Severity: **Critical** · **Major** · **Minor**

---

## Email Alerts

- [x] **#1 Minor** — Cannot add multiple recipient email addresses *(multi-email support implemented)*
- [ ] **#2 Major** — No alerts for AP rebooting frequently, high CPU, high memory, or hardware faults
- [x] **#2 Major** — AP back online alert *(implemented)*

---

## SSID & Captive Portal

- [ ] **#3 Critical** — SSID set to Hidden remains visible and connectable
- [ ] **#4 Major** — Click-through: client not redirected to the captive portal
- [x] **#5 Major** — Social Google: login not working
- [ ] **#6 Major** — Social Facebook: login not working

---

## Client Analytics

- [ ] **#7 Major** — Client Status always shown as Active, even when disconnected
- [ ] **#8 Minor** — 802.11 standard, SSID name, roaming behavior, RSSI/SNR/retries/errors not shown

---

## Traffic & Usage Analytics

- [ ] **#9 Critical** — Location-level traffic data does not reflect real usage
- [ ] **#10 Major** — No per-SSID traffic metrics
- [ ] **#11 Major** — No per-user or per-device bandwidth consumption
- [ ] **#12 Major** — No RADIUS authentication method available or documented
- [ ] **#13 Minor** — No auth success/failure rate metrics per SSID, user, or AP

---

## Location Metrics Widgets

- [ ] **#14 Minor** — Bandwidth widget: displayed time period is unclear
- [ ] **#15 Critical** — No usage data shown despite real activity
- [ ] **#16 Minor** — Connected Device Types widget: device count not displayed
- [ ] **#17 Critical** — Analytics widget: users and sessions count incorrect
- [ ] **#18 Critical** — Live Users widget: new clients not reflected in real time
- [ ] **#19 Major** — Analytics widget (bottom of page): Windows clients not detected

---

## SSID Creation & Other

- [ ] **#20 Critical** — Error 500 when DNS fields are left empty, with no meaningful error message
- [ ] — Missed logs (IP / websites) for all SSIDs *(mentioned in report; no detail)*

---

## Confirmed working (reference)

- Offline email alert after 15+ minutes
- WPA2-PSK, WPA/WPA2-PSK Mixed, WPA3-PSK
- Captive portal: click-through, password, SMS, email, email + TOTP; speed limits
- Client log widget fields + CSV export
- Zone and global traffic totals
- Dynamic channel selection; roaming (~-70 dBm handoff)

