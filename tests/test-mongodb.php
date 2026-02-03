<?php

/**
 * MongoDB extension test
 * Validates MongoDB driver is loaded and functional
 */

echo "====================================\n";
echo "Testing MongoDB Extension\n";
echo "====================================\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "\n";

// Test 1: Extension loaded
echo "Test 1: Extension loaded\n";
echo "--------------------------------------\n";
if (!extension_loaded('mongodb')) {
    echo "✗ FAILED: MongoDB extension not loaded\n";
    echo "\n";
    echo "Available extensions:\n";
    print_r(get_loaded_extensions());
    exit(1);
}
echo "✓ PASSED: MongoDB extension loaded\n";
echo "\n";

// Test 2: Classes available
echo "Test 2: MongoDB classes available\n";
echo "--------------------------------------\n";
$classes = [
    'MongoDB\Driver\Manager',
    'MongoDB\Driver\Command',
    'MongoDB\Driver\Query',
    'MongoDB\Driver\BulkWrite',
    'MongoDB\BSON\ObjectId',
];

foreach ($classes as $class) {
    if (!class_exists($class)) {
        echo "✗ FAILED: Class $class not found\n";
        exit(1);
    }
    echo "✓ $class\n";
}
echo "\n";

// Test 3: Basic instantiation
echo "Test 3: Basic instantiation\n";
echo "--------------------------------------\n";
try {
    // Create manager (won't connect until operation is performed)
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    echo "✓ MongoDB\\Driver\\Manager instantiated\n";

    // Create ObjectId
    $objectId = new MongoDB\BSON\ObjectId();
    echo "✓ MongoDB\\BSON\\ObjectId created: {$objectId}\n";

    // Try to connect (this will fail if no server, but that's OK)
    try {
        $command = new MongoDB\Driver\Command(['ping' => 1]);
        $result = $manager->executeCommand('admin', $command);
        echo "✓ Successfully connected to MongoDB server\n";
    } catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
        echo "⚠ Cannot connect to MongoDB server (this is expected if no server is running)\n";
        echo "  Connection would work if MongoDB server was available\n";
    } catch (MongoDB\Driver\Exception\Exception $e) {
        echo "⚠ MongoDB connection test: " . get_class($e) . "\n";
        echo "  (This is expected if no MongoDB server is running)\n";
    }

} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    echo "\n";
    exit(1);
}

echo "\n";
echo "====================================\n";
echo "✓ SUCCESS: MongoDB extension functional\n";
echo "====================================\n";
echo "\n";
echo "MongoDB extension version: " . phpversion('mongodb') . "\n";
echo "\n";
echo "Note: To test full MongoDB functionality, start a MongoDB\n";
echo "server and run this test again.\n";
echo "\n";

exit(0);
