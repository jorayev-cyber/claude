<?php
$pageTitle = 'Sozlamalar';
$currentPage = 'settings';
require_once __DIR__ . '/../includes/header.php';
requireRole(['super_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        $db->query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
    }
    setFlash('success', 'Sozlamalar saqlandi');
    redirect('settings.php');
}

$settings = $db->fetchAll("SELECT * FROM settings ORDER BY setting_group, setting_key");
$grouped = [];
foreach ($settings as $s) {
    $grouped[$s['setting_group']][] = $s;
}

include __DIR__ . '/../includes/sidebar_super.php';
include __DIR__ . '/../includes/topbar.php';
?>

<form method="POST">
    <div class="card animate-in">
        <div class="card-header">
            <h5><i class="fas fa-cog me-2"></i>Tizim sozlamalari</h5>
            <button type="submit" class="btn btn-primary-gradient btn-sm">
                <i class="fas fa-save me-2"></i>Saqlash
            </button>
        </div>
        <div class="card-body">
            <?php foreach ($grouped as $group => $items): ?>
            <h6 class="text-uppercase text-muted mb-3 mt-4"><?= ucfirst($group) ?></h6>
            <div class="row g-3 mb-4">
                <?php foreach ($items as $item): ?>
                <div class="col-md-6">
                    <label class="form-label"><?= clean($item['setting_key']) ?></label>
                    <input type="text" name="settings[<?= $item['setting_key'] ?>]" 
                           value="<?= clean($item['setting_value']) ?>" class="form-control">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
