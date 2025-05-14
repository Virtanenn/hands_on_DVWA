<?php

if( isset( $_GET[ 'Change' ] ) ) {
	$mysqli = $GLOBALS["___mysqli_ston"];
	
	// Get inputs
	$pass_curr = $_GET[ 'password_current' ];
	$pass_new  = $_GET[ 'password_new' ];
	$pass_conf = $_GET[ 'password_conf' ];

	// Escape and hash current password
  	$pass_curr = mysqli_real_escape_string($mysqli, $pass_curr);
    	$pass_curr_hashed = md5($pass_curr);

	// Get current user
	$current_user = dvwaCurrentUser();
	
	// Check if current password is correct using a prepared statement
	$stmt = mysqli_prepare($mysqli, "SELECT password FROM users WHERE user = ? AND password = ? LIMIT 1;");
	mysqli_stmt_bind_param($stmt, "ss", $current_user, $pass_curr_hashed);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_store_result($stmt);
	
	
	// Do the passwords match?
	if (mysqli_stmt_num_rows($stmt) === 1 && $pass_new === $pass_conf) {
		// Escape and hash new password
		$pass_new = mysqli_real_escape_string($mysqli, $pass_new);
		$pass_new_hashed = md5($pass_new);

		// Update password using prepared statement
		$update = mysqli_prepare($mysqli, "UPDATE users SET password = ? WHERE user = ?");
		mysqli_stmt_bind_param($update, "ss", $pass_new_hashed, $current_user);
		mysqli_stmt_execute($update);

		$html .= "<pre>Password Changed.</pre>";
    	} else {
        $html .= "<pre>Passwords did not match or current password incorrect.</pre>";
   	}
	
	// Close SELECT and UPDATE
	mysqli_stmt_close($stmt);
  	mysqli_stmt_close($update);
}

?>
