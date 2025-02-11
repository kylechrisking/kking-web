<?php
class SEOHelper {
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    public function generateMetaTags($data = []) {
        $tags = [
            // Basic meta tags
            '<meta name="description" content="' . ($data['meta_description'] ?? $this->config['site_description']) . '">',
            '<meta name="keywords" content="' . ($data['meta_keywords'] ?? '') . '">',
            
            // Open Graph tags
            '<meta property="og:title" content="' . ($data['title'] ?? $this->config['site_name']) . '">',
            '<meta property="og:description" content="' . ($data['meta_description'] ?? $this->config['site_description']) . '">',
            '<meta property="og:image" content="' . ($data['social_image'] ?? $this->config['site_url'] . '/assets/images/default-social.jpg') . '">',
            '<meta property="og:url" content="' . $this->getCurrentUrl() . '">',
            '<meta property="og:type" content="article">',
            '<meta property="og:site_name" content="' . $this->config['site_name'] . '">',
            
            // Twitter Card tags
            '<meta name="twitter:card" content="summary_large_image">',
            '<meta name="twitter:site" content="' . $this->config['meta']['twitter'] . '">',
            '<meta name="twitter:title" content="' . ($data['title'] ?? $this->config['site_name']) . '">',
            '<meta name="twitter:description" content="' . ($data['meta_description'] ?? $this->config['site_description']) . '">',
            '<meta name="twitter:image" content="' . ($data['social_image'] ?? $this->config['site_url'] . '/assets/images/default-social.jpg') . '">',
            
            // Canonical URL
            '<link rel="canonical" href="' . $this->getCurrentUrl() . '">'
        ];
        
        return implode("\n    ", $tags);
    }
    
    public function generateStructuredData($post = null) {
        $data = [
            "@context" => "https://schema.org",
            "@type" => "Article",
            "headline" => $post['title'] ?? $this->config['site_name'],
            "description" => $post['meta_description'] ?? $this->config['site_description'],
            "image" => $post['social_image'] ?? $this->config['site_url'] . '/assets/images/default-social.jpg',
            "datePublished" => $post['created_at'] ?? date('c'),
            "dateModified" => $post['updated_at'] ?? date('c'),
            "author" => [
                "@type" => "Person",
                "name" => "Kyle King",
                "url" => $this->config['site_url']
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => $this->config['site_name'],
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => $this->config['site_url'] . '/assets/images/logo.png'
                ]
            ]
        ];
        
        return '<script type="application/ld+json">' . json_encode($data, JSON_PRETTY_PRINT) . '</script>';
    }
    
    private function getCurrentUrl() {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
               "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    
    public function generateSitemap() {
        global $db;
        
        $posts = $db->query("
            SELECT slug, updated_at 
            FROM posts 
            WHERE status = 'published' 
            ORDER BY created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
        
        // Add homepage
        $url = $xml->addChild('url');
        $url->addChild('loc', $this->config['site_url']);
        $url->addChild('changefreq', 'daily');
        $url->addChild('priority', '1.0');
        
        // Add posts
        foreach ($posts as $post) {
            $url = $xml->addChild('url');
            $url->addChild('loc', $this->config['site_url'] . '/post.php?slug=' . $post['slug']);
            $url->addChild('lastmod', date('c', strtotime($post['updated_at'])));
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.8');
        }
        
        return $xml->asXML();
    }
} 