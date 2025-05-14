<?php
session_start(); // Start session to track failed login attempts and timestamps

// Initialize failed attempts and last failed attempt time if not already set
if (!isset($_SESSION['failed'])) { 
    $_SESSION['failed'] = 0;  // Track number of failed login attempts
    $_SESSION['last'] = 0;    // Track the last failed attempt time
}

$html = ""; // Initialize HTML output variable

// Check if the login form has been submitted
if (isset($_GET['Login'])) {
    // Get username and password from the form
    $user = $_GET['username'];
    $pass = md5($_GET['password']); // Hash the password (note: MD5 is not secure for production)

    // Check if user is locked out (3 or more failed attempts within 60 seconds)
    if ($_SESSION['failed'] >= 3 && (time() - $_SESSION['last']) < 60) {
        // Display lockout message if the user is locked out
        $html .= "<pre><br />You are locked out. Please try again in " . (60 - (time() - $_SESSION['last'])) . " seconds.</pre>";
    } else {
        // Reset failed attempts if lockout time has expired (more than 60 seconds)
        if ($_SESSION['failed'] >= 3 && (time() - $_SESSION['last']) >= 60) { 
            $_SESSION['failed'] = 0; 
        }

        // Query the database to check the username and password
        $query  = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";
        $result = mysqli_query($GLOBALS["___mysqli_ston"], $query) or die('<pre>' . (is_object($GLOBALS["___mysqli_ston"]) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');

        // Check if the login credentials are correct
        if ($result && mysqli_num_rows($result) == 1) {
            // Login successful, reset failed attempts counter
            $_SESSION['failed'] = 0;

            // Fetch user details from the database
            $row    = mysqli_fetch_assoc($result);
            $avatar = $row["avatar"];

            // Display welcome message and user avatar
            $html .= "<p>Welcome to the password protected area {$user}</p>";
            $html .= "<img src=\"{$avatar}\" />"; // Display user's avatar
        } else {
            // Login failed, increment failed attempts counter and update last attempt time
	    sleep( rand( 2, 4 ) );
    	    $_SESSION['failed']++;
    	    $_SESSION['last'] = time(); // Record the time of the failed attempt

    	    // Display error message for incorrect credentials
    	    $html .= "<pre><br />Username and/or password incorrect.</pre>";
        }

        // Close the database connection
        ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
    }
}
?>

