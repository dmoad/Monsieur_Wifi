<?php

namespace App\Services;

use App\Models\Radcheck;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RadiusService
{
    public function getUserRadcheckEntries(string $macAddress): Collection
    {
        return Radcheck::getByUsername($macAddress);
    }

    public function getUserNetworkRadcheckEntries(string $macAddress, int $networkId): Collection
    {
        return Radcheck::getByUsernameAndNetwork($macAddress, $networkId);
    }

    public function setUserAuthentication(
        string $macAddress,
        string $password,
        int $networkId,
        ?int $downloadBandwidth = null,
        ?int $uploadBandwidth = null,
        ?Carbon $expirationTime = null
    ): Radcheck {
        return Radcheck::updateOrCreateRecord(
            $macAddress,
            'Cleartext-Password',
            $password,
            '==',
            [
                'network_id'         => $networkId,
                'download_bandwidth' => $downloadBandwidth,
                'upload_bandwidth'   => $uploadBandwidth,
                'expiration_time'    => $expirationTime,
            ]
        );
    }

    public function setBandwidthLimits(
        string $macAddress,
        int $networkId,
        int $downloadBandwidth,
        int $uploadBandwidth
    ): void {
        Radcheck::updateOrCreateRecord(
            $macAddress,
            'WISPr-Bandwidth-Max-Down',
            $downloadBandwidth,
            '==',
            [
                'network_id'         => $networkId,
                'download_bandwidth' => $downloadBandwidth,
                'upload_bandwidth'   => $uploadBandwidth,
            ]
        );

        Radcheck::updateOrCreateRecord(
            $macAddress,
            'WISPr-Bandwidth-Max-Up',
            $uploadBandwidth,
            '==',
            [
                'network_id'         => $networkId,
                'download_bandwidth' => $downloadBandwidth,
                'upload_bandwidth'   => $uploadBandwidth,
            ]
        );
    }

    public function setExpirationTime(string $macAddress, int $networkId, Carbon $expirationTime): Radcheck
    {
        $sessionTimeout = $expirationTime->diffInSeconds(Carbon::now());

        return Radcheck::updateOrCreateRecord(
            $macAddress,
            'Session-Timeout',
            $sessionTimeout,
            '==',
            [
                'network_id'      => $networkId,
                'expiration_time' => $expirationTime,
            ]
        );
    }

    public function removeUserFromNetwork(string $macAddress, int $networkId): int
    {
        return Radcheck::deleteByUsernameAndNetwork($macAddress, $networkId);
    }

    public function removeUser(string $macAddress): int
    {
        return Radcheck::deleteByUsername($macAddress);
    }

    public function getUserAttribute(string $username, string $attribute): ?string
    {
        $record = Radcheck::where('username', $username)
            ->where('attribute', $attribute)
            ->first();

        return $record ? $record->value : null;
    }

    public function updateUserAttribute(string $username, string $attribute, string $value, string $op = '=='): Radcheck
    {
        return Radcheck::updateOrCreateRecord($username, $attribute, $value, $op);
    }
}
