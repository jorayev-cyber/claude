<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole(['teacher']);

$teacherId = $_SESSION['user_id'];

// O'qituvchining guruhlari
$myGroups = $db->fetchAll("SELECT g.*, c.name as course_name,
    (SELECT COUNT(*) FROM group_students gs WHERE gs.group_id = g.id AND gs.status = 'active') as student_count
    FROM groups_table g JOIN courses c ON g.course_id = c.id
    WHERE g.teacher_id = ? AND g.status = 'active'", [$teacherId]);

$totalStudents = 0;
foreach ($myGroups as $g) $totalStudents += $g['student_count'];

// Bugungi davomat
$todayAttendance = $db->fetch("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present
    FROM attendance WHERE marked_by = ? AND date = CURRENT_DATE()", [$teacherId]);

include __DIR__ . '/../includes/sidebar_teacher.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="row g-4 mb-4">
    <div class="col-md-4 animate-in">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?= count($myGroups) ?></div>
            <div class="stat-label">Mening guruhlarim</div>
        </div>
    </div>
    <div class="col-md-4 animate-in">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-value"><?= $totalStudents ?></div>
            <div class="stat-label">Jami o'quvchilar</div>
        </div>
    </div>
    <div class="col-md-4 animate-in">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-clipboard-check"></i></div>
            <div class="stat-value"><?= ($todayAttendance['present'] ?? 0) ?>/<?= ($todayAttendance['total'] ?? 0) ?></div>
            <div class="stat-label">Bugungi davomat</div>
        </div>
    </div>
</div>

<div class="card animate-in">
    <div class="card-header">
        <h5><i class="fas fa-users me-2"></i>Mening guruhlarim</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Guruh</th>
                        <th>Kurs</th>
                        <th>O'quvchilar</th>
                        <th>Jadval</th>
                        <th>Vaqt</th>
                        <th>Amal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myGroups as $g): ?>
                    <tr>
                        <td><strong><?= clean($g['name']) ?></strong></td>
                        <td><?= clean($g['course_name']) ?></td>
                        <td><span class="badge bg-primary"><?= $g['student_count'] ?>/<?= $g['max_students'] ?></span></td>
                        <td><?= clean($g['schedule']) ?></td>
                        <td><?= clean($g['time_slot']) ?></td>
                        <td>
                            <a href="attendance.php?group_id=<?= $g['id'] ?>" class="btn btn-sm btn-primary-gradient">
                                <i class="fas fa-clipboard-check me-1"></i>Davomat
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
