<?php
$pageTitle = 'Lidlar';
$currentPage = 'leads';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $data = [
            'full_name' => clean($_POST['full_name']),
            'phone' => clean($_POST['phone']),
            'email' => clean($_POST['email'] ?? ''),
            'course_interest' => $_POST['course_interest'] ?: null,
            'source' => $_POST['source'] ?? 'website',
            'assigned_to' => $_POST['assigned_to'] ?: null,
            'notes' => clean($_POST['notes'] ?? ''),
            'status' => 'new'
        ];
        $db->insert('leads', $data);
        setFlash('success', 'Lid muvaffaqiyatli qo\'shildi');
        redirect('leads.php');
    }
    if ($_POST['action'] === 'update_status') {
        $db->update('leads', ['status' => $_POST['status']], 'id = ?', [(int)$_POST['id']]);
        setFlash('success', 'Status yangilandi');
        redirect('leads.php');
    }
}

if (isset($_GET['delete'])) {
    $db->delete('leads', 'id = ?', [(int)$_GET['delete']]);
    setFlash('success', 'Lid o\'chirildi');
    redirect('leads.php');
}

$leads = $db->fetchAll("SELECT l.*, c.name as course_name, u.full_name as assigned_name
    FROM leads l 
    LEFT JOIN courses c ON l.course_interest = c.id
    LEFT JOIN users u ON l.assigned_to = u.id
    ORDER BY l.created_at DESC");

$courses = $db->fetchAll("SELECT id, name FROM courses WHERE status = 'active'");
$managers = $db->fetchAll("SELECT id, full_name FROM users WHERE role IN ('super_admin','manager') AND status = 'active'");

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span class="text-muted">Jami: <strong><?= count($leads) ?></strong> ta lid</span>
    <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#addLeadModal">
        <i class="fas fa-plus me-2"></i>Yangi lid
    </button>
</div>

<div class="card animate-in">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Ism</th>
                        <th>Telefon</th>
                        <th>Kurs</th>
                        <th>Manba</th>
                        <th>Mas'ul</th>
                        <th>Status</th>
                        <th>Sana</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><strong><?= clean($lead['full_name']) ?></strong></td>
                        <td><?= clean($lead['phone']) ?></td>
                        <td><?= clean($lead['course_name'] ?? '-') ?></td>
                        <td><span class="badge bg-secondary"><?= $lead['source'] ?></span></td>
                        <td><?= clean($lead['assigned_name'] ?? '-') ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id" value="<?= $lead['id'] ?>">
                                <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                                    <option value="new" <?= $lead['status']=='new'?'selected':'' ?>>Yangi</option>
                                    <option value="contacted" <?= $lead['status']=='contacted'?'selected':'' ?>>Bog'lanilgan</option>
                                    <option value="interested" <?= $lead['status']=='interested'?'selected':'' ?>>Qiziqgan</option>
                                    <option value="enrolled" <?= $lead['status']=='enrolled'?'selected':'' ?>>Ro'yxatdan o'tgan</option>
                                    <option value="lost" <?= $lead['status']=='lost'?'selected':'' ?>>Yo'qotilgan</option>
                                </select>
                            </form>
                        </td>
                        <td><?= formatDate($lead['created_at']) ?></td>
                        <td>
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete('leads.php?delete=<?= $lead['id'] ?>', '<?= clean($lead['full_name']) ?>')">
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

<!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-bullhorn me-2"></i>Yangi lid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ism *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefon *</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kurs</label>
                            <select name="course_interest" class="form-select">
                                <option value="">Tanlang</option>
                                <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= clean($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Manba</label>
                            <select name="source" class="form-select">
                                <option value="website">Web sayt</option>
                                <option value="instagram">Instagram</option>
                                <option value="telegram">Telegram</option>
                                <option value="facebook">Facebook</option>
                                <option value="friend">Do'st</option>
                                <option value="call">Qo'ng'iroq</option>
                                <option value="other">Boshqa</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mas'ul</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Tanlang</option>
                                <?php foreach ($managers as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= clean($m['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Izoh</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
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
