<?php

/**
 * FrankenPHP server test
 * Basic PHP info page for testing server functionality
 */

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "    <title>FrankenPHP Server Test</title>\n";
echo "    <style>\n";
echo "        body { font-family: sans-serif; margin: 40px; }\n";
echo "        h1 { color: #333; }\n";
echo "        .success { color: green; }\n";
echo "        .info { background: #f0f0f0; padding: 20px; border-radius: 5px; }\n";
echo "        table { border-collapse: collapse; margin: 20px 0; }\n";
echo "        td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }\n";
echo "        th { background: #f0f0f0; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <h1 class='success'>✓ FrankenPHP Server is Running</h1>\n";
echo "    \n";
echo "    <div class='info'>\n";
echo "        <h2>Server Information</h2>\n";
echo "        <table>\n";
echo "            <tr><th>Property</th><th>Value</th></tr>\n";
echo "            <tr><td>PHP Version</td><td>" . PHP_VERSION . "</td></tr>\n";
echo "            <tr><td>Server Software</td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</td></tr>\n";
echo "            <tr><td>Server Name</td><td>" . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "</td></tr>\n";
echo "            <tr><td>Server Port</td><td>" . ($_SERVER['SERVER_PORT'] ?? 'N/A') . "</td></tr>\n";
echo "            <tr><td>Request Method</td><td>" . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "</td></tr>\n";
echo "            <tr><td>Request URI</td><td>" . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</td></tr>\n";
echo "            <tr><td>Extensions Loaded</td><td>" . count(get_loaded_extensions()) . "</td></tr>\n";
echo "        </table>\n";
echo "    </div>\n";
echo "    \n";
echo "    <div class='info'>\n";
echo "        <h2>Key Extensions</h2>\n";
echo "        <table>\n";
echo "            <tr><th>Extension</th><th>Status</th><th>Version</th></tr>\n";

$key_extensions = ['mongodb', 'ffi', 'spx', 'curl', 'openssl', 'pdo', 'redis', 'gd', 'opcache'];
foreach ($key_extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? '✓ Loaded' : '✗ Not loaded';
    $version = $loaded ? phpversion($ext) : 'N/A';
    echo "            <tr><td>{$ext}</td><td>{$status}</td><td>{$version}</td></tr>\n";
}

echo "        </table>\n";
echo "    </div>\n";
echo "    \n";
echo "    <p><a href='?phpinfo=1'>View Full PHP Info</a></p>\n";

if (isset($_GET['phpinfo'])) {
    echo "    <hr>\n";
    phpinfo();
}

echo "</body>\n";
echo "</html>\n";
