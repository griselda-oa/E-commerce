<?php
// actions/logout_action.php
require_once __DIR__ . '/../settings/core.php';

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: ../index.php');
exit;
?>
