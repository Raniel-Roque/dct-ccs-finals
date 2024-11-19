<?php
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
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Edit Subject</h1>

</main>

<?php require '../partials/footer.php'; ?>
