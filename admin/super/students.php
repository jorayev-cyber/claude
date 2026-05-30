<?php
$pageTitle = "O'quvchilar";
$currentPage = 'students';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

// O'quvchi qo'shish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $data = [
            'full_name' => clean($_POST['full_name']),
            'phone' => clean($_POST['phone']),
            'phone2' => clean($_POST['phone2'] ?? ''),
            'email' => clean($_POST['email'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?: null,
            'gender' => $_POST['gender'] ?? null,
            'address' => clean($_POST['address'] ?? ''),
            'source' => $_POST['source'] ?? 'other',
            'notes' => clean($_POST['notes'] ?? ''),
            'status' => 'active'
        ];
        $db->insert('students', $data);
        logActivity($_SESSION['user_id'], 'student_add', "Yangi o'quvchi: " . $data['full_name']);
        setFlash('success', "O'quvchi muvaffaqiyatli qo'shildi");
        redirect('students.php');
    }
    
    if ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $data = [
            'full_name' => clean($_POST['full_name']),
            'phone' => clean($_POST['phone']),
            'phone2' => clean($_POST['phone2'] ?? ''),
            'email' => clean($_POST['email'] ?? ''),
            'birth_date' => $_POST['birth_date'] ?: null,
            'gender' => $_POST['gender'] ?? null,
            'address' => clean($_POST['address'] ?? ''),
            'source' => $_POST['source'] ?? 'other',
            'notes' => clean($_POST['notes'] ?? ''),
            'status' => $_POST['status']
        ];
        $db->update('students', $data, 'id = ?', [$id]);
        setFlash('success', "O'quvchi ma'lumotlari yangilandi");
        redirect('students.php');
    }
}

// O'chirish
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->delete('students', 'id = ?', [$id]);
    setFlash('success', "O'quvchi o'chirildi");
    redirect('students.php');
}

// O'quvchilar ro'yxati
$students = $db->fetchAll("SELECT s.*, 
    (SELECT COUNT(*) FROM group_students gs WHERE gs.student_id = s.id AND gs.status = 'active') as group_count
    FROM students s ORDER BY s.created_at DESC");

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="text-muted">Jami: <strong><?= count($students) ?></strong> ta o'quvchi</span>
    </div>
    <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        <i class="fas fa-plus me-2"></i>Yangi o'quvchi
    </button>
</div>

<!-- Students Table -->
<div class="card animate-in">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ism</th>
                        <th>Telefon</th>
                        <th>Guruhlar</th>
                        <th>Manba</th>
                        <th>Status</th>
                        <th>Qo'shilgan</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $i => $student): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar" style="width:32px;height:32px;border-radius:8px;font-size:12px;">
                                    <span><?= strtoupper(substr($student['full_name'], 0, 1)) ?></span>
                                </div>
                                <strong><?= clean($student['full_name']) ?></strong>
                            </div>
                        </td>
                        <td><?= clean($student['phone']) ?></td>
                        <td><span class="badge bg-primary"><?= $student['group_count'] ?> ta</span></td>
                        <td><span class="badge bg-secondary"><?= $student['source'] ?></span></td>
                        <td><?= statusBadge($student['status']) ?></td>
                        <td><?= formatDate($student['created_at']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm" onclick='editStudent(<?= json_encode($student) ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete('students.php?delete=<?= $student['id'] ?>', '<?= clean($student['full_name']) ?>')">
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

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Yangi o'quvchi qo'shish</h5>
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
                            <label class="form-label">Telefon *</label>
                            <input type="text" name="phone" class="form-control" placeholder="+998" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qo'shimcha telefon</label>
                            <input type="text" name="phone2" class="form-control" placeholder="+998">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tug'ilgan sana</label>
                            <input type="date" name="birth_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jinsi</label>
                            <select name="gender" class="form-select">
                                <option value="">Tanlang</option>
                                <option value="male">Erkak</option>
                                <option value="female">Ayol</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Manba</label>
                            <select name="source" class="form-select">
                                <option value="website">Web sayt</option>
                                <option value="instagram">Instagram</option>
                                <option value="telegram">Telegram</option>
                                <option value="friend">Do'st orqali</option>
                                <option value="other">Boshqa</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Manzil</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Izoh</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary-gradient">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>O'quvchini tahrirlash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">To'liq ism *</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefon *</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qo'shimcha telefon</label>
                            <input type="text" name="phone2" id="edit_phone2" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tug'ilgan sana</label>
                            <input type="date" name="birth_date" id="edit_birth_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jinsi</label>
                            <select name="gender" id="edit_gender" class="form-select">
                                <option value="">Tanlang</option>
                                <option value="male">Erkak</option>
                                <option value="female">Ayol</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Faol</option>
                                <option value="graduated">Bitirgan</option>
                                <option value="dropped">Ketgan</option>
                                <option value="frozen">Muzlatilgan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Manba</label>
                            <select name="source" id="edit_source" class="form-select">
                                <option value="website">Web sayt</option>
                                <option value="instagram">Instagram</option>
                                <option value="telegram">Telegram</option>
                                <option value="friend">Do'st orqali</option>
                                <option value="other">Boshqa</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Manzil</label>
                            <input type="text" name="address" id="edit_address" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Izoh</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary-gradient">
                        <i class="fas fa-save me-2"></i>Yangilash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extraJs = '<script>
function editStudent(s) {
    document.getElementById("edit_id").value = s.id;
    document.getElementById("edit_full_name").value = s.full_name;
    document.getElementById("edit_phone").value = s.phone;
    document.getElementById("edit_phone2").value = s.phone2 || "";
    document.getElementById("edit_email").value = s.email || "";
    document.getElementById("edit_birth_date").value = s.birth_date || "";
    document.getElementById("edit_gender").value = s.gender || "";
    document.getElementById("edit_source").value = s.source || "other";
    document.getElementById("edit_address").value = s.address || "";
    document.getElementById("edit_notes").value = s.notes || "";
    document.getElementById("edit_status").value = s.status;
    new bootstrap.Modal(document.getElementById("editStudentModal")).show();
}
</script>';

include __DIR__ . '/../includes/footer.php';
?>
