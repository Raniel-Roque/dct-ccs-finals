<?php
    ob_start();
    session_start();
    $title = 'Register Student';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();

    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "../subject/add.php";
    $pathStudents = "register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    if (isset($_POST['btnRegister'])) {
        $student_id = htmlspecialchars(stripslashes(trim($_POST['txtStudentID'])));
        $first_name = htmlspecialchars(stripslashes(trim($_POST['txtFirstName'])));
        $last_name = htmlspecialchars(stripslashes(trim($_POST['txtLastName'])));

        $arrErrors = validateStudentData($student_id, $first_name, $last_name);
        $duplicateErrors = checkDuplicateStudentData($student_id);
        $arrErrors = array_merge($arrErrors, $duplicateErrors);

        if (empty($arrErrors)) {
            $con = getDatabaseConnection();
            $stmt = $con->prepare("INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $student_id, $first_name, $last_name);
            $stmt->execute();
            $stmt->close();
            mysqli_close($con);

            $student_id = '';
            $first_name = '';
            $last_name = '';
        }
    }

    $con = getDatabaseConnection();
    $stmt = $con->prepare("SELECT * FROM students");
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    mysqli_close($con);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Register a New Student</h1>
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Register Student</li>
            </ol>
        </nav>
    </div>

    <?php if (!empty($arrErrors)): ?>
        <?= displayErrors($arrErrors); ?>
    <?php endif; ?>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <div class="form-floating mb-3">
            <input type="number" class="form-control" id="txtStudentID" name="txtStudentID" placeholder="Student ID" value="<?= isset($student_id) ? $student_id : '' ?>">
            <label for="txtStudentID">Student ID</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtFirstName" name="txtFirstName" placeholder="First Name" value="<?= isset($first_name) ? $first_name : '' ?>">
            <label for="txtFirstName">First Name</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtLastName" name="txtLastName" placeholder="Last Name" value="<?= isset($last_name) ? $last_name : '' ?>">
            <label for="txtLastName">Last Name</label>
        </div>

        <button name="btnRegister" type="submit" class="btn btn-primary w-100">Register Student</button>
    </form>

    <div class="mt-3">
        <div class="border border-secondary-1 p-5 mb-4">
            <h5>Student List</h5>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['student_id']) ?></td>
                                <td><?= htmlspecialchars($student['first_name']) ?></td>
                                <td><?= htmlspecialchars($student['last_name']) ?></td>
                                <td>
                                    <form method="POST" action="edit.php" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?= $student['student_id'] ?>">
                                        <button type="submit" name="btnEdit" class="btn btn-primary btn-sm">Edit</button>
                                    </form>

                                    <form method="POST" action="delete.php" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?= $student['student_id'] ?>">
                                        <button type="submit" name="btnDelete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>

                                    <form method="GET" action="attach-subject.php" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?= $student['student_id']; ?>">
                                        <button type="submit" name="btnAttach" class="btn btn-warning btn-sm">Attach Subject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No students found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require '../partials/footer.php'; ?>
