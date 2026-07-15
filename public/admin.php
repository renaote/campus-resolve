<?php
require '../config/database.php';
require '../includes/db-helpers.php';

// Read whichever filters were picked from the dropdowns (if any)
$filters = [
    'priority' => $_GET['priority'] ?? '',
    'category' => $_GET['category'] ?? '',
    'status' => $_GET['status'] ?? '',
];

$hasActiveFilter = !empty(array_filter($filters));

$complaints = $hasActiveFilter
    ? getFilteredComplaints($pdo, $filters)
    : getAllComplaints($pdo);

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

<form method="GET">
    <label>Priority
        <select name="priority">
            <option value="">All</option>
            <?php foreach (['Critical', 'High', 'Medium', 'Low'] as $option): ?>
                <option value="<?= $option ?>" <?= $filters['priority'] === $option ? 'selected' : '' ?>>
                    <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Category
        <select name="category">
            <option value="">All</option>
            <?php foreach (['Safety and Misconduct', 'Academic', 'Finance', 'Facilities', 'IT Support', 'Unclassified'] as $option): ?>
                <option value="<?= $option ?>" <?= $filters['category'] === $option ? 'selected' : '' ?>>
                    <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Status
        <select name="status">
            <option value="">All</option>
            <?php foreach (['Submitted', 'Under Review', 'In Progress', 'Resolved'] as $option): ?>
                <option value="<?= $option ?>" <?= $filters['status'] === $option ? 'selected' : '' ?>>
                    <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <button type="submit">Filter</button>
    <a href="admin.php">Clear filters</a>
</form>

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
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($complaints)): ?>
            <tr>
                <td colspan="8">No complaints match this filter.</td>
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
                    <td><a href="details.php?id=<?= $complaint['id'] ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require '../includes/footer.php'; ?>