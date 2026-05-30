<?php
$pageTitle = 'Hisobotlar';
$currentPage = 'reports';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

// Hisobot ma'lumotlari
$monthlyIncome = $db->fetchAll("SELECT MONTH(paid_at) as month, SUM(amount) as total 
    FROM payments WHERE YEAR(paid_at) = YEAR(NOW()) AND status = 'paid' 
    GROUP BY MONTH(paid_at) ORDER BY month");

$monthlyExpenses = $db->fetchAll("SELECT MONTH(expense_date) as month, SUM(amount) as total 
    FROM expenses WHERE YEAR(expense_date) = YEAR(NOW()) 
    GROUP BY MONTH(expense_date) ORDER BY month");

$studentsBySource = $db->fetchAll("SELECT source, COUNT(*) as count FROM students GROUP BY source");
$leadsByStatus = $db->fetchAll("SELECT status, COUNT(*) as count FROM leads GROUP BY status");

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="row g-4 mb-4">
    <div class="col-lg-8 animate-in">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i>Yillik moliyaviy hisobot</h5>
            </div>
            <div class="card-body">
                <canvas id="financialChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 animate-in">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i>O'quvchi manbalari</h5>
            </div>
            <div class="card-body">
                <canvas id="sourcesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6 animate-in">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-funnel-dollar me-2"></i>Lidlar bo'yicha</h5></div>
            <div class="card-body">
                <canvas id="leadsChart" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 animate-in">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-info-circle me-2"></i>Umumiy statistika</h5></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Jami o'quvchilar</span>
                        <strong><?= $db->count('students') ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Faol guruhlar</span>
                        <strong><?= $db->count('groups_table', "status='active'") ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Kurslar soni</span>
                        <strong><?= $db->count('courses') ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>O'qituvchilar</span>
                        <strong><?= $db->count('users', "role='teacher'") ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Yillik daromad</span>
                        <strong class="text-success"><?= formatMoney(array_sum(array_column($monthlyIncome, 'total'))) ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$incomeData = json_encode(array_column($monthlyIncome, 'total'));
$expenseData = json_encode(array_column($monthlyExpenses, 'total'));
$sourceLabels = json_encode(array_column($studentsBySource, 'source'));
$sourceCounts = json_encode(array_column($studentsBySource, 'count'));
$leadLabels = json_encode(array_column($leadsByStatus, 'status'));
$leadCounts = json_encode(array_column($leadsByStatus, 'count'));

$extraJs = "<script>
new Chart(document.getElementById('financialChart'), {
    type: 'bar',
    data: {
        labels: ['Yan','Fev','Mar','Apr','May','Iyn','Iyl','Avg','Sen','Okt','Noy','Dek'],
        datasets: [{label:'Daromad',data:{$incomeData},backgroundColor:'rgba(99,102,241,0.7)',borderRadius:6},
                   {label:'Xarajat',data:{$expenseData},backgroundColor:'rgba(239,68,68,0.7)',borderRadius:6}]
    },
    options: {responsive:true, scales:{y:{beginAtZero:true}}}
});

new Chart(document.getElementById('sourcesChart'), {
    type: 'doughnut',
    data: {labels:{$sourceLabels},datasets:[{data:{$sourceCounts},backgroundColor:['#6366f1','#10b981','#f59e0b','#06b6d4','#ef4444'],borderWidth:0}]},
    options: {cutout:'60%',plugins:{legend:{position:'bottom'}}}
});

new Chart(document.getElementById('leadsChart'), {
    type: 'bar',
    data: {labels:{$leadLabels},datasets:[{label:'Lidlar',data:{$leadCounts},backgroundColor:['#6366f1','#10b981','#f59e0b','#06b6d4','#ef4444'],borderRadius:8}]},
    options: {indexAxis:'y',plugins:{legend:{display:false}}}
});
</script>";
include __DIR__ . '/../includes/footer.php';
?>
