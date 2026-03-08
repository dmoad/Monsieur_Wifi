<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpVerification extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'location_id',
        'network_id',
        'mac_address',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Generate a new OTP for the given phone number, scoped to a network.
     */
    public static function generateOtp(string $phone, int $networkId, string $macAddress = null): self
    {
        // Invalidate any existing unverified OTPs for this phone/network combo
        self::where('phone', $phone)
            ->where('network_id', $networkId)
            ->whereNull('verified_at')
            ->update(['expires_at' => now()]);

        $otp = (string) random_int(1000, 9999);

        return self::create([
            'phone'      => $phone,
            'otp'        => $otp,
            'network_id' => $networkId,
            'mac_address' => $macAddress,
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    /**
     * Verify an OTP for the given phone number and network.
     */
    public static function verifyOtp(string $phone, string $otp, int $networkId): bool
    {
        $otpRecord = self::where('phone', $phone)
            ->where('otp', $otp)
            ->where('network_id', $networkId)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return false;
        }

        $otpRecord->verified_at = now();
        $otpRecord->save();

        return true;
    }

    /**
     * Get the most recent verified OTP for a phone and network.
     */
    public static function getVerifiedOtp(string $phone, int $networkId): ?self
    {
        return self::where('phone', $phone)
            ->where('network_id', $networkId)
            ->whereNotNull('verified_at')
            ->where('verified_at', '>', now()->subMinutes(30))
            ->latest('verified_at')
            ->first();
    }
}
