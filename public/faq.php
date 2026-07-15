<?php
$breadcrumb = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'FAQ'],
];
require '../includes/header.php';
?>

<div class="page-title">Frequently asked questions</div>

<div class="faq-item">
    <p class="faq-question">How does the classification work?</p>
    <p class="faq-answer">When you submit a complaint, the system scans the text for keywords to guess its category, calculates an urgency score based on your answers and the wording, and assigns a priority from Low to Critical. This all happens automatically, with no one reading your complaint before it's routed.</p>
</div>

<div class="faq-item">
    <p class="faq-question">Can I submit anonymously?</p>
    <p class="faq-answer">Yes. Check "Submit anonymously" on the form and your name and email won't be saved. Keep in mind the department handling your complaint won't be able to contact you directly if you do this.</p>
</div>

<div class="faq-item">
    <p class="faq-question">What if I'm in immediate danger right now?</p>
    <p class="faq-answer">This system is not a substitute for emergency services. If you are in immediate danger, contact campus security or emergency services directly. Checking "immediate danger" on the form will flag your complaint as Critical, but a human still needs to see and act on it - it isn't instant.</p>
</div>

<div class="faq-item">
    <p class="faq-question">How long until I get a response?</p>
    <p class="faq-answer">This depends on the priority assigned: roughly 2 hours for Critical, 24 hours for High, 3 days for Medium, and 5 days for Low. You can check the exact deadline for your complaint using the tracking page.</p>
</div>

<div class="faq-item">
    <p class="faq-question">Can I edit my complaint after submitting it?</p>
    <p class="faq-answer">Not yet - once submitted, a complaint's details can't be changed by the student who filed it. If you need to add information, you'll need to submit a new complaint referencing your original reference number in the description.</p>
</div>

<?php require '../includes/footer.php'; ?>