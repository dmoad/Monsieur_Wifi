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

SSID is hidden just that when you have connected already, the essid will show up in your phone/laptops etc list even if its hidden, as it reads from memory and then checks essids beacons to see if its available or not. The way to test would be to set an essid to hidden, forget same essid from your phone/laptop and then see essid list.

Click-though works.

Google and Facebook login has been enabled.

---

## Client Analytics

- [ ] **#7 Major** — Client Status always shown as Active, even when disconnected
- [ ] **#8 Minor** — 802.11 standard, SSID name, roaming behavior, RSSI/SNR/retries/errors not shown

Updated. 

---

## Traffic & Usage Analytics

- [ ] **#9 Critical** — Location-level traffic data does not reflect real usage
- [ ] **#10 Major** — No per-SSID traffic metrics
- [ ] **#11 Major** — No per-user or per-device bandwidth consumption
- [ ] **#12 Major** — No RADIUS authentication method available or documented
- [ ] **#13 Minor** — No auth success/failure rate metrics per SSID, user, or AP

Location-level traffic data definetly reflect the real data. Please keep in mind we only track and show Captive portal network stats. Networks of type Open and PSK are not managed through RADIUS and are not monitored.
We use PAP based authentication for Captive portal users.
Per essid stats has been added. You will see per essid stats and combined stats on analytics page.
Per user/device bandwidth is controlled in Captive portal but is not monitored. We do track data usage (Download and Upload) per session, which is shown in Guest Sessions.
No of Auth Success and Failure has been added.

---

## Location Metrics Widgets

- [ ] **#14 Minor** — Bandwidth widget: displayed time period is unclear
- [ ] **#15 Critical** — No usage data shown despite real activity
That is unfair assesment. We track captive portal usage only, which does reflect correctly.

- [ ] **#16 Minor** — Connected Device Types widget: device count not displayed
Fixed.
- [ ] **#17 Critical** — Analytics widget: users and sessions count incorrect
Again, you are probably mixing Captive portal and other network stats. We only track captive portal user session info.

- [ ] **#18 Critical** — Live Users widget: new clients not reflected in real time
Again, you are probably mixing Captive portal and other network stats. We only track captive portal user session info.

- [ ] **#19 Major** — Analytics widget (bottom of page): Windows clients not detected.
To Check.

---

## SSID Creation & Other

- [ ] **#20 Critical** — Error 500 when DNS fields are left empty, with no meaningful error message
- [ ] — Missed logs (IP / websites) for all SSIDs *(mentioned in report; no detail)*
Again we track IP for clients connected over Captive portal only.

---

## Confirmed working (reference)

- Offline email alert after 15+ minutes
- WPA2-PSK, WPA/WPA2-PSK Mixed, WPA3-PSK
- Captive portal: click-through, password, SMS, email, email + TOTP; speed limits
- Client log widget fields + CSV export
- Zone and global traffic totals
- Dynamic channel selection; roaming (~-70 dBm handoff)

