<?php
require '../config/database.php';

$errors = [];

// Only try to save something if the form was actually submitted (POST),
// not just when someone visits the page normally (GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $isAnonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $isImmediateDanger = isset($_POST['is_immediate_danger']) ? 1 : 0;
    $affectsMultiple = isset($_POST['affects_multiple_students']) ? 1 : 0;
    $wasPreviouslyReported = isset($_POST['was_previously_reported']) ? 1 : 0;
    $studentName = trim($_POST['student_name'] ?? '');
    $studentEmail = trim($_POST['student_email'] ?? '');

    // Basic validation - title and description are the two things
    // the whole classification engine depends on, so they can't be empty
    if ($title === '') {
        $errors[] = 'Please enter a title for your complaint.';
    }
    if ($description === '' || strlen($description) < 20) {
        $errors[] = 'Please describe the issue in at least 20 characters.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO complaints
                (reference_number, title, description, is_immediate_danger,
                 affects_multiple_students, was_previously_reported, is_anonymous,
                 student_name, student_email)
            VALUES
                (:reference_number, :title, :description, :is_immediate_danger,
                 :affects_multiple_students, :was_previously_reported, :is_anonymous,
                 :student_name, :student_email)
        ");

        // Reference number is just a placeholder for now - proper generation
        // logic comes in the next commit
        $referenceNumber = 'CR-TEMP';

        $stmt->execute([
            'reference_number' => $referenceNumber,
            'title' => $title,
            'description' => $description,
            'is_immediate_danger' => $isImmediateDanger,
            'affects_multiple_students' => $affectsMultiple,
            'was_previously_reported' => $wasPreviouslyReported,
            'is_anonymous' => $isAnonymous,
            'student_name' => $isAnonymous ? null : $studentName,
            'student_email' => $isAnonymous ? null : $studentEmail,
        ]);

        header('Location: index.php?submitted=1');
        exit;
    }
}

require '../includes/header.php';
?>

<h2>Submit a Complaint</h2>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <label>Complaint title
        <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
    </label>

    <label>Description
        <textarea name="description" rows="5"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </label>

    <label>
        <input type="checkbox" name="is_immediate_danger">
        This involves immediate danger
    </label>

    <label>
        <input type="checkbox" name="affects_multiple_students">
        This affects multiple students
    </label>

    <label>
        <input type="checkbox" name="was_previously_reported">
        I've reported this before
    </label>

    <label>
        <input type="checkbox" name="is_anonymous" id="anonymous">
        Submit anonymously
    </label>

    <div id="identity-fields">
        <label>Your name
            <input type="text" name="student_name">
        </label>
        <label>Your email
            <input type="email" name="student_email">
        </label>
    </div>

    <button type="submit">Submit Complaint</button>
</form>

<?php require '../includes/footer.php'; ?>