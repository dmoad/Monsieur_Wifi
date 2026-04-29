<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RadiusGuestSessionStatsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username'          => ['required', 'string', 'max:255'],
            'acct_session_id'   => ['required', 'string', 'max:128'],
            'acct_status_type'  => ['required'],
            'acct_input_octets'  => ['sometimes', 'nullable', 'integer', 'min:0'],
            'acct_output_octets' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'acct_session_time' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'acct_stop_time'     => ['sometimes', 'nullable'],
            'acct_start_time'    => ['sometimes', 'nullable'],
            'nas_id'             => ['sometimes', 'nullable', 'string', 'max:64'],
            'location_id'        => ['sometimes', 'nullable', 'integer', 'exists:locations,id'],
            'network_id'         => ['sometimes', 'nullable', 'integer', 'exists:location_networks,id'],
        ];
    }
}
