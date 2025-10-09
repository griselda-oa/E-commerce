<?php
// login/logout.php
require_once '../settings/core.php';

// Clear user session data
clear_user_session();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page with logout message
header('Location: login.php?message=logout_success');
exit;
?>
