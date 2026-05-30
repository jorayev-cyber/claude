<?php
$pageTitle = 'Guruhlar';
$currentPage = 'groups';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

// Guruh qo'shish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $data = [
            'name' => clean($_POST['name']),
            'course_id' => (int)$_POST['course_id'],
            'teacher_id' => (int)$_POST['teacher_id'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'] ?: null,
            'schedule' => clean($_POST['schedule'] ?? ''),
            'time_slot' => clean($_POST['time_slot'] ?? ''),
            'room' => clean($_POST['room'] ?? ''),
            'max_students' => (int)$_POST['max_students'],
            'status' => 'active'
        ];
        $db->insert('groups_table', $data);
        setFlash('success', 'Guruh muvaffaqiyatli yaratildi');
        redirect('groups.php');
    }
}

if (isset($_GET['delete'])) {
    $db->delete('groups_table', 'id = ?', [(int)$_GET['delete']]);
    setFlash('success', 'Guruh o\'chirildi');
    redirect('groups.php');
}

// Ma'lumotlarni olish
$groups = $db->fetchAll("SELECT g.*, c.name as course_name, u.full_name as teacher_name,
    (SELECT COUNT(*) FROM group_students gs WHERE gs.group_id = g.id AND gs.status = 'active') as student_count
    FROM groups_table g 
    JOIN courses c ON g.course_id = c.id
    JOIN users u ON g.teacher_id = u.id
    ORDER BY g.created_at DESC");

$courses = $db->fetchAll("SELECT id, name FROM courses WHERE status = 'active'");
$teachers = $db->fetchAll("SELECT id, full_name FROM users WHERE role = 'teacher' AND status = 'active'");

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span class="text-muted">Jami: <strong><?= count($groups) ?></strong> ta guruh</span>
    <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#addGroupModal">
        <i class="fas fa-plus me-2"></i>Yangi guruh
    </button>
</div>

<div class="card animate-in">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Guruh</th>
                        <th>Kurs</th>
                        <th>O'qituvchi</th>
                        <th>Jadval</th>
                        <th>O'quvchilar</th>
                        <th>Boshlanish</th>
                        <th>Status</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $group): ?>
                    <tr>
                        <td><strong><?= clean($group['name']) ?></strong></td>
                        <td><?= clean($group['course_name']) ?></td>
                        <td><?= clean($group['teacher_name']) ?></td>
                        <td>
                            <small><?= clean($group['schedule']) ?></small><br>
                            <small class="text-muted"><?= clean($group['time_slot']) ?></small>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?= $group['student_count'] ?>/<?= $group['max_students'] ?></span>
                        </td>
                        <td><?= formatDate($group['start_date']) ?></td>
                        <td><?= statusBadge($group['status']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="group_view.php?id=<?= $group['id'] ?>" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                                <button class="btn btn-outline-danger" onclick="confirmDelete('groups.php?delete=<?= $group['id'] ?>', '<?= clean($group['name']) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Group Modal -->
<div class="modal fade" id="addGroupModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users me-2"></i>Yangi guruh yaratish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Guruh nomi *</label>
                            <input type="text" name="name" class="form-control" placeholder="N-1, Web-3..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kurs *</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">Kursni tanlang</option>
                                <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= clean($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">O'qituvchi *</label>
                            <select name="teacher_id" class="form-select" required>
                                <option value="">O'qituvchini tanlang</option>
                                <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= clean($t['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Xona</label>
                            <input type="text" name="room" class="form-control" placeholder="A-101">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Boshlanish sanasi *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tugash sanasi</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Max o'quvchilar</label>
                            <input type="number" name="max_students" class="form-control" value="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kunlar</label>
                            <input type="text" name="schedule" class="form-control" placeholder="Du, Chor, Ju">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vaqt</label>
                            <input type="text" name="time_slot" class="form-control" placeholder="09:00 - 11:00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary-gradient"><i class="fas fa-save me-2"></i>Yaratish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
