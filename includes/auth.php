<?php
/**
 * Autentifikatsiya funksiyalari
 * O'quv Markaz CRM
 */

// Login
function login($email, $password) {
    global $db;
    
    $user = $db->fetch("SELECT * FROM users WHERE email = ? AND status = 'active'", [$email]);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_avatar'] = $user['avatar'];
        
        // Last login yangilash
        $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
        
        logActivity($user['id'], 'login', 'Tizimga kirdi');
        
        return true;
    }
    
    return false;
}

// Logout
function logout() {
    if (isset($_SESSION['user_id'])) {
        logActivity($_SESSION['user_id'], 'logout', 'Tizimdan chiqdi');
    }
    session_destroy();
    redirect(BASE_URL . 'login.php');
}

// Foydalanuvchi kirganmi tekshirish
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Role tekshirish
function hasRole($roles) {
    if (!isLoggedIn()) return false;
    if (is_string($roles)) $roles = [$roles];
    return in_array($_SESSION['user_role'], $roles);
}

// Sahifaga kirish huquqi
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('danger', 'Iltimos, tizimga kiring');
        redirect(BASE_URL . 'login.php');
    }
}

// Role bo'yicha kirish
function requireRole($roles) {
    requireLogin();
    if (!hasRole($roles)) {
        setFlash('danger', 'Sizda bu sahifaga kirish huquqi yo\'q');
        redirect(BASE_URL . 'admin/');
    }
}

// Joriy foydalanuvchi ma'lumotlari
function currentUser() {
    global $db;
    if (!isLoggedIn()) return null;
    return $db->fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

// Parolni yangilash
function changePassword($userId, $oldPassword, $newPassword) {
    global $db;
    $user = $db->fetch("SELECT password FROM users WHERE id = ?", [$userId]);
    
    if (!password_verify($oldPassword, $user['password'])) {
        return ['error' => 'Eski parol noto\'g\'ri'];
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $db->update('users', ['password' => $hashedPassword], 'id = ?', [$userId]);
    
    logActivity($userId, 'password_change', 'Parolni o\'zgartirdi');
    return ['success' => true];
}

// Yangi foydalanuvchi qo'shish
function createUser($data) {
    global $db;
    
    // Email tekshirish
    $exists = $db->fetch("SELECT id FROM users WHERE email = ?", [$data['email']]);
    if ($exists) {
        return ['error' => 'Bu email allaqachon ro\'yxatdan o\'tgan'];
    }
    
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $id = $db->insert('users', $data);
    
    logActivity($_SESSION['user_id'] ?? null, 'user_create', 'Yangi foydalanuvchi yaratildi: ' . $data['full_name']);
    return ['success' => true, 'id' => $id];
}
