<?php   
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
        // Get database connection
        $con = getDatabaseConnection();
    
        // Use prepared statements to prevent SQL injection
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Check if a user was found
        if ($result->num_rows > 0) {
            $stmt->close();
            mysqli_close($con);
            return true; // Login successful
        }
    
        // Close connections
        $stmt->close();
        mysqli_close($con);
        return false; // Invalid credentials
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

    function getBaseURL() {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'];
    }

    // STUDENT MANAGEMENT

    function validateStudentData($student_data) {
        $arrErrors = [];
    
        // Validate Student ID
        if (empty($student_data['student_id'])) {
            $arrErrors[] = "Student ID is required.";
        }
    
        // Validate First Name
        if (empty($student_data['first_name'])) {
            $arrErrors[] = "First name is required.";
        }
    
        // Validate Last Name
        if (empty($student_data['last_name'])) {
            $arrErrors[] = "Last name is required.";
        }
    
        return $arrErrors;
    }

    function checkDuplicateStudentData($student_data) {
        $arrErrors = [];

        // Check for duplicate student ID or name
        foreach ($_SESSION['students'] as $student) {
            if ($student['student_id'] === $student_data['student_id']) {
                $arrErrors[] = "Duplicate Student ID.";
                break;
            }
        }

        return $arrErrors;
    }
    
    function getSelectedStudentIndex($student_id) {
        foreach ($_SESSION['students'] as $index => $student) {
            if ($student['student_id'] === $student_id) {
                return $index;
            }
        }
        return null;  // Return null if student is not found
    }

    function getSelectedStudentData($index) {
        return isset($_SESSION['students'][$index]) ? $_SESSION['students'][$index] : null;
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
    
    // Check for Duplicate Subject in Database
    function checkDuplicateSubjectData($subject_code, $subject_name) {
        $arrErrors = [];
        $con = getDatabaseConnection();

        // Check if the subject already exists in the database
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

    function getSelectedSubjectIndex($subject_code) {
        foreach ($_SESSION['subjects'] as $index => $subject) {
            if ($subject['subject_code'] === $subject_code) {
                return $index;
            }
        }
        return null;  // Return null if subject is not found
    }
    
    function getSelectedSubjectData($index) {
        return isset($_SESSION['subjects'][$index]) ? $_SESSION['subjects'][$index] : null;
    }    
    
    function validateAttachedSubject($subject_data) {
        $arrErrors = [];
    
        if (empty($subject_data)) {
            $arrErrors[] = "Please select at least one subject to attach.";
        }
    
        return $arrErrors;
    }    
?>