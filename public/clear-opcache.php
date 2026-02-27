<?php
// Clear OPcache for web server
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared successfully!\n";
    echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
    echo "Server: " . $_SERVER['SERVER_NAME'] . "\n";
} else {
    echo "❌ OPcache not available\n";
}

// Also clear realpath cache
clearstatcache(true);
echo "✅ Realpath cache cleared\n";

echo "\n⚠️  IMPORTANT: Delete this file immediately after use!\n";
echo "Run: rm " . __FILE__ . "\n";
