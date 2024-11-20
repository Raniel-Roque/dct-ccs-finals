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
        $student_id = $_POST['student_id'];

        // Get student data from the database
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

    if (isset($_POST['btnUpdate'])) {
        $first_name = htmlspecialchars(stripslashes(trim($_POST['first_name'])));
        $last_name = htmlspecialchars(stripslashes(trim($_POST['last_name'])));

        $arrErrors = validateStudentData($student_id, $first_name, $last_name);

        if (empty($arrErrors)) {
            // Update student information in the database
            $con = getDatabaseConnection();
            $stmt = $con->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?");
            $stmt->bind_param("sss", $first_name, $last_name, $student_id);
            $stmt->execute();
            $stmt->close();
            mysqli_close($con);

            header("Location: register.php");
            exit;
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
            <input type="number" class="form-control bg-light" id="txtStudentID" name="student_id" value="<?= htmlspecialchars($student['student_id']); ?>" readonly>
            <label for="txtStudentID">Student ID</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtFirstName" name="first_name" placeholder="Enter First Name" value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : htmlspecialchars($student['first_name']); ?>">
            <label for="txtFirstName">First Name</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtLastName" name="last_name" placeholder="Enter Last Name" value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : htmlspecialchars($student['last_name']); ?>">
            <label for="txtLastName">Last Name</label>
        </div>

        <button name="btnUpdate" type="submit" class="btn btn-primary w-100">Update Student</button>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
