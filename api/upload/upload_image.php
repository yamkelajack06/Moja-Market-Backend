<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../response/response.php';

class ImageUpload {

    // Accepts a JSON body with "image" (base64 string) and "mimeType",
    // decodes and saves the file to the local uploads directory,
    // then returns the public URL for the Android app to store
    public static function uploadImage() {
        try {
            $json     = file_get_contents('php://input');
            $data     = json_decode($json, true);
            $base64   = $data['image']    ?? '';
            $mimeType = $data['mimeType'] ?? 'image/jpeg';

            if (empty($base64)) {
                return new Response(false, "No image data received");
            }

            // Validate MIME type
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                return new Response(false, "Unsupported file type: " . $mimeType);
            }

            // Decode base64 to raw bytes
            $imageBytes = base64_decode($base64, true);
            if ($imageBytes === false) {
                return new Response(false, "Invalid base64 image data");
            }

            // Enforce 10 MB limit
            if (strlen($imageBytes) > 10 * 1024 * 1024) {
                return new Response(false, "File too large (max 10 MB)");
            }

            // Map MIME type to file extension
            $extMap = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ];
            $ext = $extMap[$mimeType] ?? 'jpg';

            // Create uploads directory if it doesn't exist
            $uploadDir = __DIR__ . '/../../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Save to disk with a unique filename to avoid overwrites
            $uniqueName = uniqid('img_', true) . '.' . $ext;
            $destPath   = $uploadDir . $uniqueName;

            if (file_put_contents($destPath, $imageBytes) === false) {
                return new Response(false, "Failed to save image");
            }

            // Build the public URL the Android app will store and load
            $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host      = $_SERVER['HTTP_HOST'];
            $publicUrl = $protocol . '://' . $host . '/uploads/' . $uniqueName;

            // Return the URL inside data so Android reads response.data.url
            return new Response(true, "Image uploaded successfully", ['url' => $publicUrl]);

        } catch (Exception $e) {
            return new Response(false, "Upload error: " . $e->getMessage());
        }
    }
}