# RADIUS guest session stats API

Callback used to push accounting data from your RADIUS stack into the portal’s `user_device_login_sessions` rows.

## Endpoint

| | |
|---|---|
| **URL** | `POST {APP_URL}/api/radius/guest-session-stats` |
| **Content-Type** | `application/json` |
| **Auth** | Same secret as `RADIUS_STATS_SECRET` in Laravel `.env` |

### Authentication

Choose one header (recommended: Bearer):

```http
Authorization: Bearer <RADIUS_STATS_SECRET>
```

Alternative:

```http
X-Api-Token: <RADIUS_STATS_SECRET>
```

If the secret is empty or wrong, the API returns **401 Unauthorized**.

### Body (JSON)

All requests require:

| Field | Type | Notes |
|---|---|---|
| `username` | string | Usually the Calling-Station-ID / MAC (`aa:bb:cc:…`, `AA-BB-CC-…`, or 12 hex digits). |
| `acct_session_id` | string | Same value as FreeRADIUS `Acct-Session-Id` (`RadAcct.AcctSessionId`). Used to find or link the portal session row. |
| `acct_status_type` | number or string | **Numeric (RFC-aligned):** `1` Start, `2` Stop, `3` Interim-Update. **Strings:** e.g. `Accounting-Start`, `Accounting-Stop`, strings containing `interim`. |

Optional (send when helpful for first match or NAS context):

| Field | Type | Notes |
|---|---|---|
| `location_id` | integer | Must exist in `locations` if sent. Helps match an **open** row when no row is linked yet. |
| `network_id` | integer | Must exist in `location_networks` if sent. |
| `acct_input_octets` | integer ≥ 0 | Upload (toward subscriber / from NAS perspective as you define it for your accounting). Stored as **`total_upload`**. |
| `acct_output_octets` | integer ≥ 0 | Download. Stored as **`total_download`**. |
| `acct_session_time` | integer ≥ 0 | Seconds. Stored as **`session_duration`**. |
| `acct_stop_time` | mixed | On stop, used to set **`disconnect_time`** (Unix timestamp or parseable datetime). |
| `acct_start_time` | mixed | Available for validation; primary stop time for disconnect is `acct_stop_time`. |

## Successful response (200)

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 123,
    "radius_session_id": "ACCT-SESSION-ID-STRING",
    "total_download": 12000,
    "total_upload": 2000,
    "session_duration": 3600,
    "disconnect_time": "2026-04-29T12:34:56+00:00"
  }
}
```

`disconnect_time` is ISO 8601 when the session has been stopped; otherwise it may be `null`.

Other responses:

| Code | Typical reason |
|---|---|
| **404** | No matching `UserDeviceLoginSession` (wrong MAC/session, or row already closed—see logs). |
| **422** | Unknown or unsupported `acct_status_type`. |

## cURL examples

Set `BASE` and `SECRET` to your portal base URL and `RADIUS_STATS_SECRET`.

### Accounting-Start (link session by `Acct-Session-Id` + portal context)

```bash
BASE="https://portal.example.com"
SECRET="your-RADIUS_STATS_SECRET"

curl -sS -X POST "${BASE}/api/radius/guest-session-stats" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${SECRET}" \
  -d '{
    "username": "aa:bb:cc:dd:ee:ff",
    "acct_session_id": "7F2A...",
    "acct_status_type": 1,
    "location_id": 10,
    "network_id": 20
  }'
```

### Interim-Update

```bash
curl -sS -X POST "${BASE}/api/radius/guest-session-stats" \
  -H "Content-Type: application/json" \
  -H "X-Api-Token: '"${SECRET}"'" \
  -d '{
    "username": "aabbccddeeff",
    "acct_session_id": "7F2A...",
    "acct_status_type": 3,
    "acct_output_octets": 5000000,
    "acct_input_octets": 500000,
    "acct_session_time": 600
  }'
```

### Accounting-Stop

```bash
curl -sS -X POST "${BASE}/api/radius/guest-session-stats" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${SECRET}" \
  -d '{
    "username": "aa-bb-cc-dd-ee-ff",
    "acct_session_id": "7F2A...",
    "acct_status_type": "Accounting-Stop",
    "acct_output_octets": 12000000,
    "acct_input_octets": 2000000,
    "acct_session_time": 3600,
    "acct_stop_time": 1714400000
  }'
```

## Integration notes

1. **Secret:** Generate a long random string; set `RADIUS_STATS_SECRET` on the server and configure the same value on the component that calls this API (FreeRADIUS `rest` module, systemd unit, or post-auth script).
2. **Matching:** The service first looks up by `radius_session_id` (after you’ve linked it on Start). Otherwise it matches an **open** session by MAC variants + optional `location_id` / `network_id`.
3. **Rate / errors:** On **404**, ensure the guest actually has a portal login session row and that MAC / session id match what the captive portal stored.
4. **Tests:** Feature tests live under `tests/Feature/RadiusGuestSessionStats*Test.php` and do **not** run `migrate:fresh` (they use transactions only).
