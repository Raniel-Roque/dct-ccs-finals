<?php   
    // DB CONNECTION
    function getDatabaseConnection() {
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "dct-ccs-finals";

        // Establish a connection to the database
        $con = mysqli_connect($host, $username, $password, $database);

        // Check the connection
        if ($con === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        return $con;
    }

    // USER AUTHENTICATION
    function validateLoginCredentials($email, $password) {    
        $arrErrors = [];
        
        if (empty($email)) {
            $arrErrors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $arrErrors[] = 'Invalid email.';
        }
    
        if (empty($password)) {
            $arrErrors[] = 'Password is required.';
        }
    
        return $arrErrors;
    }
    
    function checkLoginCredentials($email, $password) {
        $con = getDatabaseConnection();
    
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $stmt->close();
            mysqli_close($con);
            return true; 
        }
    
        $stmt->close();
        mysqli_close($con);
        return false; 
    }
    

    function checkUserSessionIsActive() {
        if (isset($_SESSION['email']) && isset($_SESSION['current_page'])) {
            header("Location: " . $_SESSION['current_page']);
        }
    }

    // SESSION MANAGEMENT
    function guard() {
        if (!isset($_SESSION['email'])) {
            header("Location: " . getBaseURL());
        }
    }
    
    // ERROR HANDLING
    function displayErrors($errors) {
        $output = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        $output .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $output .= '<strong>System Errors</strong>';
        $output .= '<hr>';
        $output .= '<ul>';
    
        foreach ($errors as $error) {
            $output .= renderErrorsToView($error);
        }
    
        $output .= '</ul>';
        $output .= '</div>';

        return $output;
    }

    function renderErrorsToView($error) {
        if (is_array($error)) {
            return '<li>' . implode(', ', $error) . '</li>';
        } elseif (!empty($error)) {
            return '<li>' . $error . '</li>';
        }
        
        return '';
    }

    // STUDENT MANAGEMENT
    function validateStudentData($student_id, $first_name, $last_name) {
        $arrErrors = [];

        // Validate Student ID
        if (empty($student_id)) {
            $arrErrors[] = "Student ID is required.";
        }

        // Validate First Name
        if (empty($first_name)) {
            $arrErrors[] = "First name is required.";
        }

        // Validate Last Name
        if (empty($last_name)) {
            $arrErrors[] = "Last name is required.";
        }

        return $arrErrors;
    }

    function checkDuplicateStudentData($student_id) {
        $arrErrors = [];
        $con = getDatabaseConnection();

        // Check if the student ID already exists in the database
        $stmt = $con->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $arrErrors[] = "Duplicate Student ID.";
        }

        $stmt->close();
        mysqli_close($con);

        return $arrErrors;
    }

    function getStudentData($student_id) {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student_data = $result->fetch_assoc();
        $stmt->close();
        mysqli_close($con);

        return $student_data;
    }

    function getAllStudents() {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM students ORDER BY student_id ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $students = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        mysqli_close($con);
    
        return $students;
    }

    function getStudentCount() {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT COUNT(*) AS student_count FROM students");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        mysqli_close($con);
        
        return $data['student_count'];
    }

    function registerStudent($student_id, $first_name, $last_name) {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $student_id, $first_name, $last_name);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);
    }

    function updateStudent($student_id, $first_name, $last_name) {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?");
        $stmt->bind_param("sss", $first_name, $last_name, $student_id);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);
    }    

    function deleteStudentAndSubjects($student_id) {
        $con = getDatabaseConnection();

        $stmt = $con->prepare("DELETE FROM students_subjects WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $con->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        mysqli_close($con);
    }
    
    // SUBJECT MANAGEMENT
    function validateSubjectData($subject_code, $subject_name) {
        $arrErrors = [];
    
        if (empty($subject_code)) {
            $arrErrors[] = "Subject code is required.";
        }
    
        if (empty($subject_name)) {
            $arrErrors[] = "Subject name is required.";
        }
    
        return $arrErrors;
    }
    
    function checkDuplicateSubjectData($subject_code, $subject_name) {
        $arrErrors = [];
        $con = getDatabaseConnection();

        $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code = ? OR subject_name = ?");
        $stmt->bind_param("ss", $subject_code, $subject_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $arrErrors[] = "Duplicate Subject Code or Subject Name.";
        }

        $stmt->close();
        mysqli_close($con);

        return $arrErrors;
    }

    function checkDuplicateSubjectDataForEdit($subject_code, $subject_name) {
        $arrErrors = [];
        $con = getDatabaseConnection();
    
        $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_name = ? AND subject_code != ?");
        $stmt->bind_param("ss", $subject_name, $subject_code);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $arrErrors[] = "Duplicate Subject Name.";
        }
    
        $stmt->close();
        mysqli_close($con);
    
        return $arrErrors;
    }    

    function validateAttachedSubject($subject_data) {
        $arrErrors = [];
    
        if (empty($subject_data)) {
            $arrErrors[] = "Please select at least one subject to attach.";
        }
    
        return $arrErrors;
    }    

    function getSubjects() {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM subjects ORDER BY subject_code ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $subjects = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        mysqli_close($con);

        return $subjects;
    }

    function getSubjectByCode($subject_code) {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code = ?");
        $stmt->bind_param("s", $subject_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $subject = $result->fetch_assoc();
        $stmt->close();
        mysqli_close($con);
    
        return $subject; 
    }    

    function getSubjectCount() {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT COUNT(*) AS subject_count FROM subjects");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        mysqli_close($con);
        
        return $data['subject_count'];
    }

    function addSubject($subject_code, $subject_name) {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $subject_code, $subject_name);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);
    }

    function updateSubject($subject_code, $subject_name) {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("UPDATE subjects SET subject_name = ? WHERE subject_code = ?");
        $stmt->bind_param("ss", $subject_name, $subject_code);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);
    }    

    function deleteSubjectByCode($subject_code) {
        $con = getDatabaseConnection();
    
        $stmt = $con->prepare("DELETE FROM students_subjects WHERE subject_id = ?");
        $stmt->bind_param("s", $subject_code);
        $stmt->execute();
        $stmt->close();
    
        $stmt = $con->prepare("DELETE FROM subjects WHERE subject_code = ?");
        $stmt->bind_param("s", $subject_code);
        $stmt->execute();
        $stmt->close();
    
        mysqli_close($con);
    }     

    //GRADE MANAGEMENT
    function validateGrade($grade) {
        $arrErrors = [];

        if(empty($grade)) {
            $arrErrors[] = "Grade is Required";
        } else if ($grade < 65 || $grade > 100) {
            $arrErrors[] = "Grade must be between 65 and 100.";
        }

        return $arrErrors;
    }

    function handleGradeAssignment($student_id, $subject_id, $grade) {    
        $con = getDatabaseConnection();
        $stmt = $con->prepare("UPDATE students_subjects SET grade = ? WHERE student_id = ? AND subject_id = ?");
        $stmt->bind_param("dii", $grade, $student_id, $subject_id);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);
    }    

    function getPassFailCount() {
        $con = getDatabaseConnection();
        $passedCount = 0;
        $failedCount = 0;
    
        $students = getAllStudents();
    
        foreach ($students as $student) {
            $student_id = $student['student_id'];
    
            $stmt = $con->prepare("SELECT grade FROM students_subjects WHERE student_id = ?");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $totalGrade = 0;
            $subjectCount = 0;
    
            while ($row = $result->fetch_assoc()) {
                $totalGrade += $row['grade'];
                $subjectCount++;
            }
    
            $stmt->close();

            if ($subjectCount > 0) {
                $averageGrade = $totalGrade / $subjectCount;
    
                if ($averageGrade >= 75) {
                    $passedCount++;
                } else {
                    $failedCount++;
                }
            }
        }
    
        mysqli_close($con);
    
        return [
            'passed' => $passedCount,
            'failed' => $failedCount
        ];
    }    

    //ATTACH AND DETTACH
    function getStudentSubjectDetails($student_id, $subject_id) {
        $con = getDatabaseConnection();
    
        $stmt = $con->prepare("SELECT students.student_id, 
                                      students.first_name, 
                                      students.last_name, 
                                      subjects.subject_code, 
                                      subjects.subject_name, 
                                      students_subjects.grade
                               FROM students
                               JOIN students_subjects ON students.student_id = students_subjects.student_id
                               JOIN subjects ON subjects.subject_code = students_subjects.subject_id
                               WHERE students_subjects.student_id = ? AND students_subjects.subject_id = ?");
        $stmt->bind_param("ii", $student_id, $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $stmt->close();
            mysqli_close($con);
            return $data;
        }
    
        $stmt->close();
        mysqli_close($con);
        return null;
    }    

    function getAttachedSubjects($student_id) {
        $attachedSubjects = [];
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT subjects.subject_code, subjects.subject_name, students_subjects.grade 
                            FROM subjects 
                            JOIN students_subjects ON subjects.subject_code = students_subjects.subject_id
                            WHERE students_subjects.student_id = ?
                            ORDER BY subjects.subject_code ASC");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $attachedSubjects[] = $row;
        }
        $stmt->close();
        mysqli_close($con);

        return $attachedSubjects;
    }

    function getAvailableSubjects($student_id) {
        $availableSubjects = [];
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code NOT IN (SELECT subject_id FROM students_subjects WHERE student_id = ?) ORDER BY subject_code ASC");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $availableSubjects[] = $row;
        }
        $stmt->close();
        mysqli_close($con);

        return $availableSubjects;
    }

    function attachSubjectsToStudent($student_id, $subject_codes) {
        $con = getDatabaseConnection();
        foreach ($subject_codes as $subject_code) {
            $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code = ?");
            $stmt->bind_param("i", $subject_code);
            $stmt->execute();
            $subject_data = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($subject_data) {
                $stmt = $con->prepare("INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, 0)");
                $stmt->bind_param("ii", $student_id, $subject_code);
                $stmt->execute();
                $stmt->close();
            }
        }
        mysqli_close($con);
    }

    function detachSubjectFromStudent($student_id, $subject_id) {
        $con = getDatabaseConnection();
        $stmt = $con->prepare("DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $student_id, $subject_id);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);
    }    

    // OTHER FUNCTIONS
    function redirectTo($url) {
        header("Location: " . $url);
        exit;
    }

    function sanitize($data) {
        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }    

    function getBaseURL() {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'];
    }
?>