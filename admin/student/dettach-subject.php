<?php
    ob_start();
    session_start();
    $title = 'Detach Subject from Student';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();

    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "../subject/add.php";
    $pathStudents = "register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    if (isset($_POST['student_id']) && isset($_POST['subject_id'])) {
        $student_id = $_POST['student_id'];
        $subject_id = $_POST['subject_id'];  // subject_id is passed from the form

        $con = getDatabaseConnection();
        
        // Fetch student and subject details based on student_id and subject_id
        $stmt = $con->prepare("SELECT students.student_id, students.first_name, students.last_name, 
                                          subjects.subject_code, subjects.subject_name
                                   FROM students
                                   JOIN students_subjects ON students.student_id = students_subjects.student_id
                                   JOIN subjects ON subjects.subject_code = students_subjects.subject_id
                                   WHERE students_subjects.student_id = ? AND students_subjects.subject_id = ?");
        $stmt->bind_param("ii", $student_id, $subject_id);  // Use both student_id and subject_id for the query
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $subject_code = $data['subject_code'];
            $subject_name = $data['subject_name'];
        }

        $stmt->close();
        mysqli_close($con);
    } else {
        header("Location: register.php");
        exit;
    }

    // Detach subject when the confirmation button is clicked
    if (isset($_POST['btnConfirmDetach'])) {
        $con = getDatabaseConnection();
        
        // Perform the delete operation to detach the subject from the student
        $stmt = $con->prepare("DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $student_id, $subject_id);  // subject_id and student_id are used in the delete query
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);

        // After detaching, redirect back to the attach-subject.php page
        header("Location: attach-subject.php?student_id=" . $student_id);
        exit;
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
        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id); ?>"> <!-- subject_id passed in form -->

        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnConfirmDetach" type="submit" class="btn btn-danger">Detach Subject</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
