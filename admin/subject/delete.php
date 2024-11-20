<?php
    ob_start();
    session_start();
    $title = 'Delete Subject';
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
        $subject = getSubjectByCode($subject_code);

        if (!$subject) {
            redirectTo('add.php');
        }
    } else {
        redirectTo('add.php');
    }

    if (isset($_POST['btnConfirmDelete'])) {
        deleteSubjectByCode($subject_code);
        redirectTo('add.php');
    }

    if (isset($_POST['btnCancel'])) {
        redirectTo('add.php');
    }
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Delete Subject</h1>
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="add.php" class="text-decoration-none">Add Subject</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
            </ol>
        </nav>
    </div>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <p>Are you sure you want to delete the following subject record?</p>

        <ul>
            <li><strong>Subject Code:</strong> <?= sanitize($subject['subject_code']); ?> </li>
            <li><strong>Subject Name:</strong> <?= sanitize($subject['subject_name']); ?> </li>
        </ul>

        <input type="hidden" name="subject_code" value="<?= sanitize($subject['subject_code']); ?>">

        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnConfirmDelete" type="submit" class="btn btn-primary">Delete Subject Record</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>