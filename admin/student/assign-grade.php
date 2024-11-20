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

    // Validate the presence of student_id and subject_id
    if (isset($_POST['student_id'], $_POST['subject_id'])) {
        $student_id = (int)$_POST['student_id'];
        $subject_id = (int)$_POST['subject_id'];

        $studentSubjectDetails = getStudentSubjectDetails($student_id, $subject_id);

        if ($studentSubjectDetails) {
            // Concatenate first name and last name to create the full name
            $full_name = $studentSubjectDetails['first_name'] . ' ' . $studentSubjectDetails['last_name'];
            $subject_code = $studentSubjectDetails['subject_code'];
            $subject_name = $studentSubjectDetails['subject_name'];
            $grade = $studentSubjectDetails['grade'];
        } else {
            redirectTo($pathStudents);
        }
    } else {
        redirectTo($pathStudents);
    }

    // Handle form submission for grade assignment
    if (isset($_POST['btnAssignGrade'], $_POST['txtGrade'])) {
        $grade = $_POST['txtGrade'];
        $arrErrors = validateGrade($grade);

        if (empty($arrErrors)) {
            handleGradeAssignment($student_id, $subject_id, $grade);
            redirectTo("attach-subject.php?student_id=" . $student_id);
        }
    }

    // Handle cancellation
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
                    <a href="attach-subject.php?student_id=<?= htmlspecialchars($student_id); ?>" class="text-decoration-none">Attach Subject to Student</a>
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
            <li><strong>Student ID:</strong> <?= htmlspecialchars($student_id); ?></li>
            <li><strong>Name:</strong> <?= htmlspecialchars($full_name); ?></li>
            <li><strong>Subject Code:</strong> <?= htmlspecialchars($subject_code); ?></li>
            <li><strong>Subject Name:</strong> <?= htmlspecialchars($subject_name); ?></li>
        </ul>

        <hr>

        <div class="form-floating mb-3">
            <input type="number" class="form-control" id="txtGrade" name="txtGrade" placeholder="Grade" 
                value="<?= isset($_POST['txtGrade']) ? htmlspecialchars($_POST['txtGrade']) : number_format(htmlspecialchars($grade), 2); ?>">
            <label for="txtGrade">Grade</label>
        </div>

        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id); ?>">
        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id); ?>">

        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnAssignGrade" type="submit" class="btn btn-primary">Assign Grade</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
