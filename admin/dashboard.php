<!-- Template Files here -->
<?php
    ob_start();
    session_start();
    $title = 'Dashboard';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];
    
    require '../functions.php';
    guard();
    
    $pathDashboard = "dashboard.php";
    $pathLogout = "logout.php";
    $pathSubjects = "subject/add.php";
    $pathStudents = "student/register.php";

    require 'partials/header.php';
    require 'partials/side-bar.php';

    $studentCount = getStudentCount();
    $subjectCount = getSubjectCount();
    $result = getPassFailCount();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Dashboard</h1>        
    
    <div class="row mt-5">
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?= $subjectCount; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?= $studentCount; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                <div class="card-body text-danger">
                    <h5 class="card-title"><?=$result['failed']?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?=$result['passed']?></h5>
                </div>
            </div>
        </div>
    </div>    
</main>
<!-- Template Files here -->
 <?php require 'partials/footer.php'; ?>