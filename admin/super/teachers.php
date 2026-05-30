<?php
$pageTitle = "O'qituvchilar";
$currentPage = 'teachers';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

$teachers = $db->fetchAll("SELECT u.*, 
    (SELECT COUNT(*) FROM groups_table g WHERE g.teacher_id = u.id AND g.status = 'active') as group_count,
    (SELECT COUNT(DISTINCT gs.student_id) FROM groups_table g2 JOIN group_students gs ON gs.group_id = g2.id WHERE g2.teacher_id = u.id AND gs.status = 'active') as student_count
    FROM users u WHERE u.role = 'teacher' ORDER BY u.created_at DESC");

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span class="text-muted">Jami: <strong><?= count($teachers) ?></strong> ta o'qituvchi</span>
    <a href="users.php" class="btn btn-primary-gradient">
        <i class="fas fa-plus me-2"></i>Yangi o'qituvchi
    </a>
</div>

<div class="row g-4">
    <?php foreach ($teachers as $t): ?>
    <div class="col-lg-4 col-md-6 animate-in">
        <div class="card hover-lift h-100">
            <div class="card-body text-center">
                <div class="avatar mx-auto mb-3" style="width:64px;height:64px;border-radius:16px;font-size:24px;">
                    <span><?= strtoupper(substr($t['full_name'], 0, 1)) ?></span>
                </div>
                <h5 class="mb-1"><?= clean($t['full_name']) ?></h5>
                <p class="text-muted small mb-3"><?= clean($t['email']) ?></p>
                <div class="d-flex justify-content-center gap-4 mb-3">
                    <div class="text-center">
                        <strong class="d-block"><?= $t['group_count'] ?></strong>
                        <small class="text-muted">Guruhlar</small>
                    </div>
                    <div class="text-center">
                        <strong class="d-block"><?= $t['student_count'] ?></strong>
                        <small class="text-muted">O'quvchilar</small>
                    </div>
                </div>
                <?= statusBadge($t['status']) ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
