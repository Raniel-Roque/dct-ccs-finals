<?php
    ob_start();
    session_start();
    $title = 'Edit Subject';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();

    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "add.php";
    $pathStudents = "../student/register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    if (isset($_POST['subject_code'])) {
        $subject_code = $_POST['subject_code'];
        
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code = ?");
        $stmt->bind_param("s", $subject_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $subject = $result->fetch_assoc();
        $stmt->close();
        mysqli_close($con);

        if (!$subject) {
            header("Location: add.php");
            exit;
        }
    } else {
        header("Location: add.php");
        exit;
    }

    if (isset($_POST['btnUpdate'])) {
        $subject_name = htmlspecialchars(stripslashes(trim($_POST['subject_name'])));

        $arrErrors = validateSubjectData($subject_code, $subject_name);

        if (empty($arrErrors)) {
            $con = getDatabaseConnection();
            $stmt = $con->prepare("UPDATE subjects SET subject_name = ? WHERE subject_code = ?");
            $stmt->bind_param("ss", $subject_name, $subject_code);
            $stmt->execute();
            $stmt->close();
            mysqli_close($con);

            header("Location: add.php");
            exit;
        }
    }
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Edit Subject</h1>
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="add.php" class="text-decoration-none">Add Subject</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
            </ol>
        </nav>
    </div>

    <?php if (!empty($arrErrors)): ?>
        <?= displayErrors($arrErrors); ?>
    <?php endif; ?>
    
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <div class="form-floating mb-3">
            <input type="number" class="form-control bg-light" id="txtSubjectCode" name="subject_code" value="<?= htmlspecialchars($subject['subject_code']); ?>" readonly>
            <label for="txtSubjectCode">Subject Code</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtSubjectName" name="subject_name" placeholder="Enter Subject Name" value="<?= htmlspecialchars($subject['subject_name']); ?>">
            <label for="txtSubjectName">Subject Name</label>
        </div>

        <button name="btnUpdate" type="submit" class="btn btn-primary w-100">Update Subject</button>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
