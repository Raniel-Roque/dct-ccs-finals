<?php
    ob_start();
    session_start();
    $title = 'Edit Student';
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
        $student_id = sanitize($_POST['student_id']);

        $student = getStudentData($student_id);

        if (!$student) {
            redirectTo($pathStudents);
        }
    } else {
        redirectTo($pathStudents);
    }

    if (isset($_POST['btnUpdate'])) {
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);

        $arrErrors = validateStudentData($student_id, $first_name, $last_name);

        if (empty($arrErrors)) {
            updateStudent($student_id, $first_name, $last_name);
            redirectTo($pathStudents);
        }
    }
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Edit Student</h1>
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
            </ol>
        </nav>
    </div>

    <?php if (!empty($arrErrors)): ?>
        <?= displayErrors($arrErrors); ?>
    <?php endif; ?>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <div class="form-floating mb-3">
            <input type="number" class="form-control bg-light" id="txtStudentID" name="student_id" value="<?= sanitize($student['student_id']); ?>" readonly>
            <label for="txtStudentID">Student ID</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtFirstName" name="first_name" placeholder="Enter First Name" value="<?= isset($_POST['first_name']) ? sanitize($_POST['first_name']) : sanitize($student['first_name']); ?>">
            <label for="txtFirstName">First Name</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtLastName" name="last_name" placeholder="Enter Last Name" value="<?= isset($_POST['last_name']) ? sanitize($_POST['last_name']) : sanitize($student['last_name']); ?>">
            <label for="txtLastName">Last Name</label>
        </div>

        <button name="btnUpdate" type="submit" class="btn btn-primary w-100">Update Student</button>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
