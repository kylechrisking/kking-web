<?php
class AIHelper {
    private $api_key;
    private $model;
    
    public function __construct($api_key, $model = 'gpt-3.5-turbo') {
        $this->api_key = $api_key;
        $this->model = $model;
    }
    
    public function generatePost($topic, $type = 'tech_news') {
        $prompts = [
            'tech_news' => "Write a detailed tech news article about {$topic}. Include latest developments, impact on the industry, and future implications. Format in markdown with proper headings, paragraphs, and bullet points where appropriate.",
            'update' => "Write a comprehensive update article about {$topic}. Include new features, improvements, security fixes, and installation instructions. Format in markdown with clear sections.",
            'vulnerability' => "Write a detailed cybersecurity article about the {$topic} vulnerability. Include technical details, impact assessment, mitigation steps, and recommendations. Format in markdown.",
            'event' => "Write an engaging article about the {$topic} tech event. Include key announcements, highlights, and industry implications. Format in markdown."
        ];
        
        $prompt = $prompts[$type] ?? $prompts['tech_news'];
        
        $response = $this->callAPI($prompt);
        
        // Generate SEO-friendly metadata
        $metadata = $this->generateMetadata($response['content'], $topic);
        
        return [
            'title' => $metadata['title'],
            'content' => $response['content'],
            'excerpt' => $metadata['excerpt'],
            'tags' => $metadata['tags'],
            'meta_description' => $metadata['meta_description']
        ];
    }
    
    private function callAPI($prompt) {
        $url = 'https://api.openai.com/v1/chat/completions';
        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional tech journalist writing for a modern tech blog. Write in a clear, engaging style with proper markdown formatting.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return [
            'content' => $result['choices'][0]['message']['content']
        ];
    }
    
    private function generateMetadata($content, $topic) {
        // Generate SEO-friendly title
        $titlePrompt = "Generate a SEO-friendly title (max 60 chars) for an article about: {$topic}";
        $titleResponse = $this->callAPI($titlePrompt);
        
        // Generate meta description
        $descPrompt = "Generate a compelling meta description (max 155 chars) for an article about: {$topic}";
        $descResponse = $this->callAPI($descPrompt);
        
        // Generate relevant tags
        $tagsPrompt = "Generate 3-5 relevant tech tags for an article about: {$topic}";
        $tagsResponse = $this->callAPI($tagsPrompt);
        
        // Extract excerpt from content
        $excerpt = substr(strip_tags($content), 0, 200) . '...';
        
        return [
            'title' => trim($titleResponse['content']),
            'excerpt' => $excerpt,
            'tags' => explode(',', trim($tagsResponse['content'])),
            'meta_description' => trim($descResponse['content'])
        ];
    }
} 