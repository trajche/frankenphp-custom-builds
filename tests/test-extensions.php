<?php

/**
 * Extension validation test
 * Tests that all required extensions are loaded
 */

echo "====================================\n";
echo "Testing PHP Extensions\n";
echo "====================================\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Extensions loaded: " . count(get_loaded_extensions()) . "\n";
echo "\n";

// Required extensions (common to all PHP versions)
$required = [
    'mongodb',
    'FFI',
    'SPX',
    'curl',
    'openssl',
    'PDO',
    'pdo_mysql',
    'pdo_pgsql',
    'pdo_sqlite',
    'mysqli',
    'redis',
    'gd',
    'mbstring',
    'zip',
    'intl',
    'bcmath',
    'gmp',
    'opcache',
    'apcu',
    'imagick',
    'soap',
    'xml',
    'dom',
    'simplexml',
    'xmlreader',
    'xmlwriter',
    'xsl',
    'pcntl',
    'posix',
    'sockets',
    'fileinfo',
    'filter',
    'tokenizer',
    'calendar',
    'sodium',
    'zlib',
    'bz2',
    'exif',
    'ctype',
    'iconv',
    'session',
    'phar'
];

// Add IMAP for PHP 8.3 only
if (PHP_VERSION_ID >= 80300 && PHP_VERSION_ID < 80400) {
    $required[] = 'imap';
}

$loaded = array_map('strtolower', get_loaded_extensions());
$missing = [];
$found = [];

foreach ($required as $ext) {
    $ext_lower = strtolower($ext);
    $is_loaded = in_array($ext_lower, $loaded);

    // Special case: opcache might be named "zend opcache"
    if (!$is_loaded && $ext_lower === 'opcache') {
        $is_loaded = in_array('zend opcache', $loaded);
    }

    if (!$is_loaded) {
        $missing[] = $ext;
        echo "✗ $ext (MISSING)\n";
    } else {
        $found[] = $ext;
        echo "✓ $ext\n";
    }
}

echo "\n";
echo "====================================\n";
echo "Summary\n";
echo "====================================\n";
echo "Required: " . count($required) . "\n";
echo "Found: " . count($found) . "\n";
echo "Missing: " . count($missing) . "\n";

if (!empty($missing)) {
    echo "\n";
    echo "✗ FAILED: Missing extensions:\n";
    foreach ($missing as $ext) {
        echo "  - $ext\n";
    }
    echo "\n";
    exit(1);
}

echo "\n";
echo "✓ SUCCESS: All required extensions loaded\n";
echo "\n";

// Show all loaded extensions
echo "All loaded extensions (" . count($loaded) . "):\n";
echo implode(', ', array_slice($loaded, 0, 20));
if (count($loaded) > 20) {
    echo ', ... (and ' . (count($loaded) - 20) . ' more)';
}
echo "\n";

exit(0);
