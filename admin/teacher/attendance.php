<?php
$pageTitle = 'Davomat';
$currentPage = 'attendance';
require_once __DIR__ . '/../includes/header.php';
requireRole(['teacher']);

$teacherId = $_SESSION['user_id'];
$groupId = $_GET['group_id'] ?? null;
$date = $_GET['date'] ?? date('Y-m-d');

// Davomat belgilash
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    foreach ($_POST['attendance'] as $studentId => $status) {
        $existing = $db->fetch("SELECT id FROM attendance WHERE group_id = ? AND student_id = ? AND date = ?", 
            [(int)$_POST['group_id'], (int)$studentId, $_POST['date']]);
        
        if ($existing) {
            $db->update('attendance', ['status' => $status, 'marked_by' => $teacherId], 'id = ?', [$existing['id']]);
        } else {
            $db->insert('attendance', [
                'group_id' => (int)$_POST['group_id'],
                'student_id' => (int)$studentId,
                'date' => $_POST['date'],
                'status' => $status,
                'marked_by' => $teacherId
            ]);
        }
    }
    setFlash('success', 'Davomat saqlandi');
    redirect("attendance.php?group_id={$_POST['group_id']}&date={$_POST['date']}");
}

// Faqat o'z guruhlari
$groups = $db->fetchAll("SELECT g.*, c.name as course_name FROM groups_table g JOIN courses c ON g.course_id = c.id WHERE g.teacher_id = ? AND g.status = 'active'", [$teacherId]);

$students = [];
$attendanceMap = [];
if ($groupId) {
    // Tekshirish - bu guruh o'qituvchiga tegishlimi
    $check = $db->fetch("SELECT id FROM groups_table WHERE id = ? AND teacher_id = ?", [(int)$groupId, $teacherId]);
    if ($check) {
        $students = $db->fetchAll("SELECT s.* FROM students s JOIN group_students gs ON gs.student_id = s.id WHERE gs.group_id = ? AND gs.status = 'active' ORDER BY s.full_name", [(int)$groupId]);
        $attendanceData = $db->fetchAll("SELECT * FROM attendance WHERE group_id = ? AND date = ?", [(int)$groupId, $date]);
        $attendanceMap = array_column($attendanceData, 'status', 'student_id');
    }
}

include __DIR__ . '/../includes/sidebar_teacher.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="card mb-4 animate-in">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Guruh</label>
                <select name="group_id" class="form-select" required>
                    <option value="">Guruhni tanlang</option>
                    <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $groupId==$g['id']?'selected':'' ?>>
                        <?= clean($g['name']) ?> - <?= clean($g['course_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sana</label>
                <input type="date" name="date" class="form-control" value="<?= $date ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary-gradient w-100">
                    <i class="fas fa-search me-2"></i>Ko'rsatish
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($groupId && !empty($students)): ?>
<div class="card animate-in">
    <div class="card-header">
        <h5><i class="fas fa-clipboard-check me-2"></i>Davomat - <?= formatDate($date) ?></h5>
    </div>
    <div class="card-body p-0">
        <form method="POST">
            <input type="hidden" name="group_id" value="<?= $groupId ?>">
            <input type="hidden" name="date" value="<?= $date ?>">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>O'quvchi</th>
                            <th class="text-center">✅ Keldi</th>
                            <th class="text-center">❌ Kelmadi</th>
                            <th class="text-center">⏰ Kechikdi</th>
                            <th class="text-center">📋 Sababli</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $i => $s): 
                            $st = $attendanceMap[$s['id']] ?? 'present';
                        ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><strong><?= clean($s['full_name']) ?></strong></td>
                            <td class="text-center"><input type="radio" name="attendance[<?= $s['id'] ?>]" value="present" <?= $st=='present'?'checked':'' ?> class="form-check-input"></td>
                            <td class="text-center"><input type="radio" name="attendance[<?= $s['id'] ?>]" value="absent" <?= $st=='absent'?'checked':'' ?> class="form-check-input"></td>
                            <td class="text-center"><input type="radio" name="attendance[<?= $s['id'] ?>]" value="late" <?= $st=='late'?'checked':'' ?> class="form-check-input"></td>
                            <td class="text-center"><input type="radio" name="attendance[<?= $s['id'] ?>]" value="excused" <?= $st=='excused'?'checked':'' ?> class="form-check-input"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 text-end">
                <button type="submit" class="btn btn-primary-gradient"><i class="fas fa-save me-2"></i>Saqlash</button>
            </div>
        </form>
    </div>
</div>
<?php elseif ($groupId): ?>
<div class="alert alert-info animate-in">Bu guruhda o'quvchilar yo'q</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
