<?php
    ob_start();
    session_start();
    $title = 'Detach Subject from Student';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();

    // Define paths for navigation
    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "../subject/add.php";
    $pathStudents = "register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    // Check if student and subject are provided and fetch details
    if (isset($_POST['student_id']) && isset($_POST['subject_id'])) {
        $student_id = $_POST['student_id'];
        $subject_id = $_POST['subject_id'];

        // Fetch student and subject details
        $studentSubjectDetails = getStudentSubjectDetails($student_id, $subject_id);

        if ($studentSubjectDetails) {
            $first_name = $studentSubjectDetails['first_name'];
            $last_name = $studentSubjectDetails['last_name'];
            $subject_code = $studentSubjectDetails['subject_code'];
            $subject_name = $studentSubjectDetails['subject_name'];
        }
    } else {
        redirectTo("register.php");
    }

    // Handle detachment of the subject from the student
    if (isset($_POST['btnConfirmDetach'])) {
        detachSubjectFromStudent($student_id, $subject_id);
        redirectTo("attach-subject.php?student_id=" . $student_id);
    }

    // Cancel detachment and go back
    if (isset($_POST['btnCancel'])) {
        redirectTo("attach-subject.php?student_id=" . $student_id);
    }
?>

<main class="container justify-content-between align-items-center col-8 mt-4">
    <h2 class="mt-4">Detach Subject from Student</h2>
    <div class="mt-5 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                <li class="breadcrumb-item"><a href="attach-subject.php?student_id=<?= htmlspecialchars($student_id); ?>" class="text-decoration-none">Attach Subject to Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detach Subject from Student</li>
            </ol>
        </nav>
    </div>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <p>Are you sure you want to detach the following subject from this student?</p>

        <ul>
            <li><strong>Student ID:</strong> <?= htmlspecialchars($student_id); ?></li>
            <li><strong>First Name:</strong> <?= htmlspecialchars($first_name); ?></li>
            <li><strong>Last Name:</strong> <?= htmlspecialchars($last_name); ?></li>
            <li><strong>Subject Code:</strong> <?= htmlspecialchars($subject_code); ?></li>
            <li><strong>Subject Name:</strong> <?= htmlspecialchars($subject_name); ?></li>
        </ul>

        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id); ?>">
        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id); ?>">

        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnConfirmDetach" type="submit" class="btn btn-danger">Detach Subject</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
