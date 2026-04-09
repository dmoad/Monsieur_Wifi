<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * dhcp_end: was end IPv4 string; now unsigned pool size (address count).
     */
    public function up(): void
    {
        $rows = DB::table('location_networks')->select('id', 'dhcp_start', 'dhcp_end')->get();
        $pools = [];
        foreach ($rows as $row) {
            $pools[$row->id] = $this->derivePoolSize($row->dhcp_start, $row->dhcp_end);
        }

        Schema::table('location_networks', function (Blueprint $table) {
            $table->dropColumn('dhcp_end');
        });

        Schema::table('location_networks', function (Blueprint $table) {
            $table->unsignedInteger('dhcp_end')->nullable()->after('dhcp_start');
        });

        foreach ($pools as $id => $pool) {
            DB::table('location_networks')->where('id', $id)->update(['dhcp_end' => $pool]);
        }
    }

    public function down(): void
    {
        $rows = DB::table('location_networks')->select('id', 'dhcp_start', 'dhcp_end')->get();
        $endIps = [];
        foreach ($rows as $row) {
            $endIps[$row->id] = $this->poolToEndIp($row->dhcp_start, $row->dhcp_end);
        }

        Schema::table('location_networks', function (Blueprint $table) {
            $table->dropColumn('dhcp_end');
        });

        Schema::table('location_networks', function (Blueprint $table) {
            $table->string('dhcp_end')->nullable()->after('dhcp_start');
        });

        foreach ($endIps as $id => $endIp) {
            DB::table('location_networks')->where('id', $id)->update(['dhcp_end' => $endIp]);
        }
    }

    private function derivePoolSize(?string $dhcpStart, ?string $dhcpEnd): int
    {
        if ($dhcpStart && $dhcpEnd) {
            $start = filter_var($dhcpStart, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            $end = filter_var($dhcpEnd, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            if ($start && $end) {
                $s = ip2long($start);
                $e = ip2long($end);
                if ($s !== false && $e !== false) {
                    $su = $s < 0 ? $s + 0x100000000 : $s;
                    $eu = $e < 0 ? $e + 0x100000000 : $e;
                    if ($eu >= $su) {
                        return (int) ($eu - $su + 1);
                    }
                }
            }
            if (is_numeric($dhcpEnd)) {
                $n = (int) $dhcpEnd;
                if ($n > 0) {
                    return $n;
                }
            }
        }

        return 101;
    }

    private function poolToEndIp(?string $dhcpStart, $poolSize): ?string
    {
        if (empty($dhcpStart) || $poolSize === null || (int) $poolSize < 1) {
            return null;
        }
        $start = ip2long($dhcpStart);
        if ($start === false) {
            return null;
        }
        $su = $start < 0 ? $start + 0x100000000 : $start;
        $last = $su + (int) $poolSize - 1;
        if ($last > 0xFFFFFFFF) {
            return null;
        }
        $packed = $last > 0x7FFFFFFF ? $last - 0x100000000 : $last;

        return long2ip($packed);
    }
};
