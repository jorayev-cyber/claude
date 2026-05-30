<?php
/**
 * Saytdan lid qabul qilish
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => clean($_POST['full_name'] ?? ''),
        'phone' => clean($_POST['phone'] ?? ''),
        'course_interest' => $_POST['course_interest'] ?: null,
        'source' => 'website',
        'notes' => clean($_POST['notes'] ?? ''),
        'status' => 'new'
    ];
    
    if (!empty($data['full_name']) && !empty($data['phone'])) {
        $db->insert('leads', $data);
        setFlash('success', 'Murojaatingiz qabul qilindi! Tez orada siz bilan bog\'lanamiz.');
    } else {
        setFlash('danger', 'Iltimos, ism va telefon raqamingizni kiriting');
    }
}

redirect(BASE_URL . '#contact');
