<?php require '../includes/header.php'; ?>

<p>Submit a complaint about a campus issue and get it routed to the right department automatically.</p>

<?php if (isset($_GET['submitted']) && isset($_GET['ref'])): ?>
    <div>
        <p>Complaint submitted successfully.</p>
        <p>Your reference number: <strong><?= htmlspecialchars($_GET['ref']) ?></strong></p>
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
        <p>Save this number - you'll need it to track your complaint.</p>
    </div>
<?php endif; ?>

<a href="submit.php">Submit a Complaint</a>
<a href="track.php">Track a Complaint</a>
<a href="admin.php">Admin Dashboard</a>

<?php require '../includes/footer.php'; ?>