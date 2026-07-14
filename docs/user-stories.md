# User stories — CampusResolve

Format: As a `<role>`, I want `<goal>`, so that `<benefit>`.

## Student side
1. As a student, I want to submit a complaint with a title and description, so that the issue gets recorded somewhere official instead of just being forgotten.
2. As a student, I want the option to submit anonymously, so that I can report sensitive issues without giving my name.
3. As a student, I want to say whether the issue involves immediate danger, so that urgent complaints get treated differently from minor ones.
4. As a student, I want to get a reference number after submitting, so that I can check on my complaint later.
5. As a student, I want to see what category and priority my complaint was given, so that I know it's actually been looked at.
6. As a student, I want to track my complaint using my reference number, so that I can check its status without having to contact anyone.

## Automatic triage
7. As the system, I want to scan the complaint text for keywords, so that I can guess which category it belongs to without a human reading it first.
8. As the system, I want to calculate an urgency score based on the text and the student's answers, so that dangerous complaints don't get stuck behind minor ones.
9. As the system, I want to turn that score into a priority level (Low, Medium, High, Critical), so that admins know what to look at first.
10. As the system, I want to route the complaint to the right department based on its category, so that admins don't have to figure that out manually.
11. As the system, I want to calculate a response deadline based on priority, so that there's a clear expectation for how fast it should be handled.
12. As the system, I want to flag sensitive complaints (harassment, safety, etc.), so that they get handled more carefully.

## Admin side
13. As an admin, I want to see all complaints sorted with the most urgent ones first, so that I don't miss something serious.
14. As an admin, I want to filter complaints by category, priority, or status, so that I can focus on a specific type of issue.
15. As an admin, I want to open a complaint and see the full details, including why it was classified that way, so that I can trust the system's decision or override it.
16. As an admin, I want to update a complaint's status (Submitted, Under Review, In Progress, Resolved), so that students can see progress.
17. As an admin, I want to write a public response that the student can see, so that they get some feedback without me contacting them directly.

## Out of scope (v1)
- Real login/authentication for students or admins
- Email or SMS notifications
- Machine learning-based classification
- Multiple admin permission levels