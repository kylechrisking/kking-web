<?php
require_once 'includes/db.php';

$new_password = 'your_secure_password'; // Change this to your desired password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->execute([$hashed_password]);

echo "Password updated successfully!"; 