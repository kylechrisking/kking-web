<?php
require_once 'includes/db.php';
require_once 'includes/seo_helper.php';

$config = require 'config.php';
$seo = new SEOHelper($config);

header('Content-Type: application/xml; charset=utf-8');
echo $seo->generateSitemap(); 