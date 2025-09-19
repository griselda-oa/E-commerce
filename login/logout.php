<?php
// login/logout.php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page with logout message
header('Location: login.php?message=logout_success');
exit;
?>
