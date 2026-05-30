<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole(['manager']);

$totalStudents = $db->count('students', "status = 'active'");
$totalGroups = $db->count('groups_table', "status = 'active'");
$newLeads = $db->count('leads', "status = 'new'");
$monthlyIncome = $db->fetch("SELECT COALESCE(SUM(amount),0) as t FROM payments WHERE status = 'paid' AND MONTH(paid_at) = MONTH(NOW())")['t'];

$recentStudents = $db->fetchAll("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");
$recentLeads = $db->fetchAll("SELECT * FROM leads WHERE status = 'new' ORDER BY created_at DESC LIMIT 5");

include __DIR__ . '/../includes/sidebar_manager.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="row g-4 mb-4">
    <div class="col-md-3 animate-in">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-value"><?= $totalStudents ?></div>
            <div class="stat-label">O'quvchilar</div>
        </div>
    </div>
    <div class="col-md-3 animate-in">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?= $totalGroups ?></div>
            <div class="stat-label">Guruhlar</div>
        </div>
    </div>
    <div class="col-md-3 animate-in">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-bullhorn"></i></div>
            <div class="stat-value"><?= $newLeads ?></div>
            <div class="stat-label">Yangi lidlar</div>
        </div>
    </div>
    <div class="col-md-3 animate-in">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-value"><?= number_format($monthlyIncome, 0, '', ' ') ?></div>
            <div class="stat-label">Oylik daromad</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6 animate-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-graduate me-2"></i>So'nggi o'quvchilar</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <?php foreach ($recentStudents as $s): ?>
                            <tr>
                                <td><strong><?= clean($s['full_name']) ?></strong></td>
                                <td><?= clean($s['phone']) ?></td>
                                <td><?= statusBadge($s['status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 animate-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bullhorn me-2"></i>Yangi lidlar</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <?php foreach ($recentLeads as $l): ?>
                            <tr>
                                <td><strong><?= clean($l['full_name']) ?></strong></td>
                                <td><?= clean($l['phone']) ?></td>
                                <td><span class="badge bg-secondary"><?= $l['source'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
