// Settings/core.php
<?php
session_start();


//for header redirection
ob_start();

// Authentication & authorisation helpers

// Check whether a user session exists
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}


//function to get user ID
function currentUserId(): ?int {
    if (!isLoggedIn()) {
        return null;
    }
    return (int)$_SESSION['user_id'];
}


//function to check for role (admin, customer, etc)
function currentUserRole(): ?int {
    if (!isset($_SESSION['user_role'])) {
        return null;
    }
    return (int)$_SESSION['user_role'];
}

// Admin = 1, Regular user = 2
function isAdmin(): bool {
    return isLoggedIn() && currentUserRole() === 1;
}



?>