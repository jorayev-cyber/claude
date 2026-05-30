<?php
$pageTitle = "To'lovlar";
$currentPage = 'payments';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

// To'lov qo'shish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'student_id' => (int)$_POST['student_id'],
        'group_id' => (int)$_POST['group_id'],
        'amount' => (float)$_POST['amount'],
        'payment_type' => $_POST['payment_type'],
        'payment_for' => clean($_POST['payment_for'] ?? ''),
        'month_for' => $_POST['month_for'] ?: null,
        'discount' => (float)($_POST['discount'] ?? 0),
        'note' => clean($_POST['note'] ?? ''),
        'received_by' => $_SESSION['user_id'],
        'status' => 'paid'
    ];
    $db->insert('payments', $data);
    setFlash('success', "To'lov muvaffaqiyatli qo'shildi");
    redirect('payments.php');
}

$payments = $db->fetchAll("SELECT p.*, s.full_name as student_name, g.name as group_name, u.full_name as received_name
    FROM payments p 
    JOIN students s ON p.student_id = s.id 
    JOIN groups_table g ON p.group_id = g.id
    LEFT JOIN users u ON p.received_by = u.id
    ORDER BY p.created_at DESC LIMIT 200");

$students = $db->fetchAll("SELECT id, full_name FROM students WHERE status = 'active' ORDER BY full_name");
$groups = $db->fetchAll("SELECT id, name FROM groups_table WHERE status = 'active'");

// Statistika
$totalPaid = $db->fetch("SELECT COALESCE(SUM(amount),0) as t FROM payments WHERE status = 'paid' AND MONTH(paid_at) = MONTH(NOW())")['t'];
$totalPending = $db->fetch("SELECT COALESCE(SUM(amount),0) as t FROM payments WHERE status = 'pending'")['t'];

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4 animate-in">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value"><?= formatMoney($totalPaid) ?></div>
            <div class="stat-label">Bu oyda to'langan</div>
        </div>
    </div>
    <div class="col-md-4 animate-in">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
            <div class="stat-value"><?= formatMoney($totalPending) ?></div>
            <div class="stat-label">Kutilmoqda</div>
        </div>
    </div>
    <div class="col-md-4 animate-in">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-receipt"></i></div>
            <div class="stat-value"><?= count($payments) ?></div>
            <div class="stat-label">Jami tranzaksiyalar</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span></span>
    <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
        <i class="fas fa-plus me-2"></i>To'lov qo'shish
    </button>
</div>

<div class="card animate-in">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>O'quvchi</th>
                        <th>Guruh</th>
                        <th>Summa</th>
                        <th>Turi</th>
                        <th>Status</th>
                        <th>Sana</th>
                        <th>Qabul qildi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= clean($p['student_name']) ?></strong></td>
                        <td><?= clean($p['group_name']) ?></td>
                        <td><strong><?= formatMoney($p['amount']) ?></strong></td>
                        <td><span class="badge bg-secondary"><?= $p['payment_type'] ?></span></td>
                        <td><?= statusBadge($p['status']) ?></td>
                        <td><?= formatDate($p['paid_at'], 'd.m.Y H:i') ?></td>
                        <td><?= clean($p['received_name'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-money-bill me-2"></i>To'lov qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">O'quvchi *</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Tanlang</option>
                                <?php foreach ($students as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= clean($s['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guruh *</label>
                            <select name="group_id" class="form-select" required>
                                <option value="">Tanlang</option>
                                <?php foreach ($groups as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= clean($g['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Summa (UZS) *</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To'lov turi</label>
                            <select name="payment_type" class="form-select">
                                <option value="cash">Naqd</option>
                                <option value="card">Karta</option>
                                <option value="transfer">O'tkazma</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qaysi oy uchun</label>
                            <input type="date" name="month_for" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chegirma</label>
                            <input type="number" name="discount" class="form-control" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Izoh</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary-gradient"><i class="fas fa-save me-2"></i>Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
