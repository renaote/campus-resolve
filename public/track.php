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

$breadcrumb = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Track a complaint'],
];
require '../includes/header.php';
?>

<div class="page-narrow">
    <div class="page-title">Track a complaint</div>
    <p class="page-intro">Enter the reference number you received when you submitted your complaint to check its current status.</p>

    <form method="POST" class="form-section">
        <div class="form-group">
            <label for="reference_number">Reference number</label>
            <input type="text" id="reference_number" name="reference_number" placeholder="e.g. CR-2026-0001"
                   value="<?= htmlspecialchars($_POST['reference_number'] ?? '') ?>">
        </div>
        <button type="submit">Track</button>
    </form>

    <?php if ($notFound): ?>
        <p class="not-found">No complaint found with that reference number. Double check it and try again.</p>
    <?php endif; ?>

    <?php if ($complaint): ?>
        <div class="detail-section">
            <h3><?= htmlspecialchars($complaint['title']) ?></h3>
            <p>Reference: <span class="ref-number"><?= htmlspecialchars($complaint['reference_number']) ?></span></p>
            <p>Category: <strong><?= htmlspecialchars($complaint['detected_category']) ?></strong></p>
            <p>Priority:
                <span class="badge priority-<?= strtolower($complaint['priority']) ?>">
                    <?= htmlspecialchars($complaint['priority']) ?>
                </span>
            </p>
            <p>Status:
                <span class="badge <?= 'status-' . strtolower(str_replace(' ', '-', $complaint['status'])) ?>">
                    <?= htmlspecialchars($complaint['status']) ?>
                </span>
            </p>
            <p>Submitted: <?= htmlspecialchars($complaint['submitted_at']) ?></p>
            <p>Expected response by: <?= htmlspecialchars($complaint['response_deadline'] ?? 'Not yet calculated') ?></p>

            <?php if ($complaint['is_immediate_danger']): ?>
                <div class="danger-notice">
                    <strong>This is not a substitute for emergency services.</strong>
                    If you are in immediate danger, contact campus security directly.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require '../includes/footer.php'; ?>