<?php
$breadcrumb = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Departments'],
];
require '../includes/header.php';

$departments = [
    ['name' => 'Campus Security', 'handles' => 'Safety and Misconduct', 'phone' => '0800 000 111', 'hours' => '24/7'],
    ['name' => 'Faculty Administration', 'handles' => 'Academic', 'phone' => '012 345 6789', 'hours' => 'Mon-Fri, 8am-4pm'],
    ['name' => 'Finance Office', 'handles' => 'Finance', 'phone' => '012 345 6790', 'hours' => 'Mon-Fri, 8am-4pm'],
    ['name' => 'Facilities Department', 'handles' => 'Facilities', 'phone' => '012 345 6791', 'hours' => 'Mon-Fri, 7am-5pm'],
    ['name' => 'IT Support', 'handles' => 'IT Support', 'phone' => '012 345 6792', 'hours' => 'Mon-Fri, 8am-6pm'],
    ['name' => 'Student Affairs', 'handles' => 'Unclassified / general', 'phone' => '012 345 6793', 'hours' => 'Mon-Fri, 8am-4pm'],
];
?>

<div class="page-title">Department contacts</div>
<p class="page-intro">Every complaint you submit gets routed to one of these departments automatically. If something is urgent, you don't have to wait - you can call the relevant department directly using the numbers below.</p>

<table class="dept-table">
    <thead>
        <tr>
            <th>Department</th>
            <th>Handles</th>
            <th>Phone</th>
            <th>Hours</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($departments as $dept): ?>
            <tr>
                <td class="dept-name"><?= htmlspecialchars($dept['name']) ?></td>
                <td><?= htmlspecialchars($dept['handles']) ?></td>
                <td class="dept-phone"><?= htmlspecialchars($dept['phone']) ?></td>
                <td><?= htmlspecialchars($dept['hours']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="page-intro" style="margin-top: 20px;">Not sure which department is right for your issue? Just <a href="submit.php">submit a complaint</a> and the system will work that out for you.</p>

<?php require '../includes/footer.php'; ?>