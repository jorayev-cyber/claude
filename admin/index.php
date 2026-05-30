<?php
/**
 * Admin Panel - Roli bo'yicha yo'naltirish
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

switch ($_SESSION['user_role']) {
    case 'super_admin':
        redirect(ADMIN_URL . 'super/');
        break;
    case 'manager':
        redirect(ADMIN_URL . 'manager/');
        break;
    case 'teacher':
        redirect(ADMIN_URL . 'teacher/');
        break;
    default:
        redirect(BASE_URL . 'login.php');
}
