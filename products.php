<?php
/**
 * products.php - View/Entry point
 * Redirects to all_product.php following MVC structure
 * This file handles legacy links and bookmarks that reference products.php
 */

// Follow MVC pattern: require core settings
require_once 'settings/core.php';

// Redirect to all_product.php using 301 permanent redirect
http_response_code(301);
header('Location: all_product.php', true, 301);
exit();
?>

