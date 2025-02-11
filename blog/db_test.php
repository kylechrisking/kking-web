<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: Basic PHP working<br>";

// Test file includes
if (file_exists('includes/db.php')) {
    echo "Step 2: db.php exists<br>";
} else {
    die("db.php not found");
}

if (file_exists('config.php')) {
    echo "Step 3: config.php exists<br>";
} else {
    die("config.php not found");
}

// Try loading just the db file
try {
    require_once 'includes/db.php';
    echo "Step 4: db.php loaded successfully<br>";
} catch (Exception $e) {
    die("Error loading db.php: " . $e->getMessage());
}

echo "Test complete"; 