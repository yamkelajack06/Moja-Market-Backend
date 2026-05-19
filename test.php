<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/response/response.php';
require_once __DIR__ . '/utils/utils.php';
require_once __DIR__ . '/api/posts/items.php';
require_once __DIR__ . '/api/posts/wants.php';

// Connect to database
try {
    Database::connect();
    echo "Database connected.\n\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// ── ITEMS FEED ──────────────────────────────
echo "=== ITEMS FEED ===\n";
$itemFeedResult = ItemDatabase::getFeed();
echo json_encode($itemFeedResult->toArray(), JSON_PRETTY_PRINT) . "\n\n";

// ── WANTS FEED ──────────────────────────────
echo "=== WANTS FEED ===\n";
$wantFeedResult = (new WantsDatabase())->getWantRequestFeed();
echo json_encode($wantFeedResult->toArray(), JSON_PRETTY_PRINT) . "\n\n";
