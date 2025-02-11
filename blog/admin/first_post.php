<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/ai_helper.php';
$config = require '../config.php';

$ai = new AIHelper($config['openai_api_key']);

$topic = "Welcome to My Tech Blog: A Journey Through Technology";
$content = <<<EOT
# Welcome to My Tech Blog

I'm excited to launch this tech blog where I'll be sharing insights, updates, and deep dives into various aspects of technology. From the latest software updates to cybersecurity vulnerabilities, from tech events to industry trends - this blog will be your go-to resource for staying informed about the ever-evolving tech landscape.

## What to Expect

Here's what you can look forward to:

- **Tech News & Analysis**: Breaking down the latest technological developments and their implications
- **Software Updates**: Detailed coverage of important updates for Windows, macOS, and popular software
- **Security Alerts**: Timely information about vulnerabilities and how to protect yourself
- **Event Coverage**: Updates from major tech conferences and product launches
- **Tutorial & Guides**: Step-by-step guides for various technical tasks

## Why Another Tech Blog?

In today's fast-paced tech world, staying informed is crucial. My goal is to provide clear, concise, and accurate information that helps you understand complex tech topics. Whether you're a developer, IT professional, or tech enthusiast, you'll find valuable content here.

## Join the Journey

I encourage you to:
1. Subscribe to the RSS feed to stay updated
2. Follow me on social media for real-time updates
3. Engage in discussions through comments
4. Share posts that you find helpful

## What's Coming Next

In the upcoming posts, we'll be covering:
- Latest developments in AI and machine learning
- Comprehensive guides on cybersecurity best practices
- Reviews of new tech products and tools
- Industry insights and trend analysis

Stay tuned for regular updates, and don't hesitate to reach out with topics you'd like to see covered!

## Technical Details

This blog is built with modern web technologies, featuring:
- Responsive design for optimal viewing on all devices
- Dark/light mode support
- Markdown support for better content formatting
- Fast loading times and SEO optimization

Welcome aboard, and let's explore technology together!
EOT;

try {
    $db->beginTransaction();
    
    // Insert the first post
    $stmt = $db->prepare("
        INSERT INTO posts (
            title, 
            slug, 
            content, 
            excerpt,
            status,
            meta_description
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $slug = 'welcome-to-my-tech-blog';
    $excerpt = "Welcome to my tech blog! Join me on a journey through technology, where we'll explore the latest developments, share insights, and dive deep into the world of tech.";
    $meta_description = "A tech blog covering latest updates, cybersecurity, tutorials, and industry insights. Join us for in-depth analysis and stay informed about technology trends.";
    
    $stmt->execute([
        $topic,
        $slug,
        $content,
        $excerpt,
        'published',
        $meta_description
    ]);
    
    $postId = $db->lastInsertId();
    
    // Add some initial tags
    $initialTags = ['Technology', 'Blog', 'Tech News', 'Updates'];
    foreach ($initialTags as $tagName) {
        $tagSlug = strtolower(str_replace(' ', '-', $tagName));
        
        $stmt = $db->prepare("
            INSERT INTO tags (name, slug) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
        ");
        $stmt->execute([$tagName, $tagSlug]);
        $tagId = $db->lastInsertId();
        
        $stmt = $db->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
        $stmt->execute([$postId, $tagId]);
    }
    
    $db->commit();
    echo "First post created successfully!";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error creating first post: " . $e->getMessage();
} 