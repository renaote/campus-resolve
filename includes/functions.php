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
    $padded = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    return "CR-{$year}-{$padded}";
}

// Keyword lists for each category. Kept as a simple array here instead of
// a database table for now - easier to tweak while I'm still testing which
// words actually work well, could move to the database later if needed.
function getCategoryKeywords(): array {
    return [
        'Safety and Misconduct' => [
            'unsafe', 'threat', 'threatened', 'assault', 'security',
            'stolen', 'theft', 'dark walkway', 'harassment', 'bullying',
            'discrimination', 'inappropriate', 'intimidation', 'misconduct',
        ],
        'Academic' => [
            'marks', 'mark', 'grade', 'lecturer', 'exam', 'examination',
            'test', 'assignment', 'timetable', 'assessment', 'supplementary',
        ],
        'Finance' => [
            'payment', 'refund', 'fees', 'fee', 'bursary', 'invoice',
            'charged', 'account balance', 'financial',
        ],
        'Facilities' => [
            'broken', 'water', 'electricity', 'toilet', 'door', 'furniture',
            'maintenance', 'dirty', 'light', 'lights', 'residence',
        ],
        'IT Support' => [
            'wifi', 'wi-fi', 'internet', 'portal', 'password', 'login',
            'computer', 'email', 'system', 'website',
        ],
    ];
}

// Looks through the complaint text and counts how many keywords match
// each category. Whichever category has the most matches wins. If nothing
// matches at all, it falls back to "Unclassified" rather than guessing.
function detectCategory(string $text): string {
    $text = strtolower($text);
    $keywordsByCategory = getCategoryKeywords();

    $scores = [];
    foreach ($keywordsByCategory as $category => $keywords) {
        $score = 0;
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                $score++;
            }
        }
        $scores[$category] = $score;
    }

    $bestCategory = array_key_first($scores);
    $bestScore = $scores[$bestCategory];

    foreach ($scores as $category => $score) {
        if ($score > $bestScore) {
            $bestCategory = $category;
            $bestScore = $score;
        }
    }

    // Nothing matched any keyword list at all
    if ($bestScore === 0) {
        return 'Unclassified';
    }

    return $bestCategory;
}