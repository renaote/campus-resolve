<?php
require '../config/database.php';
require '../includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $isAnonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $isImmediateDanger = isset($_POST['is_immediate_danger']) ? 1 : 0;
    $affectsMultiple = isset($_POST['affects_multiple_students']) ? 1 : 0;
    $wasPreviouslyReported = isset($_POST['was_previously_reported']) ? 1 : 0;
    $studentName = trim($_POST['student_name'] ?? '');
    $studentEmail = trim($_POST['student_email'] ?? '');

    if ($title === '') {
        $errors[] = 'Please enter a title for your complaint.';
    }
    if ($description === '' || strlen($description) < 20) {
        $errors[] = 'Please describe the issue in at least 20 characters.';
    }

    if (empty($errors)) {
        $referenceNumber = generateReferenceNumber($pdo);
        $fullText = $title . ' ' . $description;

        $detectedCategory = detectCategory($fullText);
        $urgencyScore = calculateUrgencyScore(
            $fullText,
            (bool) $isImmediateDanger,
            (bool) $affectsMultiple,
            (bool) $wasPreviouslyReported
        );
        $priority = determinePriority($urgencyScore, (bool) $isImmediateDanger);
        $department = getDepartmentForCategory($detectedCategory);
        $responseDeadline = calculateResponseDeadline($priority);

        $stmt = $pdo->prepare("
            INSERT INTO complaints
                (reference_number, title, description, detected_category,
                 urgency_score, priority, assigned_department, response_deadline,
                 is_immediate_danger, affects_multiple_students, was_previously_reported,
                 is_anonymous, student_name, student_email)
            VALUES
                (:reference_number, :title, :description, :detected_category,
                 :urgency_score, :priority, :assigned_department, :response_deadline,
                 :is_immediate_danger, :affects_multiple_students, :was_previously_reported,
                 :is_anonymous, :student_name, :student_email)
        ");

        $stmt->execute([
            'reference_number' => $referenceNumber,
            'title' => $title,
            'description' => $description,
            'detected_category' => $detectedCategory,
            'urgency_score' => $urgencyScore,
            'priority' => $priority,
            'assigned_department' => $department,
            'response_deadline' => $responseDeadline,
            'is_immediate_danger' => $isImmediateDanger,
            'affects_multiple_students' => $affectsMultiple,
            'was_previously_reported' => $wasPreviouslyReported,
            'is_anonymous' => $isAnonymous,
            'student_name' => $isAnonymous ? null : $studentName,
            'student_email' => $isAnonymous ? null : $studentEmail,
        ]);

        header('Location: index.php?submitted=1'
            . '&ref=' . urlencode($referenceNumber)
            . '&category=' . urlencode($detectedCategory)
            . '&priority=' . urlencode($priority)
            . '&department=' . urlencode($department)
            . '&deadline=' . urlencode($responseDeadline));
        exit;
    }
}

$breadcrumb = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Submit a complaint'],
];
require '../includes/header.php';
?>

<div class="page-narrow">
    <div class="page-title">Submit a complaint</div>

    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p class="page-intro">Use this form to report any issue affecting you or others on campus, from safety concerns to academic, financial, facilities, or IT problems. Before you submit, please confirm the following:</p>
    <ul class="checklist">
        <li>You've described the issue in enough detail for it to be understood and acted on</li>
        <li>This is not a medical or physical emergency requiring immediate emergency services</li>
        <li>You understand your complaint will be automatically classified and routed to a department</li>
        <li>If submitting anonymously, you understand the department won't be able to contact you directly</li>
    </ul>

    <form method="POST" class="form-section">
        <div class="form-group">
            <label for="title">Complaint title</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="checkbox-grid">
            <label class="checkbox-group">
                <input type="checkbox" name="is_immediate_danger">
                This involves immediate danger
            </label>

            <label class="checkbox-group">
                <input type="checkbox" name="affects_multiple_students">
                This affects multiple students
            </label>

            <label class="checkbox-group">
                <input type="checkbox" name="was_previously_reported">
                I've reported this before
            </label>

            <label class="checkbox-group">
                <input type="checkbox" name="is_anonymous" id="anonymous">
                Submit anonymously
            </label>
        </div>

        <div class="form-group">
            <label for="student_name">Your name</label>
            <input type="text" id="student_name" name="student_name">
        </div>

        <div class="form-group">
            <label for="student_email">Your email</label>
            <input type="email" id="student_email" name="student_email">
        </div>

        <button type="submit">Submit complaint</button>
    </form>
</div>

<?php require '../includes/footer.php'; ?>