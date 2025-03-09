<?php require_once 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <h1><a href="<?= SITE_URL ?>"><?= SITE_TITLE ?></a></h1>
        </div>
    </header>
    <nav class="blog-nav">
        <div class="nav-content">
            <a href="/" class="logo">KK</a>
            <a href="/blog" class="blog-home">Blog</a>
        </div>
    </nav>
    <main class="blog-content"> 