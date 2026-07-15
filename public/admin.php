<?php
require '../config/database.php';
require '../includes/db-helpers.php';

$complaints = getAllComplaints($pdo);
$counts = getComplaintCounts($pdo);

require '../includes/header.php';
?>

<h2>Admin Dashboard</h2>

<div>
    <p>Total Complaints: <strong><?= $counts['total'] ?></strong></p>
    <p>Critical Complaints: <strong><?= $counts['critical'] ?></strong></p>
    <p>Open Complaints: <strong><?= $counts['open'] ?></strong></p>
    <p>Resolved Complaints: <strong><?= $counts['resolved'] ?></strong></p>
</div>

<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>Reference</th>
            <th>Title</th>
            <th>Category</th>
            <th>Priority</th>
            <th>Department</th>
            <th>Status</th>
            <th>Deadline</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($complaints)): ?>
            <tr>
                <td colspan="7">No complaints have been submitted yet.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($complaints as $complaint): ?>
                <tr>
                    <td><?= htmlspecialchars($complaint['reference_number']) ?></td>
                    <td><?= htmlspecialchars($complaint['title']) ?></td>
                    <td><?= htmlspecialchars($complaint['detected_category']) ?></td>
                    <td><?= htmlspecialchars($complaint['priority']) ?></td>
                    <td><?= htmlspecialchars($complaint['assigned_department']) ?></td>
                    <td><?= htmlspecialchars($complaint['status']) ?></td>
                    <td><?= htmlspecialchars($complaint['response_deadline']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require '../includes/footer.php'; ?>