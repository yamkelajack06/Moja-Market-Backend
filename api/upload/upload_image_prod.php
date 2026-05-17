<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../response/response.php';

class ImageUpload {

    // Accepts a JSON body with "image" (base64 string) and "mimeType",
    // uploads directly to Cloudinary via cURL, and returns the permanent hosted URL
    public static function uploadImage() {
        try {
            $json     = file_get_contents('php://input');
            $data     = json_decode($json, true);
            $base64   = $data['image']    ?? '';
            $mimeType = $data['mimeType'] ?? 'image/jpeg';

            if (empty($base64)) {
                return new Response(false, "No image data received");
            }

            // Validate MIME type before sending to Cloudinary
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                return new Response(false, "Unsupported file type: " . $mimeType);
            }

            // Decode and check size before uploading
            $imageBytes = base64_decode($base64, true);
            if ($imageBytes === false) {
                return new Response(false, "Invalid base64 image data");
            }

            if (strlen($imageBytes) > 10 * 1024 * 1024) {
                return new Response(false, "File too large (max 10 MB)");
            }

            // Pull Cloudinary credentials from Render environment variables
            $cloudName    = getenv('CLOUDINARY_CLOUD_NAME');
            $uploadPreset = getenv('CLOUDINARY_UPLOAD_PRESET');

            if (empty($cloudName) || empty($uploadPreset)) {
                return new Response(false, "Cloudinary is not configured on the server");
            }

            // Cloudinary expects a data URI: data:<mimeType>;base64,<data>
            $dataUri   = 'data:' . $mimeType . ';base64,' . $base64;
            $uploadUrl = 'https://api.cloudinary.com/v1_1/' . $cloudName . '/image/upload';

            // Use cURL for the upload — more reliable than file_get_contents for external calls
            $ch = curl_init($uploadUrl);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => [
                    'file'          => $dataUri,
                    'upload_preset' => $uploadPreset,
                ],
                CURLOPT_TIMEOUT        => 30,
            ]);

            $rawResponse = curl_exec($ch);
            $curlError   = curl_error($ch);
            curl_close($ch);

            if ($rawResponse === false) {
                return new Response(false, "cURL error: " . $curlError);
            }

            $result = json_decode($rawResponse, true);

            // Cloudinary returns "secure_url" on a successful upload
            if (empty($result['secure_url'])) {
                $errorMsg = $result['error']['message'] ?? 'Unknown Cloudinary error';
                return new Response(false, "Cloudinary upload failed: " . $errorMsg);
            }

            // Return the permanent HTTPS URL inside data so Android reads response.data.url
            return new Response(true, "Image uploaded successfully", ['url' => $result['secure_url']]);

        } catch (Exception $e) {
            return new Response(false, "Upload error: " . $e->getMessage());
        }
    }
}