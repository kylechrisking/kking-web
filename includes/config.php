<?php
session_start();

define('SITE_ROOT', '/');
define('BLOG_PATH', __DIR__ . '/../blog/posts/');
define('ADMIN_PASSWORD_HASH', password_hash('your-secure-password', PASSWORD_DEFAULT));

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_blog');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1); 