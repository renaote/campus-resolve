<?php
require '../config/database.php';
require '../includes/db-helpers.php';

$complaint = null;
$notFound = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $referenceNumber = trim($_POST['reference_number'] ?? '');

    if ($referenceNumber !== '') {
        $complaint = getComplaintByReference($pdo, $referenceNumber);
        if (!$complaint) {
            $notFound = true;
        }
    }
}

require '../includes/header.php';
?>

<h2>Track a Complaint</h2>

<form method="POST">
    <label>Reference number
        <input type="text" name="reference_number" placeholder="e.g. CR-2026-0001"
               value="<?= htmlspecialchars($_POST['reference_number'] ?? '') ?>">
    </label>
    <button type="submit">Track</button>
</form>

<?php if ($notFound): ?>
    <p>No complaint found with that reference number. Double check it and try again.</p>
<?php endif; ?>

<?php if ($complaint): ?>
    <div>
        <h3><?= htmlspecialchars($complaint['title']) ?></h3>
        <p>Reference: <strong><?= htmlspecialchars($complaint['reference_number']) ?></strong></p>
        <p>Category: <strong><?= htmlspecialchars($complaint['detected_category']) ?></strong></p>
        <p>Priority: <strong><?= htmlspecialchars($complaint['priority']) ?></strong></p>
        <p>Status: <strong><?= htmlspecialchars($complaint['status']) ?></strong></p>
        <p>Submitted: <?= htmlspecialchars($complaint['submitted_at']) ?></p>
        <p>Expected response by: <?= htmlspecialchars($complaint['response_deadline'] ?? 'Not yet calculated') ?></p>

        <?php if ($complaint['is_immediate_danger']): ?>
            <p><strong>This is not a substitute for emergency services.</strong>
               If you are in immediate danger, contact campus security directly.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<p><a href="index.php">&laquo; Back to home</a></p>

<?php require '../includes/footer.php'; ?>