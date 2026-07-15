<?php
$breadcrumb = [['label' => 'Home']];
require '../includes/header.php';
?>

<div class="page-title">CampusResolve</div>
<p class="page-intro">Submit a complaint about a campus issue and get it automatically classified, prioritised, and routed to the right department.</p>

<?php if (isset($_GET['submitted']) && isset($_GET['ref'])): ?>
    <div class="confirmation-box">
        <p><strong>Complaint submitted successfully.</strong></p>
        <p>Reference number: <span class="ref-number"><?= htmlspecialchars($_GET['ref']) ?></span></p>
        <?php if (isset($_GET['category'])): ?>
            <p>Detected category: <strong><?= htmlspecialchars($_GET['category']) ?></strong></p>
        <?php endif; ?>
        <?php if (isset($_GET['priority'])): ?>
            <p>Priority: <strong><?= htmlspecialchars($_GET['priority']) ?></strong></p>
        <?php endif; ?>
        <?php if (isset($_GET['department'])): ?>
            <p>Assigned to: <strong><?= htmlspecialchars($_GET['department']) ?></strong></p>
        <?php endif; ?>
        <?php if (isset($_GET['deadline'])): ?>
            <p>Expected response by: <strong><?= htmlspecialchars($_GET['deadline']) ?></strong></p>
        <?php endif; ?>
        <p>Save this reference number - you'll need it to track your complaint.</p>
    </div>
<?php endif; ?>

<div class="how-it-works">
    <div class="step-card">
        <span class="step-number">1</span>
        <h4>Submit</h4>
        <p>Describe the issue. Say whether it's urgent, affects others, or has come up before.</p>
    </div>
    <div class="step-card">
        <span class="step-number">2</span>
        <h4>Classify</h4>
        <p>The system reads your complaint, works out a category and urgency score, and assigns a priority.</p>
    </div>
    <div class="step-card">
        <span class="step-number">3</span>
        <h4>Route</h4>
        <p>Your complaint goes to the right department automatically, with a response deadline attached.</p>
    </div>
    <div class="step-card">
        <span class="step-number">4</span>
        <h4>Track</h4>
        <p>Check on your complaint any time using the reference number you were given.</p>
    </div>
</div>

<a href="submit.php" class="btn-primary">Submit a complaint</a>

<div class="emergency-callout">
    <strong>In immediate danger?</strong> This system is not a substitute for emergency services. Contact Campus Security directly on <strong>0800 000 111</strong>, available 24/7.
</div>

<?php require '../includes/footer.php'; ?>