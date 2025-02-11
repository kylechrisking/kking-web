<?php
class AnalyticsHelper {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function trackEvent($event_type, $data = []) {
        $stmt = $this->db->prepare("
            INSERT INTO analytics (
                page_url,
                post_id,
                visitor_ip,
                user_agent,
                referrer,
                event_type
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SERVER['REQUEST_URI'] ?? '',
            $data['post_id'] ?? null,
            $this->getIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_REFERER'] ?? '',
            $event_type
        ]);
    }
    
    public function getStats($period = '7days') {
        $stats = [];
        
        // Date range
        switch ($period) {
            case '24h':
                $date = 'DATE_SUB(NOW(), INTERVAL 24 HOUR)';
                break;
            case '7days':
                $date = 'DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case '30days':
                $date = 'DATE_SUB(NOW(), INTERVAL 30 DAY)';
                break;
            default:
                $date = 'DATE_SUB(NOW(), INTERVAL 7 DAY)';
        }
        
        // Total pageviews
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM analytics 
            WHERE event_type = 'pageview' 
            AND created_at >= {$date}
        ");
        $stmt->execute();
        $stats['pageviews'] = $stmt->fetchColumn();
        
        // Popular posts
        $stats['popular_posts'] = $this->db->query("
            SELECT p.title, p.slug, COUNT(*) as views
            FROM analytics a
            JOIN posts p ON a.post_id = p.id
            WHERE a.event_type = 'post_view'
            AND a.created_at >= {$date}
            GROUP BY p.id
            ORDER BY views DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        // Top referrers
        $stats['top_referrers'] = $this->db->query("
            SELECT referrer, COUNT(*) as count
            FROM analytics
            WHERE referrer != ''
            AND created_at >= {$date}
            GROUP BY referrer
            ORDER BY count DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        // Popular search terms
        $stats['search_terms'] = $this->db->query("
            SELECT page_url, COUNT(*) as count
            FROM analytics
            WHERE event_type = 'search'
            AND created_at >= {$date}
            GROUP BY page_url
            ORDER BY count DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    private function getIP() {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
} 