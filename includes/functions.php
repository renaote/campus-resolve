<?php
// Small reusable helper functions used across multiple pages,
// so this logic doesn't get copy-pasted everywhere.

// Builds a reference number like CR-2026-0001. Counts how many complaints
// already exist this year and adds one, so numbers stay sequential and
// reset naturally each year instead of just growing forever.
function generateReferenceNumber(PDO $pdo): string {
    $year = date('Y');

    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM complaints
        WHERE YEAR(submitted_at) = :year
    ");
    $stmt->execute(['year' => $year]);
    $countThisYear = (int) $stmt->fetchColumn();

    $nextNumber = $countThisYear + 1;

    // str_pad makes sure it's always 4 digits: 1 becomes 0001, 23 becomes 0023
    $padded = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    return "CR-{$year}-{$padded}";
}