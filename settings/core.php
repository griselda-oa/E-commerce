<?php
/**
 * Core Session Management and Authentication Functions
 * Part 1: Session Management & Admin Privileges
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if a user has administrative privileges
 * @return bool True if user is admin (user_role = 1), false otherwise
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

/**
 * Get the current user's ID
 * @return int|null User ID or null if not logged in
 */
function get_user_id() {
    return is_logged_in() ? $_SESSION['user_id'] : null;
}

/**
 * Get the current user's name
 * @return string|null User name or null if not logged in
 */
function get_user_name() {
    return is_logged_in() ? $_SESSION['user_name'] : null;
}

/**
 * Get the current user's first name
 * @return string|null User's first name or null if not logged in
 */
function get_user_first_name() {
    if (!is_logged_in()) {
        return null;
    }
    
    $fullName = $_SESSION['user_name'] ?? '';
    $nameParts = explode(' ', trim($fullName));
    return $nameParts[0] ?? $fullName;
}

/**
 * Get the current user's email
 * @return string|null User email or null if not logged in
 */
function get_user_email() {
    return is_logged_in() ? $_SESSION['user_email'] : null;
}

/**
 * Get the current user's role
 * @return int|null User role or null if not logged in
 */
function get_user_role() {
    return is_logged_in() ? $_SESSION['user_role'] : null;
}

/**
 * Check if user has a specific role
 * @param int $role Role to check for
 * @return bool True if user has the role, false otherwise
 */
function has_role($role) {
    return get_user_role() === $role;
}

/**
 * Require user to be logged in
 * Redirects to login page if not logged in
 * @param string $redirect_url Optional redirect URL after login
 */
function require_login($redirect_url = null) {
    if (!is_logged_in()) {
        // Use absolute path from document root to avoid relative path issues
        // Get the script path relative to document root
        $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Get the directory of the script
        $script_dir = dirname($script_name);
        
        // If we're in admin folder, remove /admin from the path
        // Example: /~griselda.owusu/admin -> /~griselda.owusu
        if (strpos($script_dir, '/admin') !== false) {
            $script_dir = str_replace('/admin', '', $script_dir);
        }
        
        // Build absolute path to login from document root
        $login_url = rtrim($script_dir, '/') . '/login/login.php';
        
        // Remove any double slashes (except at the start)
        $login_url = preg_replace('#([^:])//+#', '$1/', $login_url);
        
        if ($redirect_url) {
            $login_url .= '?redirect=' . urlencode($redirect_url);
        }
        
        header('Location: ' . $login_url);
        exit();
    }
}

/**
 * Require user to be admin
 * Redirects to login page if not logged in or not admin
 * @param string $redirect_url Optional redirect URL after login
 */
function require_admin($redirect_url = null) {
    require_login($redirect_url);
    
    if (!is_admin()) {
        if ($redirect_url) {
            header('Location: ' . $redirect_url);
        } else {
            header('Location: ../index.php?error=access_denied');
        }
        exit();
    }
}

/**
 * Set user session data
 * @param array $user_data User data array
 */
function set_user_session($user_data) {
    $_SESSION['user_id'] = $user_data['user_id'];
    $_SESSION['user_name'] = $user_data['user_name'];
    $_SESSION['user_email'] = $user_data['user_email'];
    $_SESSION['user_role'] = $user_data['user_role'];
    $_SESSION['is_admin'] = $user_data['is_admin'] ?? 0;
}

/**
 * Clear user session data
 */
function clear_user_session() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_role']);
    unset($_SESSION['is_admin']);
}

/**
 * Get user display name (first name or full name)
 * @param bool $use_first_name_only Whether to return only first name
 * @return string User display name
 */
function get_user_display_name($use_first_name_only = true) {
    if (!is_logged_in()) {
        return 'Guest';
    }
    
    if ($use_first_name_only) {
        return get_user_first_name();
    }
    
    return get_user_name();
}

/**
 * Check if current page requires admin access
 * @return bool True if page requires admin access
 */
function page_requires_admin() {
    $admin_pages = [
        'admin/category.php',
        'admin/dashboard.php',
        'admin/product.php'
    ];
    
    $current_page = $_SERVER['PHP_SELF'];
    foreach ($admin_pages as $admin_page) {
        if (strpos($current_page, $admin_page) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Initialize page with proper authentication
 * Checks if page requires admin access and redirects accordingly
 */
function init_page() {
    if (page_requires_admin()) {
        require_admin();
    }
}

// Auto-initialize page if this file is included
// Only auto-init if user is logged in (to avoid redirect loops)
if (basename($_SERVER['PHP_SELF']) !== 'core.php' && is_logged_in()) {
    init_page();
}
?>