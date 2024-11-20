<?php
    ob_start();
    session_start();
    $title = 'Delete Student';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();

    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "../subject/add.php";
    $pathStudents = "register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    if (isset($_POST['student_id'])) {
        $student_id = $_POST['student_id'];

        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
        mysqli_close($con);

        if (!$student) {
            header("Location: register.php");
            exit;
        }
    } else {
        header("Location: register.php");
        exit;
    }

    if (isset($_POST['btnConfirmDelete'])) {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);

        header("Location: register.php");
        exit;
    }

    if (isset($_POST['btnCancel'])) {
        header("Location: register.php");
        exit;
    }
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Delete Student</h1>
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
            </ol>
        </nav>
    </div>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <p>Are you sure you want to delete the following student record?</p>

        <ul>
            <li><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']); ?> </li>
            <li><strong>First Name:</strong> <?= htmlspecialchars($student['first_name']); ?> </li>
            <li><strong>Last Name:</strong> <?= htmlspecialchars($student['last_name']); ?> </li>
        </ul>

        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']); ?>">

        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnConfirmDelete" type="submit" class="btn btn-danger">Delete Student Record</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
