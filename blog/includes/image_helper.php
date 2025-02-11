<?php
class ImageHelper {
    private $config;
    private $uploadDir;
    
    public function __construct($config) {
        $this->config = $config;
        $this->uploadDir = __DIR__ . '/../uploads/';
        
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function optimizeAndSave($file, $type = 'post') {
        $filename = $this->generateFilename($file['name']);
        $path = $this->uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        // Get image info
        $info = getimagesize($path);
        if (!$info) {
            unlink($path);
            throw new Exception('Invalid image file');
        }
        
        // Create image resource based on type
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($path);
                break;
            default:
                unlink($path);
                throw new Exception('Unsupported image type');
        }
        
        // Resize based on type
        switch ($type) {
            case 'social':
                $width = 1200;
                $height = 630;
                break;
            case 'featured':
                $width = 800;
                $height = 400;
                break;
            default:
                $width = 1200;
                $height = null;
        }
        
        $image = $this->resize($image, $width, $height);
        
        // Save optimized image
        $optimizedPath = $this->uploadDir . 'opt_' . $filename;
        imagejpeg($image, $optimizedPath, 85);
        
        // Clean up
        imagedestroy($image);
        unlink($path);
        
        return '/uploads/opt_' . $filename;
    }
    
    private function resize($image, $maxWidth, $maxHeight = null) {
        $width = imagesx($image);
        $height = imagesy($image);
        
        if ($maxHeight === null) {
            // Maintain aspect ratio
            $ratio = $maxWidth / $width;
            $newWidth = $maxWidth;
            $newHeight = $height * $ratio;
        } else {
            // Crop to exact dimensions
            $sourceRatio = $width / $height;
            $targetRatio = $maxWidth / $maxHeight;
            
            if ($sourceRatio > $targetRatio) {
                $newHeight = $maxHeight;
                $newWidth = $width * ($maxHeight / $height);
                $x = ($newWidth - $maxWidth) / 2;
                $y = 0;
            } else {
                $newWidth = $maxWidth;
                $newHeight = $height * ($maxWidth / $width);
                $x = 0;
                $y = ($newHeight - $maxHeight) / 2;
            }
        }
        
        $resized = imagecreatetruecolor($maxWidth, $maxHeight ?? $newHeight);
        
        // Preserve transparency
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        
        // Resize and crop if needed
        imagecopyresampled(
            $resized, $image,
            $maxHeight ? -$x : 0, $maxHeight ? -$y : 0,
            0, 0,
            $newWidth, $newHeight,
            $width, $height
        );
        
        return $resized;
    }
    
    private function generateFilename($originalName) {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . time() . '.' . $ext;
    }
} 