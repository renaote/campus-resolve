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
//
// Known limitation: when two categories tie, this picks whichever one
// appears first in the array above - it doesn't try to break the tie any
// smarter than that. Worth mentioning in the README.
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

    if ($bestScore === 0) {
        return 'Unclassified';
    }

    return $bestCategory;
}

// Calculates an urgency score from 0-100 based on the complaint text and
// the yes/no answers from the form. Higher score = more urgent. Capped at
// 100 so one complaint hitting every rule doesn't break the priority math.
function calculateUrgencyScore(
    string $text,
    bool $isImmediateDanger,
    bool $affectsMultipleStudents,
    bool $wasPreviouslyReported
): int {
    $text = strtolower($text);
    $score = 0;

    if ($isImmediateDanger) {
        $score += 40;
    }

    $safetyWords = ['unsafe', 'threat', 'threatened', 'assault', 'security', 'danger'];
    foreach ($safetyWords as $word) {
        if (str_contains($text, $word)) {
            $score += 25;
            break;
        }
    }

    $timeWords = ['exam tomorrow', 'deadline', 'closes tomorrow', 'closes today', 'graduation'];
    foreach ($timeWords as $word) {
        if (str_contains($text, $word)) {
            $score += 20;
            break;
        }
    }

    $essentialServiceWords = ['no water', 'no electricity', 'portal unavailable', 'account blocked'];
    foreach ($essentialServiceWords as $word) {
        if (str_contains($text, $word)) {
            $score += 15;
            break;
        }
    }

    if ($affectsMultipleStudents) {
        $score += 10;
    }

    if ($wasPreviouslyReported) {
        $score += 10;
    }

    return min($score, 100);
}

// Turns the 0-100 urgency score into a priority label. Immediate danger
// always forces Critical, even if the score itself came out lower than
// 75 - a student saying "I'm in danger" shouldn't get downgraded just
// because their wording didn't match every scoring keyword.
function determinePriority(int $urgencyScore, bool $isImmediateDanger): string {
    if ($isImmediateDanger) {
        return 'Critical';
    }

    if ($urgencyScore >= 75) {
        return 'Critical';
    } elseif ($urgencyScore >= 50) {
        return 'High';
    } elseif ($urgencyScore >= 25) {
        return 'Medium';
    } else {
        return 'Low';
    }
}

// Maps each category to the department that should handle it. Unclassified
// complaints go to Student Affairs by default, since that's the closest
// thing to a general front desk for anything that doesn't fit elsewhere.
function getDepartmentForCategory(string $category): string {
    $departmentMap = [
        'Safety and Misconduct' => 'Campus Security',
        'Academic' => 'Faculty Administration',
        'Finance' => 'Finance Office',
        'Facilities' => 'Facilities Department',
        'IT Support' => 'IT Support',
        'Unclassified' => 'Student Affairs',
    ];

    return $departmentMap[$category] ?? 'Student Affairs';
}

// Works out a response deadline based on priority. Returns a full
// DATETIME string ready to save straight into the database.
function calculateResponseDeadline(string $priority): string {
    $now = new DateTime();

    switch ($priority) {
        case 'Critical':
            $now->modify('+2 hours');
            break;
        case 'High':
            $now->modify('+24 hours');
            break;
        case 'Medium':
            $now->modify('+3 days');
            break;
        default: // Low
            $now->modify('+5 days');
            break;
    }

    return $now->format('Y-m-d H:i:s');
}