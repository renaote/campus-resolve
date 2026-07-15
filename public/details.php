<?php
require '../config/database.php';
require '../includes/db-helpers.php';

$id = (int) ($_GET['id'] ?? 0);
$complaint = getComplaintById($pdo, $id);

if (!$complaint) {
    require '../includes/header.php';
    echo '<p>Complaint not found.</p>';
    require '../includes/footer.php';
    exit;
}

$statusMessage = '';

// Handle the status update form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    updateComplaintStatus($pdo, $id, $_POST['status']);
    // Reload the complaint so the page shows the new status right away
    $complaint = getComplaintById($pdo, $id);
    $statusMessage = 'Status updated.';
}

require '../includes/header.php';
?>

<a href="admin.php">&laquo; Back to dashboard</a>

<h2><?= htmlspecialchars($complaint['title']) ?></h2>
<p>Reference: <strong><?= htmlspecialchars($complaint['reference_number']) ?></strong></p>

<?php if ($statusMessage): ?>
    <p><?= htmlspecialchars($statusMessage) ?></p>
<?php endif; ?>

<h3>Student submission</h3>
<p><?= nl2br(htmlspecialchars($complaint['description'])) ?></p>
<p>Submitted: <?= htmlspecialchars($complaint['submitted_at']) ?></p>
<p>Anonymous: <?= $complaint['is_anonymous'] ? 'Yes' : 'No' ?></p>
<?php if (!$complaint['is_anonymous']): ?>
    <p>Name: <?= htmlspecialchars($complaint['student_name'] ?? 'Not provided') ?></p>
    <p>Email: <?= htmlspecialchars($complaint['student_email'] ?? 'Not provided') ?></p>
<?php endif; ?>

<h3>Classification result</h3>
<p>Category: <strong><?= htmlspecialchars($complaint['detected_category']) ?></strong></p>
<p>Urgency score: <strong><?= htmlspecialchars($complaint['urgency_score']) ?> / 100</strong></p>
<p>Priority: <strong><?= htmlspecialchars($complaint['priority']) ?></strong></p>
<p>Assigned department: <strong><?= htmlspecialchars($complaint['assigned_department']) ?></strong></p>
<p>Response deadline: <strong><?= htmlspecialchars($complaint['response_deadline']) ?></strong></p>

<h3>Administration</h3>
<form method="POST">
    <label>Status
        <select name="status">
            <?php foreach (['Submitted', 'Under Review', 'In Progress', 'Resolved'] as $option): ?>
                <option value="<?= $option ?>" <?= $complaint['status'] === $option ? 'selected' : '' ?>>
                    <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit">Update status</button>
</form>

<?php require '../includes/footer.php'; ?>