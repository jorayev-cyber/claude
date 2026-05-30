<?php
$pageTitle = 'Xodimlar';
$currentPage = 'users';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $result = createUser([
            'full_name' => clean($_POST['full_name']),
            'email' => clean($_POST['email']),
            'phone' => clean($_POST['phone'] ?? ''),
            'password' => $_POST['password'],
            'role' => $_POST['role'],
            'status' => 'active'
        ]);
        if (isset($result['success'])) {
            setFlash('success', 'Xodim muvaffaqiyatli qo\'shildi');
        } else {
            setFlash('danger', $result['error']);
        }
        redirect('users.php');
    }
}

if (isset($_GET['delete']) && $_GET['delete'] != $_SESSION['user_id']) {
    $db->delete('users', 'id = ?', [(int)$_GET['delete']]);
    setFlash('success', 'Xodim o\'chirildi');
    redirect('users.php');
}

if (isset($_GET['toggle'])) {
    $user = $db->fetch("SELECT status FROM users WHERE id = ?", [(int)$_GET['toggle']]);
    $newStatus = $user['status'] === 'active' ? 'blocked' : 'active';
    $db->update('users', ['status' => $newStatus], 'id = ?', [(int)$_GET['toggle']]);
    setFlash('success', 'Status yangilandi');
    redirect('users.php');
}

$users = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span class="text-muted">Jami: <strong><?= count($users) ?></strong> ta xodim</span>
    <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus me-2"></i>Yangi xodim
    </button>
</div>

<div class="card animate-in">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Xodim</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Lavozim</th>
                        <th>Status</th>
                        <th>Oxirgi kirish</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar" style="width:32px;height:32px;border-radius:8px;font-size:12px;">
                                    <span><?= strtoupper(substr($user['full_name'], 0, 1)) ?></span>
                                </div>
                                <strong><?= clean($user['full_name']) ?></strong>
                            </div>
                        </td>
                        <td><?= clean($user['email']) ?></td>
                        <td><?= clean($user['phone']) ?></td>
                        <td>
                            <span class="badge bg-<?= $user['role']=='super_admin'?'danger':($user['role']=='manager'?'warning':'info') ?>">
                                <?= ucfirst(str_replace('_', ' ', $user['role'])) ?>
                            </span>
                        </td>
                        <td><?= statusBadge($user['status']) ?></td>
                        <td><?= $user['last_login'] ? formatDate($user['last_login'], 'd.m.Y H:i') : '-' ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <div class="btn-group btn-group-sm">
                                <a href="users.php?toggle=<?= $user['id'] ?>" class="btn btn-outline-warning" title="<?= $user['status']=='active'?'Bloklash':'Faollashtirish' ?>">
                                    <i class="fas fa-<?= $user['status']=='active'?'ban':'check' ?>"></i>
                                </a>
                                <button class="btn btn-outline-danger" onclick="confirmDelete('users.php?delete=<?= $user['id'] ?>', '<?= clean($user['full_name']) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php else: ?>
                            <span class="text-muted small">Siz</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Yangi xodim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">To'liq ism *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefon</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lavozim *</label>
                            <select name="role" class="form-select" required>
                                <option value="teacher">O'qituvchi</option>
                                <option value="manager">Manager</option>
                                <option value="receptionist">Resepshion</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Parol *</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
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
