<?php
// Basic PHP test
echo "PHP is working";

// Test database connection
try {
    require_once 'includes/db.php';
    echo "<br>Database connection successful";
} catch (Exception $e) {
    echo "<br>Database error: " . $e->getMessage();
}

// Test config
try {
    $config = require_once 'config.php';
    echo "<br>Config loaded successfully";
    echo "<br>Site name: " . $config['site_name'];
} catch (Exception $e) {
    echo "<br>Config error: " . $e->getMessage();
}

// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Print PHP info
phpinfo(); 