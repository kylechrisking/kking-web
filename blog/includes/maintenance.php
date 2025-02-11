<?php
class MaintenanceMode {
    private $file;
    
    public function __construct() {
        $this->file = __DIR__ . '/../.maintenance';
    }
    
    public function isEnabled() {
        return file_exists($this->file);
    }
    
    public function enable($message = '') {
        file_put_contents($this->file, $message ?: 'Site is under maintenance');
    }
    
    public function disable() {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }
    
    public function getMessage() {
        return file_exists($this->file) ? file_get_contents($this->file) : '';
    }
} 