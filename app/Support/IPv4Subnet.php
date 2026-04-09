<?php

namespace App\Support;

/**
 * IPv4 utilities for LAN / netmask and DHCP pool bounds.
 */
final class IPv4Subnet
{
    public static function ipv4ToUint32(string $ip): ?int
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return null;
        }
        $long = ip2long($ip);
        if ($long === false) {
            return null;
        }

        return $long < 0 ? $long + 0x100000000 : $long;
    }

    /**
     * @return array{0: int, 1: int}|null first host, last host (inclusive), or null if unusable
     */
    public static function usableHostBounds(?string $ipAddress, ?string $netmask): ?array
    {
        if ($ipAddress === null || $ipAddress === '' || $netmask === null || $netmask === '') {
            return null;
        }
        $ip = self::ipv4ToUint32($ipAddress);
        $mask = self::ipv4ToUint32($netmask);
        if ($ip === null || $mask === null) {
            return null;
        }

        $network = $ip & $mask;
        $broadcast = ($network | (~$mask & 0xFFFFFFFF)) & 0xFFFFFFFF;
        $wildcard = (~$mask) & 0xFFFFFFFF;
        $hostBits = 0;
        for ($i = 0; $i < 32; $i++) {
            $hostBits += ($wildcard >> $i) & 1;
        }
        if ($hostBits < 2) {
            return null;
        }

        $first = ($network + 1) & 0xFFFFFFFF;
        $last = ($broadcast - 1) & 0xFFFFFFFF;

        return [$first, $last];
    }

    /**
     * @return array{valid: bool, message?: string}
     */
    public static function validateDhcpPool(
        ?string $ipAddress,
        ?string $netmask,
        ?string $dhcpStart,
        ?int $poolSize,
    ): array {
        if ($poolSize === null || $poolSize < 1) {
            return ['valid' => false, 'message' => 'DHCP pool size must be at least 1.'];
        }
        if ($dhcpStart === null || $dhcpStart === '') {
            return ['valid' => false, 'message' => 'DHCP start address is required when DHCP is enabled.'];
        }
        $bounds = self::usableHostBounds($ipAddress, $netmask);
        if ($bounds === null) {
            return ['valid' => false, 'message' => 'Invalid IP address or netmask for subnet.'];
        }
        [$first, $last] = $bounds;
        $startU = self::ipv4ToUint32($dhcpStart);
        if ($startU === null) {
            return ['valid' => false, 'message' => 'DHCP start must be a valid IPv4 address.'];
        }
        if ($startU < $first || $startU > $last) {
            return ['valid' => false, 'message' => 'DHCP start must be within the LAN subnet.'];
        }
        $lastPool = ($startU + $poolSize - 1) & 0xFFFFFFFF;
        if ($lastPool > $last) {
            return ['valid' => false, 'message' => 'DHCP pool extends beyond the subnet.'];
        }

        return ['valid' => true];
    }
}
