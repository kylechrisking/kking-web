<?php
session_start();

define('SITE_ROOT', '/');
define('BLOG_PATH', __DIR__ . '/../blog/posts/');
define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_DEFAULT));  // temporary test password

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1); 