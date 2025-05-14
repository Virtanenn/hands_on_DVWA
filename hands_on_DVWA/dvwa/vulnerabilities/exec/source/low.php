<?php

if( isset( $_POST['Submit'] ) ) {
    // Get input
    $target = trim($_REQUEST['ip']);

    // Validate input (allow only valid IP addresses)
    if (filter_var($target, FILTER_VALIDATE_IP)) {
        // Determine OS and execute the ping command securely
        if( stristr( php_uname( 's' ), 'Windows NT' ) )  {
            // Windows
            $cmd = shell_exec('ping ' . escapeshellarg($target));
        } else {
            // *nix (Linux/macOS)
            $cmd = shell_exec('ping -c 4 ' . escapeshellarg($target));
        }

        // Store output for display
        $html .= "<pre>{$cmd}</pre>";
    } else {
        $html .= "<p style='color:red;'>Invalid IP address.</p>";
    }
}

?>

