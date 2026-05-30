<?php
$pageTitle = 'Xarajatlar';
$currentPage = 'expenses';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $db->insert('expenses', [
        'title' => clean($_POST['title']),
        'amount' => (float)$_POST['amount'],
        'category' => $_POST['category'],
        'description' => clean($_POST['description'] ?? ''),
        'expense_date' => $_POST['expense_date'],
        'created_by' => $_SESSION['user_id']
    ]);
    setFlash('success', 'Xarajat qo\'shildi');
    redirect('expenses.php');
}

if (isset($_GET['delete'])) {
    $db->delete('expenses', 'id = ?', [(int)$_GET['delete']]);
    setFlash('success', 'Xarajat o\'chirildi');
    redirect('expenses.php');
}

$expenses = $db->fetchAll("SELECT e.*, u.full_name as created_name FROM expenses e LEFT JOIN users u ON e.created_by = u.id ORDER BY e.expense_date DESC");
$totalExpenses = $db->fetch("SELECT COALESCE(SUM(amount),0) as t FROM expenses WHERE MONTH(expense_date) = MONTH(NOW())")['t'];

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="row g-4 mb-4">
    <div class="col-md-4 animate-in">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-receipt"></i></div>
            <div class="stat-value"><?= formatMoney($totalExpenses) ?></div>
            <div class="stat-label">Bu oylik xarajatlar</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span></span>
    <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
        <i class="fas fa-plus me-2"></i>Xarajat qo'shish
    </button>
</div>

<div class="card animate-in">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Sarlavha</th>
                        <th>Summa</th>
                        <th>Kategoriya</th>
                        <th>Sana</th>
                        <th>Kiritdi</th>
                        <th>Amal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $e): ?>
                    <tr>
                        <td><strong><?= clean($e['title']) ?></strong></td>
                        <td class="text-danger"><strong><?= formatMoney($e['amount']) ?></strong></td>
                        <td><span class="badge bg-secondary"><?= $e['category'] ?></span></td>
                        <td><?= formatDate($e['expense_date']) ?></td>
                        <td><?= clean($e['created_name'] ?? '-') ?></td>
                        <td>
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete('expenses.php?delete=<?= $e['id'] ?>', '<?= clean($e['title']) ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Xarajat qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Sarlavha *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Summa *</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategoriya</label>
                            <select name="category" class="form-select">
                                <option value="salary">Ish haqi</option>
                                <option value="rent">Ijara</option>
                                <option value="utilities">Kommunal</option>
                                <option value="supplies">Jihozlar</option>
                                <option value="marketing">Marketing</option>
                                <option value="other">Boshqa</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sana *</label>
                            <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
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
