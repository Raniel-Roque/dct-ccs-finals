<?php
    ob_start();
    session_start();
    $title = 'Assign Grade to Subject';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();

    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "../subject/add.php";
    $pathStudents = "register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    $arrErrors = [];

    if (isset($_POST['student_id'], $_POST['subject_id'])) {
        $student_id = sanitize($_POST['student_id']);
        $subject_id = sanitize($_POST['subject_id']);

        $studentSubjectDetails = getStudentSubjectDetails($student_id, $subject_id);

        if ($studentSubjectDetails) {
            $full_name = sanitize($studentSubjectDetails['first_name'] . ' ' . $studentSubjectDetails['last_name']);
            $subject_code = sanitize($studentSubjectDetails['subject_code']);
            $subject_name = sanitize($studentSubjectDetails['subject_name']);
            $grade = sanitize($studentSubjectDetails['grade']);
        } else {
            redirectTo($pathStudents);
        }
    } else {
        redirectTo($pathStudents);
    }

    if (isset($_POST['btnAssignGrade'], $_POST['txtGrade'])) {
        $grade = sanitize($_POST['txtGrade']);
        $arrErrors = validateGrade($grade);

        if (empty($arrErrors)) {
            handleGradeAssignment($student_id, $subject_id, $grade);
            redirectTo("attach-subject.php?student_id=" . $student_id);
        }
    }

    if (isset($_POST['btnCancel'])) {
        redirectTo("attach-subject.php?student_id=" . $student_id);
    }
?>

<main class="container col-8 mt-4">
    <h2 class="mt-4">Assign Grade to Subject</h2>
    <div class="mt-5 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                <li class="breadcrumb-item">
                    <a href="attach-subject.php?student_id=<?= sanitize($student_id); ?>" class="text-decoration-none">Attach Subject to Student</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Assign Grade to Subject</li>
            </ol>
        </nav>
    </div>

    <?php if ($arrErrors): ?>
        <?= displayErrors($arrErrors); ?>
    <?php endif; ?>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <h4>Selected Student and Subject Information</h4>

        <ul>
            <li><strong>Student ID:</strong> <?= sanitize($student_id); ?></li>
            <li><strong>Name:</strong> <?= sanitize($full_name); ?></li>
            <li><strong>Subject Code:</strong> <?= sanitize($subject_code); ?></li>
            <li><strong>Subject Name:</strong> <?= sanitize($subject_name); ?></li>
        </ul>

        <hr>

        <div class="form-floating mb-3">
            <input type="number" class="form-control" id="txtGrade" name="txtGrade" placeholder="Grade" 
                value="<?= isset($_POST['txtGrade']) ? sanitize($_POST['txtGrade']) : number_format(sanitize($grade), 2); ?>">
            <label for="txtGrade">Grade</label>
        </div>

        <input type="hidden" name="student_id" value="<?= sanitize($student_id); ?>">
        <input type="hidden" name="subject_id" value="<?= sanitize($subject_id); ?>">

        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnAssignGrade" type="submit" class="btn btn-primary">Assign Grade</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>