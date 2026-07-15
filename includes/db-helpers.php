<?php
// Database query helpers specifically for the admin dashboard.
// Kept separate from functions.php since that file is about the
// classification logic, not database queries.

// Gets every complaint, with the most urgent ones first. MySQL doesn't
// know that "Critical" should outrank "Low" alphabetically, so FIELD()
// tells it the exact order to sort priorities in.
function getAllComplaints(PDO $pdo): array {
    $stmt = $pdo->query("
        SELECT * FROM complaints
        ORDER BY
            FIELD(priority, 'Critical', 'High', 'Medium', 'Low'),
            submitted_at ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Quick counts for the dashboard summary cards
function getComplaintCounts(PDO $pdo): array {
    $counts = [
        'total' => 0,
        'critical' => 0,
        'open' => 0,
        'resolved' => 0,
    ];

    $counts['total'] = (int) $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();

    $counts['critical'] = (int) $pdo->query("
        SELECT COUNT(*) FROM complaints WHERE priority = 'Critical'
    ")->fetchColumn();

    $counts['open'] = (int) $pdo->query("
        SELECT COUNT(*) FROM complaints WHERE status != 'Resolved'
    ")->fetchColumn();

    $counts['resolved'] = (int) $pdo->query("
        SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'
    ")->fetchColumn();

    return $counts;
}