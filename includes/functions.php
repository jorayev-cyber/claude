<?php
/**
 * Yordamchi funksiyalar
 * O'quv Markaz CRM
 */

// XSS himoya
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Redirect
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Flash xabar
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Pul formati
function formatMoney($amount) {
    return number_format($amount, 0, '.', ' ') . ' UZS';
}

// Sana formati
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

// Vaqt oldin
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) return $diff->y . ' yil oldin';
    if ($diff->m > 0) return $diff->m . ' oy oldin';
    if ($diff->d > 0) return $diff->d . ' kun oldin';
    if ($diff->h > 0) return $diff->h . ' soat oldin';
    if ($diff->i > 0) return $diff->i . ' daqiqa oldin';
    return 'Hozirgina';
}

// Slug yaratish
function createSlug($text) {
    $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
    $text = strtolower(trim($text));
    return preg_replace('/\s+/', '-', $text);
}

// Rasm yuklash
function uploadImage($file, $folder = 'uploads/') {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return ['error' => 'Faqat rasm fayllari ruxsat etilgan'];
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => 'Fayl hajmi 5MB dan oshmasligi kerak'];
    }
    
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $path = ROOT_DIR . $folder . $filename;
    
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $path)) {
        return ['success' => true, 'filename' => $filename, 'path' => $folder . $filename];
    }
    
    return ['error' => 'Fayl yuklanmadi'];
}

// Sahifalash
function paginate($total, $perPage = 15, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset
    ];
}

// Faoliyat logi
function logActivity($userId, $action, $description = '') {
    global $db;
    $db->insert('activity_log', [
        'user_id' => $userId,
        'action' => $action,
        'description' => $description,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
    ]);
}

// CSRF Token
function generateToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Bildirishnoma yuborish
function sendNotification($userId, $title, $message, $type = 'info', $link = '') {
    global $db;
    $db->insert('notifications', [
        'user_id' => $userId,
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'link' => $link
    ]);
}

// Status badge
function statusBadge($status) {
    $badges = [
        'active' => '<span class="badge bg-success">Faol</span>',
        'inactive' => '<span class="badge bg-secondary">Nofaol</span>',
        'blocked' => '<span class="badge bg-danger">Bloklangan</span>',
        'completed' => '<span class="badge bg-info">Tugallangan</span>',
        'cancelled' => '<span class="badge bg-warning">Bekor qilingan</span>',
        'graduated' => '<span class="badge bg-primary">Bitirgan</span>',
        'dropped' => '<span class="badge bg-danger">Ketgan</span>',
        'frozen' => '<span class="badge bg-warning">Muzlatilgan</span>',
        'paid' => '<span class="badge bg-success">To\'langan</span>',
        'pending' => '<span class="badge bg-warning">Kutilmoqda</span>',
        'new' => '<span class="badge bg-primary">Yangi</span>',
        'contacted' => '<span class="badge bg-info">Bog\'lanilgan</span>',
        'interested' => '<span class="badge bg-warning">Qiziqgan</span>',
        'enrolled' => '<span class="badge bg-success">Ro\'yxatdan o\'tgan</span>',
        'lost' => '<span class="badge bg-danger">Yo\'qotilgan</span>',
        'present' => '<span class="badge bg-success">Kelgan</span>',
        'absent' => '<span class="badge bg-danger">Kelmagan</span>',
        'late' => '<span class="badge bg-warning">Kechikkan</span>',
        'excused' => '<span class="badge bg-info">Sababli</span>',
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}
