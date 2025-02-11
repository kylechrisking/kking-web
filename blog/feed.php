<?php
require_once 'includes/db.php';
$config = require 'config.php';

// Get latest posts
$posts = $db->query("
    SELECT p.*, GROUP_CONCAT(t.name) as tags
    FROM posts p
    LEFT JOIN post_tags pt ON p.id = pt.post_id
    LEFT JOIN tags t ON pt.tag_id = t.id
    WHERE p.status = 'published'
    GROUP BY p.id
    ORDER BY p.created_at DESC
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/rss+xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?= htmlspecialchars($config['site_name']) ?></title>
        <link><?= htmlspecialchars($config['site_url']) ?></link>
        <description><?= htmlspecialchars($config['site_description']) ?></description>
        <language>en-us</language>
        <lastBuildDate><?= date(DATE_RSS) ?></lastBuildDate>
        <atom:link href="<?= $config['site_url'] ?>/feed.php" rel="self" type="application/rss+xml" />
        
        <?php foreach ($posts as $post): ?>
            <item>
                <title><?= htmlspecialchars($post['title']) ?></title>
                <link><?= $config['site_url'] ?>/post.php?slug=<?= urlencode($post['slug']) ?></link>
                <guid><?= $config['site_url'] ?>/post.php?slug=<?= urlencode($post['slug']) ?></guid>
                <pubDate><?= date(DATE_RSS, strtotime($post['created_at'])) ?></pubDate>
                <description><![CDATA[<?= $post['excerpt'] ?>]]></description>
                <?php if ($post['tags']): ?>
                    <category><?= htmlspecialchars($post['tags']) ?></category>
                <?php endif; ?>
            </item>
        <?php endforeach; ?>
    </channel>
</rss> 