<?php

if (isset($_REQUEST['Submit'])) {
    $id = $_REQUEST['id'];

    // Validate that ID is a number
    if (!is_numeric($id)) {
        $html .= "<pre>Invalid ID: Must be a number.</pre>";
    } else {
        switch ($_DVWA['SQLI_DB']) {
            case MYSQL:
                $mysqli = $GLOBALS["___mysqli_ston"];

                // Use a prepared statement
                $stmt = mysqli_prepare($mysqli, "SELECT first_name, last_name FROM users WHERE user_id = ?");
                if (!$stmt) {
                    $html .= "<pre>Error preparing statement: " . mysqli_error($mysqli) . "</pre>";
                    break;
                }

                $id = (int)$id; // Ensure it's an integer for binding
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $first, $last);

                // Fetch and display results
                $found = false;
                while (mysqli_stmt_fetch($stmt)) {
                    $found = true;
                    $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
                }

                if (!$found) {
                    $html .= "<pre>No user found with ID: {$id}</pre>";
                }

                mysqli_stmt_close($stmt);
                mysqli_close($mysqli);
                break;

            case SQLITE:
                global $sqlite_db_connection;

                // Use SQLite prepared statement
                $stmt = $sqlite_db_connection->prepare("SELECT first_name, last_name FROM users WHERE user_id = :id");
                if (!$stmt) {
                    $html .= "<pre>Error preparing SQLite statement.</pre>";
                    break;
                }

                $stmt->bindValue(':id', (int)$id, SQLITE3_INTEGER);

                try {
                    $results = $stmt->execute();
                } catch (Exception $e) {
                    $html .= "<pre>Database error: " . htmlspecialchars($e->getMessage()) . "</pre>";
                    break;
                }

                $found = false;
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                    $found = true;
                    $first = $row['first_name'];
                    $last  = $row['last_name'];
                    $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
                }

                if (!$found) {
                    $html .= "<pre>No user found with ID: {$id}</pre>";
                }

                break;
        }
    }
}
?>

