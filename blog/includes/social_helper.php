<?php
class SocialHelper {
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    public function getSharingButtons($post) {
        $url = urlencode($this->config['site_url'] . '/post.php?slug=' . $post['slug']);
        $title = urlencode($post['title']);
        
        $buttons = [
            'twitter' => [
                'url' => "https://twitter.com/intent/tweet?url={$url}&text={$title}&via=" . trim($this->config['meta']['twitter'], '@'),
                'icon' => '<svg>...</svg>',
                'label' => 'Share on Twitter'
            ],
            'facebook' => [
                'url' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
                'icon' => '<svg>...</svg>',
                'label' => 'Share on Facebook'
            ],
            'linkedin' => [
                'url' => "https://www.linkedin.com/shareArticle?mini=true&url={$url}&title={$title}",
                'icon' => '<svg>...</svg>',
                'label' => 'Share on LinkedIn'
            ]
        ];
        
        $html = '<div class="social-sharing">';
        foreach ($buttons as $network => $data) {
            $html .= sprintf(
                '<a href="%s" class="share-button %s" target="_blank" rel="noopener">%s<span>%s</span></a>',
                $data['url'],
                $network,
                $data['icon'],
                $data['label']
            );
        }
        $html .= '</div>';
        
        return $html;
    }
} 