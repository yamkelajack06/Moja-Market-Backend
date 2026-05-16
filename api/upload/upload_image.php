<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../response/response.php';

class ImageUpload {
    public static function uploadImage() {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return new Response(false, "No image received or upload error");
        }

        $file     = $_FILES['image'];
        $tmpPath  = $file['tmp_name'];
        $origName = basename($file['name']);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType     = mime_content_type($tmpPath);

        if (!in_array($mimeType, $allowedMimes)) {
            return new Response(false, "Unsupported file type: " . $mimeType);
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            return new Response(false, "File too large (max 10 MB)");
        }

        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Sanitise the filename and make it unique to avoid overwrites
        $ext      = pathinfo($origName, PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
        $uniqueName = $safeName . '_' . uniqid() . '.' . $ext;
        $destPath   = $uploadDir . $uniqueName;

        if (!move_uploaded_file($tmpPath, $destPath)) {
            return new Response(false, "Failed to save file");
        }

        // Build a full public URL the Android app can load with Glide
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'];                    // e.g. "10.129.34.12:8000"
        $publicUrl = $protocol . '://' . $host . '/uploads/' . $uniqueName;

        return new Response(true, $publicUrl);
    }
}