<?php
    session_start();
    $title = 'Dashboard';
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