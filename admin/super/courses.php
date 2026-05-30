<?php
$pageTitle = 'Kurslar';
$currentPage = 'courses';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

// Kurs qo'shish/tahrirlash
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $data = [
            'name' => clean($_POST['name']),
            'slug' => createSlug($_POST['name']),
            'description' => clean($_POST['description'] ?? ''),
            'duration_months' => (int)$_POST['duration_months'],
            'price' => (float)$_POST['price'],
            'discount_price' => $_POST['discount_price'] ? (float)$_POST['discount_price'] : null,
            'category' => clean($_POST['category'] ?? ''),
            'level' => $_POST['level'] ?? 'beginner',
            'status' => 'active'
        ];
        $db->insert('courses', $data);
        setFlash('success', 'Kurs muvaffaqiyatli qo\'shildi');
        redirect('courses.php');
    }
    if ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $data = [
            'name' => clean($_POST['name']),
            'slug' => createSlug($_POST['name']),
            'description' => clean($_POST['description'] ?? ''),
            'duration_months' => (int)$_POST['duration_months'],
            'price' => (float)$_POST['price'],
            'discount_price' => $_POST['discount_price'] ? (float)$_POST['discount_price'] : null,
            'category' => clean($_POST['category'] ?? ''),
            'level' => $_POST['level'] ?? 'beginner',
            'status' => $_POST['status']
        ];
        $db->update('courses', $data, 'id = ?', [$id]);
        setFlash('success', 'Kurs yangilandi');
        redirect('courses.php');
    }
}

if (isset($_GET['delete'])) {
    $db->delete('courses', 'id = ?', [(int)$_GET['delete']]);
    setFlash('success', 'Kurs o\'chirildi');
    redirect('courses.php');
}

$courses = $db->fetchAll("SELECT c.*, 
    (SELECT COUNT(*) FROM groups_table g WHERE g.course_id = c.id AND g.status = 'active') as group_count
    FROM courses c ORDER BY c.created_at DESC");

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <span class="text-muted">Jami: <strong><?= count($courses) ?></strong> ta kurs</span>
    <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="fas fa-plus me-2"></i>Yangi kurs
    </button>
</div>

<!-- Courses Grid -->
<div class="row g-4">
    <?php foreach ($courses as $course): ?>
    <div class="col-lg-4 col-md-6 animate-in">
        <div class="card hover-lift h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon purple" style="width:42px;height:42px;font-size:16px;">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <?= statusBadge($course['status']) ?>
                </div>
                <h5 class="mb-2"><?= clean($course['name']) ?></h5>
                <p class="text-muted small mb-3"><?= clean(substr($course['description'], 0, 80)) ?>...</p>
                <div class="d-flex gap-3 mb-3">
                    <small class="text-muted"><i class="fas fa-clock me-1"></i><?= $course['duration_months'] ?> oy</small>
                    <small class="text-muted"><i class="fas fa-users me-1"></i><?= $course['group_count'] ?> guruh</small>
                    <small class="text-muted"><i class="fas fa-signal me-1"></i><?= ucfirst($course['level']) ?></small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <?php if ($course['discount_price']): ?>
                            <span class="text-decoration-line-through text-muted small"><?= formatMoney($course['price']) ?></span><br>
                            <strong class="text-success"><?= formatMoney($course['discount_price']) ?></strong>
                        <?php else: ?>
                            <strong><?= formatMoney($course['price']) ?></strong>
                        <?php endif; ?>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick='editCourse(<?= json_encode($course) ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="confirmDelete('courses.php?delete=<?= $course['id'] ?>', '<?= clean($course['name']) ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Yangi kurs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Kurs nomi *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Davomiyligi (oy)</label>
                            <input type="number" name="duration_months" class="form-control" value="3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategoriya</label>
                            <input type="text" name="category" class="form-control" placeholder="IT, Til, ...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Narx *</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chegirma narx</label>
                            <input type="number" name="discount_price" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Daraja</label>
                            <select name="level" class="form-select">
                                <option value="beginner">Boshlang'ich</option>
                                <option value="intermediate">O'rta</option>
                                <option value="advanced">Yuqori</option>
                            </select>
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

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Kursni tahrirlash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="ec_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Kurs nomi *</label>
                            <input type="text" name="name" id="ec_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" id="ec_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Davomiyligi (oy)</label>
                            <input type="number" name="duration_months" id="ec_duration" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategoriya</label>
                            <input type="text" name="category" id="ec_category" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Narx *</label>
                            <input type="number" name="price" id="ec_price" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chegirma narx</label>
                            <input type="number" name="discount_price" id="ec_discount" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" id="ec_status" class="form-select">
                                <option value="active">Faol</option>
                                <option value="inactive">Nofaol</option>
                                <option value="archived">Arxiv</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daraja</label>
                            <select name="level" id="ec_level" class="form-select">
                                <option value="beginner">Boshlang'ich</option>
                                <option value="intermediate">O'rta</option>
                                <option value="advanced">Yuqori</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary-gradient"><i class="fas fa-save me-2"></i>Yangilash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extraJs = '<script>
function editCourse(c) {
    document.getElementById("ec_id").value = c.id;
    document.getElementById("ec_name").value = c.name;
    document.getElementById("ec_description").value = c.description || "";
    document.getElementById("ec_duration").value = c.duration_months;
    document.getElementById("ec_category").value = c.category || "";
    document.getElementById("ec_price").value = c.price;
    document.getElementById("ec_discount").value = c.discount_price || "";
    document.getElementById("ec_status").value = c.status;
    document.getElementById("ec_level").value = c.level;
    new bootstrap.Modal(document.getElementById("editCourseModal")).show();
}
</script>';
include __DIR__ . '/../includes/footer.php';
?>
