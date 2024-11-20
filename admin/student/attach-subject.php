<?php
    ob_start();
    session_start();
    $title = 'Attach Subject to Student';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();

    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "../subject/add.php";
    $pathStudents = "register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    if (isset($_GET['student_id'])) {
        $student_id = $_GET['student_id'];
        
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student_data = $result->fetch_assoc();
        $stmt->close();
        mysqli_close($con);
        
        if ($student_data) {
            $first_name = $student_data['first_name'];
            $last_name = $student_data['last_name'];
            $full_name = $first_name . ' ' . $last_name;
        } else {
            header("Location: register.php");
            exit;
        }
    } else {
        header("Location: register.php");
        exit;
    }

    $arrErrors = [];

    if (isset($_POST['btnConfirmAttach'])) {
        $subject_codes = $_POST['subject_ids'] ?? null;

        $arrErrors = validateAttachedSubject($subject_codes);

        if (empty($arrErrors)) {
            if (isset($subject_codes) && !empty($subject_codes)) {
                $con = getDatabaseConnection();
                foreach ($subject_codes as $subject_code) {
                    $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code = ?");
                    $stmt->bind_param("s", $subject_code); // Use subject_code
                    $stmt->execute();
                    $subject_data = $stmt->get_result()->fetch_assoc();
                    $stmt->close();

                    if ($subject_data) {
                        // Insert new subjects with default grade of 0, using subject_code
                        $stmt = $con->prepare("INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, 0)");
                        $stmt->bind_param("ss", $student_id, $subject_code); // Use student_id as string and subject_code as string
                        $stmt->execute();
                        $stmt->close();
                    }
                }
                mysqli_close($con);
            }
        }
    }

    $attachedSubjects = [];
    $con = getDatabaseConnection();
    $stmt = $con->prepare("SELECT subjects.subject_code, subjects.subject_name, students_subjects.grade 
                           FROM subjects 
                           JOIN students_subjects ON subjects.subject_code = students_subjects.subject_id
                           WHERE students_subjects.student_id = ?");
    $stmt->bind_param("s", $student_id); // Use student_id as string
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $attachedSubjects[] = $row;
    }
    $stmt->close();
    mysqli_close($con);

    $availableSubjects = [];
    $con = getDatabaseConnection();
    $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code NOT IN (SELECT subject_id FROM students_subjects WHERE student_id = ?)");
    $stmt->bind_param("s", $student_id); // Use student_id as string
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $availableSubjects[] = $row;
    }
    $stmt->close();
    mysqli_close($con);
?>

<main class="container justify-content-between align-items-center col-8 mt-4">
    <h2 class="mt-4">Attach Subject to Student</h2>
    <div class="mt-5 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attach Subject to Student</li>
            </ol>
        </nav>
    </div>

    <?php if (!empty($arrErrors)): ?>
        <?= displayErrors($arrErrors); ?>
    <?php endif; ?>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <h3>Selected Student Information</h3>
        <ul>
            <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></li>
            <li><strong>Name:</strong> <?php echo htmlspecialchars($full_name); ?></li>
        </ul>

        <hr>

        <?php if (count($availableSubjects) > 0): ?>
            <?php foreach ($availableSubjects as $subject): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="subject_ids[]" value="<?= htmlspecialchars($subject['subject_code']); ?>" id="subject_<?= htmlspecialchars($subject['subject_code']); ?>">
                    <label class="form-check-label" for="subject_<?= htmlspecialchars($subject['subject_code']); ?>">
                        <?= htmlspecialchars($subject['subject_code']); ?> - <?= htmlspecialchars($subject['subject_name']); ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <div class="mt-4">
                <button name="btnConfirmAttach" type="submit" class="btn btn-primary">Attach Subject</button>
            </div>
        <?php else: ?>
            <p>No subjects available to attach.</p>
        <?php endif; ?>
    </form>

    <div class="mt-3">
        <div class="border border-secondary-1 p-5 mb-4">
            <h5>Subject List</h5>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Grade</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($attachedSubjects) > 0): ?>
                        <?php foreach ($attachedSubjects as $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <?php echo ($subject['grade'] == 0 ? '--.--' : htmlspecialchars($subject['grade'])); ?>
                                </td>
                                <td>
                                    <form method="POST" action="dettach-subject.php" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id); ?>">
                                        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['subject_code']); ?>"> <!-- subject_code here -->
                                        <button type="submit" name="btnDettach" class="btn btn-danger btn-sm">Detach Subject</button>
                                    </form>
                                    <form method="POST" action="assign-grade.php" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id); ?>">
                                        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['subject_code']); ?>"> 
                                        <button type="submit" name="btnAssignGrade" class="btn btn-success btn-sm">Assign Grade</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No subjects attached</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require '../partials/footer.php'; ?>