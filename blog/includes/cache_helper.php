<?php
class CacheHelper {
    private $cacheDir;
    private $enabled;
    
    public function __construct() {
        $this->cacheDir = __DIR__ . '/../cache/';
        $this->enabled = true;
        
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function get($key) {
        if (!$this->enabled) return false;
        
        $filename = $this->cacheDir . md5($key);
        if (!file_exists($filename)) return false;
        
        $data = file_get_contents($filename);
        $cached = unserialize($data);
        
        if ($cached['expires'] < time()) {
            unlink($filename);
            return false;
        }
        
        return $cached['data'];
    }
    
    public function set($key, $data, $ttl = 3600) {
        if (!$this->enabled) return false;
        
        $cached = [
            'expires' => time() + $ttl,
            'data' => $data
        ];
        
        $filename = $this->cacheDir . md5($key);
        return file_put_contents($filename, serialize($cached));
    }
    
    public function clear($key = null) {
        if ($key) {
            $filename = $this->cacheDir . md5($key);
            if (file_exists($filename)) {
                unlink($filename);
            }
        } else {
            array_map('unlink', glob($this->cacheDir . '*'));
        }
    }
} 