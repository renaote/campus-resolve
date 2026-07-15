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

// Same as above, but only returns complaints matching whichever filters
// were actually passed in. Building the WHERE clause piece by piece so
// I only filter on things the admin actually picked, not everything.
function getFilteredComplaints(PDO $pdo, array $filters): array {
    $sql = "SELECT * FROM complaints WHERE 1=1";
    $params = [];

    if (!empty($filters['priority'])) {
        $sql .= " AND priority = :priority";
        $params['priority'] = $filters['priority'];
    }
    if (!empty($filters['category'])) {
        $sql .= " AND detected_category = :category";
        $params['category'] = $filters['category'];
    }
    if (!empty($filters['status'])) {
        $sql .= " AND status = :status";
        $params['status'] = $filters['status'];
    }

    $sql .= " ORDER BY FIELD(priority, 'Critical', 'High', 'Medium', 'Low'), submitted_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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

// Gets one complaint by its database id - used on the details page
function getComplaintById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM complaints WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

// Looks up a complaint by its reference number instead of its database id -
// this is what students use, since they only ever see the reference
// number (like CR-2026-0001), never the internal id.
function getComplaintByReference(PDO $pdo, string $referenceNumber): ?array {
    $stmt = $pdo->prepare("SELECT * FROM complaints WHERE reference_number = :ref");
    $stmt->execute(['ref' => $referenceNumber]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

// Updates just the status field for one complaint. If the new status is
// Resolved, also stamps resolved_at with the current time.
function updateComplaintStatus(PDO $pdo, int $id, string $status): void {
    if ($status === 'Resolved') {
        $stmt = $pdo->prepare("
            UPDATE complaints
            SET status = :status, resolved_at = NOW()
            WHERE id = :id
        ");
    } else {
        $stmt = $pdo->prepare("
            UPDATE complaints
            SET status = :status
            WHERE id = :id
        ");
    }
    $stmt->execute(['status' => $status, 'id' => $id]);
}