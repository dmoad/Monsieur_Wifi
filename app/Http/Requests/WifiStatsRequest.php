<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WifiStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Top-level fields ────────────────────────────────────────────
            'ap_id'            => ['required', 'string'],
            // Colons (often lowercase from older AP builds) or hyphens / upper hex e.g. AA-BB-CC-11-22-33
            'ap_mac'           => ['required', 'string', 'regex:/^(?:[0-9a-fA-F]{2}(?::[0-9a-fA-F]{2}){5}|[0-9a-fA-F]{2}(?:-[0-9a-fA-F]{2}){5})$/'],
            'config_version'   => ['required', 'string'],
            'firmware_version' => ['required', 'string'],
            'ts'               => ['required', 'string'],

            // ── Physical radios (top-level array, 1–2 entries) ───────────────
            'radios'               => ['required', 'array'],
            'radios.*.radio'       => ['required', 'string', 'in:radio0,radio1'],
            'radios.*.band'        => ['required', 'string', 'in:2g,5g'],
            'radios.*.channel'     => ['required', 'integer'],
            'radios.*.htmode'      => ['required', 'string'],
            'radios.*.txpower_dbm' => ['required', 'integer'],
            'radios.*.noise_dbm'   => ['nullable', 'integer'],

            // ── Slots (present, not required — empty array is valid) ─────────
            'slots'          => ['present', 'array'],
            'slots.*.slot'   => ['required', 'integer', 'between:0,7'],
            'slots.*.ssid'   => ['required', 'string'],
            'slots.*.network' => ['required', 'string'],
            // Per-slot WLAN mode from AP e.g. password | captive_portal (optional for older firmware)
            'slots.*.network_type' => ['sometimes', 'nullable', 'string', 'max:64'],
            // nasid can be "" (seen in real payloads) — nullable covers empty→null conversion
            'slots.*.nasid'  => ['present', 'nullable', 'string'],

            // ── Per-slot radios ──────────────────────────────────────────────
            'slots.*.radios'              => ['present', 'array'],
            'slots.*.radios.*.radio'      => ['required', 'string', 'in:radio0,radio1'],
            'slots.*.radios.*.iface'      => ['required', 'string'],
            'slots.*.radios.*.band'       => ['required', 'string', 'in:2g,5g'],
            'slots.*.radios.*.client_count' => ['required', 'integer', 'min:0'],

            // clients can be [] — use present, not required
            'slots.*.radios.*.clients' => ['present', 'array'],

            // ── Per-client fields ────────────────────────────────────────────
            'slots.*.radios.*.clients.*.mac'              => ['required', 'string', 'regex:/^(?:[0-9a-fA-F]{2}(?::[0-9a-fA-F]{2}){5}|[0-9a-fA-F]{2}(?:-[0-9a-fA-F]{2}){5})$/'],
            'slots.*.radios.*.clients.*.ssid'             => ['required', 'string'],
            'slots.*.radios.*.clients.*.ip'               => ['nullable', 'string'],
            'slots.*.radios.*.clients.*.signal_dbm'       => ['required', 'integer'],
            'slots.*.radios.*.clients.*.signal_avg_dbm'   => ['required', 'integer'],
            'slots.*.radios.*.clients.*.snr_db'           => ['nullable', 'integer'],
            'slots.*.radios.*.clients.*.tx_retries'       => ['required', 'integer', 'min:0'],
            'slots.*.radios.*.clients.*.tx_failed'        => ['required', 'integer', 'min:0'],
            'slots.*.radios.*.clients.*.connected_time_s' => ['required', 'integer', 'min:0'],
            'slots.*.radios.*.clients.*.inactive_time_ms' => ['required', 'integer', 'min:0'],
        ];
    }
}
