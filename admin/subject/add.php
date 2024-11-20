<?php
    ob_start();
    session_start();
    $title = 'Add Subject';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();

    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "add.php";
    $pathStudents = "../student/register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    $subject_code = '';
    $subject_name = '';
    $arrErrors = [];

    if (isset($_POST['btnAdd'])) {
        $subject_code = sanitize($_POST['txtSubjectCode']);
        $subject_name = sanitize($_POST['txtSubjectName']);
        
        $arrErrors = validateSubjectData($subject_code, $subject_name);
        $duplicateErrors = checkDuplicateSubjectData($subject_code, $subject_name);
        $arrErrors = array_merge($arrErrors, $duplicateErrors);
        
        if (empty($arrErrors)) {
            addSubject($subject_code, $subject_name);
            $subject_code = '';
            $subject_name = '';
        }
    }

    $subjects = getSubjects();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Add a New Subject</h1>
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
            </ol>
        </nav>
    </div>

    <?php if (!empty($arrErrors)): ?>
        <?= displayErrors($arrErrors); ?>
    <?php endif; ?>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <div class="form-floating mb-3">
            <input type="number" class="form-control" id="txtSubjectCode" name="txtSubjectCode" placeholder="Subject Code" value="<?= $subject_code ?>">
            <label for="txtSubjectCode">Subject Code</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtSubjectName" name="txtSubjectName" placeholder="Subject Name" value="<?= $subject_name ?>">
            <label for="txtSubjectName">Subject Name</label>
        </div>

        <button name="btnAdd" type="submit" class="btn btn-primary w-100">Add Subject</button>
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
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($subjects) > 0): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?= sanitize($subject['subject_code']) ?></td>
                                <td><?= sanitize($subject['subject_name']) ?></td>
                                <td>
                                    <form method="POST" action="edit.php" class="d-inline">
                                        <input type="hidden" name="subject_code" value="<?= sanitize($subject['subject_code']) ?>">
                                        <button type="submit" name="btnEdit" class="btn btn-primary btn-sm">Edit</button>
                                    </form>

                                    <form method="POST" action="delete.php" class="d-inline">
                                        <input type="hidden" name="subject_code" value="<?= sanitize($subject['subject_code']) ?>">
                                        <button type="submit" name="btnDelete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No subjects found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require '../partials/footer.php'; ?>