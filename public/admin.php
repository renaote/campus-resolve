<?php
require '../config/database.php';
require '../includes/db-helpers.php';

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

function statusClass(string $status): string {
    return 'status-' . strtolower(str_replace(' ', '-', $status));
}

$breadcrumb = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Admin dashboard'],
];
require '../includes/header.php';
?>

<div class="page-title">Admin dashboard</div>

<div class="stat-strip">
    <div class="stat-item stat-total">
        <span class="count"><?= $counts['total'] ?></span>
        <span class="label">Total</span>
    </div>
    <div class="stat-item stat-critical">
        <span class="count"><?= $counts['critical'] ?></span>
        <span class="label">Critical</span>
    </div>
    <div class="stat-item stat-open">
        <span class="count"><?= $counts['open'] ?></span>
        <span class="label">Open</span>
    </div>
    <div class="stat-item stat-resolved">
        <span class="count"><?= $counts['resolved'] ?></span>
        <span class="label">Resolved</span>
    </div>
</div>

<form method="GET" class="filter-form">
    <select name="priority">
        <option value="">All priorities</option>
        <?php foreach (['Critical', 'High', 'Medium', 'Low'] as $option): ?>
            <option value="<?= $option ?>" <?= $filters['priority'] === $option ? 'selected' : '' ?>><?= $option ?></option>
        <?php endforeach; ?>
    </select>

    <select name="category">
        <option value="">All categories</option>
        <?php foreach (['Safety and Misconduct', 'Academic', 'Finance', 'Facilities', 'IT Support', 'Unclassified'] as $option): ?>
            <option value="<?= $option ?>" <?= $filters['category'] === $option ? 'selected' : '' ?>><?= $option ?></option>
        <?php endforeach; ?>
    </select>

    <select name="status">
        <option value="">All statuses</option>
        <?php foreach (['Submitted', 'Under Review', 'In Progress', 'Resolved'] as $option): ?>
            <option value="<?= $option ?>" <?= $filters['status'] === $option ? 'selected' : '' ?>><?= $option ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn-primary">Filter</button>
    <a href="admin.php" class="btn-secondary">Clear</a>
</form>

<table>
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
            <tr><td colspan="8">No complaints match this filter.</td></tr>
        <?php else: ?>
            <?php foreach ($complaints as $complaint): ?>
                <tr>
                    <td class="ref-number"><?= htmlspecialchars($complaint['reference_number']) ?></td>
                    <td><?= htmlspecialchars($complaint['title']) ?></td>
                    <td><?= htmlspecialchars($complaint['detected_category']) ?></td>
                    <td><span class="badge priority-<?= strtolower($complaint['priority']) ?>"><?= htmlspecialchars($complaint['priority']) ?></span></td>
                    <td><?= htmlspecialchars($complaint['assigned_department'] ?: '-') ?></td>
                    <td><span class="badge <?= statusClass($complaint['status']) ?>"><?= htmlspecialchars($complaint['status']) ?></span></td>
                    <td><?= htmlspecialchars($complaint['response_deadline'] ?? '-') ?></td>
                    <td><a href="details.php?id=<?= $complaint['id'] ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require '../includes/footer.php'; ?>