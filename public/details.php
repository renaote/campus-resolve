<?php
require '../config/database.php';
require '../includes/db-helpers.php';

$id = (int) ($_GET['id'] ?? 0);
$complaint = getComplaintById($pdo, $id);

if (!$complaint) {
    $breadcrumb = [['label' => 'Home', 'url' => 'index.php'], ['label' => 'Not found']];
    require '../includes/header.php';
    echo '<p>Complaint not found.</p>';
    require '../includes/footer.php';
    exit;
}

$statusMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    updateComplaintStatus($pdo, $id, $_POST['status']);
    $complaint = getComplaintById($pdo, $id);
    $statusMessage = 'Status updated.';
}

$breadcrumb = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Admin dashboard', 'url' => 'admin.php'],
    ['label' => $complaint['reference_number']],
];
require '../includes/header.php';
?>

<div class="page-title"><?= htmlspecialchars($complaint['title']) ?></div>
<p class="page-intro">Reference: <span class="ref-number"><?= htmlspecialchars($complaint['reference_number']) ?></span></p>

<?php if ($statusMessage): ?>
    <div class="confirmation-box"><p><?= htmlspecialchars($statusMessage) ?></p></div>
<?php endif; ?>

<div class="detail-section">
    <h3>Student submission</h3>
    <p><?= nl2br(htmlspecialchars($complaint['description'])) ?></p>
    <p>Submitted: <?= htmlspecialchars($complaint['submitted_at']) ?></p>
    <p>Anonymous: <?= $complaint['is_anonymous'] ? 'Yes' : 'No' ?></p>
    <?php if (!$complaint['is_anonymous']): ?>
        <p>Name: <?= htmlspecialchars($complaint['student_name'] ?? 'Not provided') ?></p>
        <p>Email: <?= htmlspecialchars($complaint['student_email'] ?? 'Not provided') ?></p>
    <?php endif; ?>
</div>

<div class="detail-section">
    <h3>Classification result</h3>
    <p>Category: <strong><?= htmlspecialchars($complaint['detected_category']) ?></strong></p>
    <p>Urgency score: <strong><?= htmlspecialchars($complaint['urgency_score']) ?> / 100</strong></p>
    <p>Priority: <span class="badge priority-<?= strtolower($complaint['priority']) ?>"><?= htmlspecialchars($complaint['priority']) ?></span></p>
    <p>Assigned department: <strong><?= htmlspecialchars($complaint['assigned_department'] ?: 'Not yet assigned') ?></strong></p>
    <p>Response deadline: <strong><?= htmlspecialchars($complaint['response_deadline'] ?? 'Not yet calculated') ?></strong></p>
</div>

<div class="detail-section">
    <h3>Administration</h3>
    <form method="POST">
       <div class="form-group">
<label for="status">Status</label>
    <select id="status" name="status"> 
                <?php foreach (['Submitted', 'Under Review', 'In Progress', 'Resolved'] as $option): ?>
                    <option value="<?= $option ?>" <?= $complaint['status'] === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
       </select>
</div>
        <button type="submit">Update status</button>
    </form>
</div>

<?php require '../includes/footer.php'; ?>