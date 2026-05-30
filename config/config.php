<?php
/**
 * Asosiy Konfiguratsiya
 * O'quv Markaz CRM
 */

session_start();

// Asosiy sozlamalar
define('BASE_URL', 'http://localhost/claude/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');
define('ROOT_DIR', __DIR__ . '/../');

// Xatoliklarni ko'rsatish (production da o'chirish)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vaqt zonasi
date_default_timezone_set('Asia/Tashkent');

// Database ulanish
require_once __DIR__ . '/database.php';

// Yordamchi funksiyalar
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Database instance
$db = Database::getInstance();
