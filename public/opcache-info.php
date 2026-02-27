<?php
echo "<h1>OPcache Status</h1>";

if (function_exists('opcache_get_status')) {
    $status = opcache_get_status(false);
    echo "<p><strong>Enabled:</strong> " . ($status['opcache_enabled'] ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Cache Full:</strong> " . ($status['cache_full'] ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Cached Scripts:</strong> " . $status['opcache_statistics']['num_cached_scripts'] . "</p>";
    echo "<p><strong>Hits:</strong> " . $status['opcache_statistics']['hits'] . "</p>";
    echo "<p><strong>Misses:</strong> " . $status['opcache_statistics']['misses'] . "</p>";
    
    // Check if our Order.php is cached
    $cached_scripts = opcache_get_status(true)['scripts'];
    $orderPhpPath = realpath('/var/www/mrwifi/app/Models/Order.php');
    
    if (isset($cached_scripts[$orderPhpPath])) {
        echo "<p style='color:red'><strong>⚠️ Order.php IS CACHED</strong></p>";
        echo "<p>Cached at: " . date('Y-m-d H:i:s', $cached_scripts[$orderPhpPath]['timestamp']) . "</p>";
        echo "<p>Last used: " . date('Y-m-d H:i:s', $cached_scripts[$orderPhpPath]['last_used_timestamp']) . "</p>";
    } else {
        echo "<p style='color:green'><strong>✅ Order.php NOT in cache</strong></p>";
    }
    
    echo "<hr>";
    echo "<h2>Clear OPcache?</h2>";
    echo "<form method='post'>";
    echo "<button type='submit' name='clear' value='1' style='padding:15px 30px; background:#ea5455; color:white; border:none; border-radius:8px; font-size:16px; cursor:pointer;'>Clear OPcache Now</button>";
    echo "</form>";
    
    if (isset($_POST['clear'])) {
        if (opcache_reset()) {
            echo "<p style='color:green; font-size:20px;'><strong>✅ OPcache CLEARED!</strong></p>";
            echo "<p>Timestamp: " . date('Y-m-d H:i:s') . "</p>";
            echo "<p><a href='opcache-info.php'>Refresh to see new status</a></p>";
        } else {
            echo "<p style='color:red;'><strong>❌ Failed to clear OPcache</strong></p>";
        }
    }
} else {
    echo "<p>OPcache not available</p>";
}

echo "<hr>";
echo "<p><strong>⚠️ Delete these files after use:</strong></p>";
echo "<pre>rm /var/www/mrwifi/public/opcache-info.php\nrm /var/www/mrwifi/public/clear-opcache.php</pre>";
?>
