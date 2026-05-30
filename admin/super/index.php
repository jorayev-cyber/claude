<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

// Statistika
$totalStudents = $db->count('students', "status = 'active'");
$totalGroups = $db->count('groups_table', "status = 'active'");
$totalCourses = $db->count('courses', "status = 'active'");
$totalTeachers = $db->count('users', "role = 'teacher' AND status = 'active'");
$totalLeads = $db->count('leads', "status = 'new'");

// Oylik daromad
$monthlyIncome = $db->fetch(
    "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE MONTH(paid_at) = MONTH(CURRENT_DATE()) AND YEAR(paid_at) = YEAR(CURRENT_DATE()) AND status = 'paid'"
)['total'];

// Oylik xarajat
$monthlyExpense = $db->fetch(
    "SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())"
)['total'];

// So'nggi to'lovlar
$recentPayments = $db->fetchAll(
    "SELECT p.*, s.full_name as student_name FROM payments p JOIN students s ON p.student_id = s.id ORDER BY p.created_at DESC LIMIT 5"
);

// So'nggi lidlar
$recentLeads = $db->fetchAll("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5");

// Haftalik davomat statistikasi
$weeklyAttendance = $db->fetchAll(
    "SELECT date, 
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent
     FROM attendance WHERE date >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) GROUP BY date ORDER BY date"
);

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6 animate-in">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-value"><span class="counter" data-target="<?= $totalStudents ?>"><?= $totalStudents ?></span></div>
            <div class="stat-label">Faol o'quvchilar</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 animate-in">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-value"><span class="counter" data-target="<?= $monthlyIncome ?>"><?= number_format($monthlyIncome, 0, '', ' ') ?></span></div>
            <div class="stat-label">Oylik daromad (UZS)</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 animate-in">
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value"><span class="counter" data-target="<?= $totalGroups ?>"><?= $totalGroups ?></span></div>
            <div class="stat-label">Faol guruhlar</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 animate-in">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="stat-value"><span class="counter" data-target="<?= $totalLeads ?>"><?= $totalLeads ?></span></div>
            <div class="stat-label">Yangi lidlar</div>
        </div>
    </div>
</div>

<!-- Charts & Tables -->
<div class="row g-4 mb-4">
    <!-- Income Chart -->
    <div class="col-lg-8 animate-in">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i>Daromad va Xarajatlar</h5>
            </div>
            <div class="card-body">
                <canvas id="incomeChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-lg-4 animate-in">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i>Kurslar bo'yicha</h5>
            </div>
            <div class="card-body">
                <canvas id="coursesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Payments -->
    <div class="col-lg-6 animate-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-money-bill-wave me-2"></i>So'nggi to'lovlar</h5>
                <a href="payments.php" class="btn btn-sm btn-primary-gradient">Barchasi</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>O'quvchi</th>
                                <th>Summa</th>
                                <th>Status</th>
                                <th>Sana</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPayments as $payment): ?>
                            <tr>
                                <td><strong><?= clean($payment['student_name']) ?></strong></td>
                                <td><?= formatMoney($payment['amount']) ?></td>
                                <td><?= statusBadge($payment['status']) ?></td>
                                <td><?= formatDate($payment['paid_at'], 'd.m.Y H:i') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentPayments)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">To'lovlar yo'q</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Leads -->
    <div class="col-lg-6 animate-in">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bullhorn me-2"></i>Yangi lidlar</h5>
                <a href="leads.php" class="btn btn-sm btn-primary-gradient">Barchasi</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ism</th>
                                <th>Telefon</th>
                                <th>Manba</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLeads as $lead): ?>
                            <tr>
                                <td><strong><?= clean($lead['full_name']) ?></strong></td>
                                <td><?= clean($lead['phone']) ?></td>
                                <td><span class="badge bg-secondary"><?= $lead['source'] ?></span></td>
                                <td><?= statusBadge($lead['status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentLeads)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Lidlar yo'q</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$extraJs = '
<script>
// Income Chart
const incomeCtx = document.getElementById("incomeChart").getContext("2d");
new Chart(incomeCtx, {
    type: "line",
    data: {
        labels: ["Yan", "Fev", "Mar", "Apr", "May", "Iyn", "Iyl", "Avg", "Sen", "Okt", "Noy", "Dek"],
        datasets: [{
            label: "Daromad",
            data: [12, 19, 15, 25, 22, 30, 28, 35, 40, 38, 42, 45],
            borderColor: "#6366f1",
            backgroundColor: "rgba(99,102,241,0.1)",
            fill: true,
            tension: 0.4,
            borderWidth: 3
        }, {
            label: "Xarajat",
            data: [8, 12, 10, 15, 13, 18, 16, 20, 22, 19, 24, 26],
            borderColor: "#ef4444",
            backgroundColor: "rgba(239,68,68,0.05)",
            fill: true,
            tension: 0.4,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: "top" } },
        scales: { y: { beginAtZero: true } }
    }
});

// Courses Chart
const coursesCtx = document.getElementById("coursesChart").getContext("2d");
new Chart(coursesCtx, {
    type: "doughnut",
    data: {
        labels: ["IT", "Ingliz tili", "Matematika", "Dizayn"],
        datasets: [{
            data: [40, 25, 20, 15],
            backgroundColor: ["#6366f1", "#10b981", "#f59e0b", "#06b6d4"],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: "bottom" } },
        cutout: "65%"
    }
});
</script>';

include __DIR__ . '/../includes/footer.php';
?>
