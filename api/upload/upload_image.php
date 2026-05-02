<?php
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../response/response.php';

    class ImageUpload {

        public static function uploadImage() {
            $success = false;

            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                return new Response($success, "No image received or upload error");
            }

            $file     = $_FILES['image'];
            $tmpPath  = $file['tmp_name'];
            $origName = basename($file['name']);

            // Validate mime type
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mimeType     = mime_content_type($tmpPath);

            if (!in_array($mimeType, $allowedMimes)) {
                return new Response($success, "Unsupported file type: " . $mimeType);
            }

            // Cap file size at 10 MB
            if ($file['size'] > 10 * 1024 * 1024) {
                return new Response($success, "File too large (max 10 MB)");
            }

            $uploadDir = __DIR__ . '/../../uploads/';

            // Create uploads directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
            $destPath = $uploadDir . $safeName;

            if (!move_uploaded_file($tmpPath, $destPath)) {
                return new Response($success, "Failed to save file");
            }

            $success = true;
            $publicUrl = '/uploads/' . $safeName;
            return new Response($success, $publicUrl);
        }
    }