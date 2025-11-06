<?php
/**
 * products.php - Redirect to all_product.php for backward compatibility
 * This file handles legacy links and bookmarks that reference products.php
 */

// Use 301 permanent redirect for SEO and browser caching
http_response_code(301);

// Get the base URL to ensure proper redirect
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? '';
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$base_url = $protocol . $host . $script_dir;
$base_url = rtrim($base_url, '/');

// Redirect to all_product.php
$redirect_url = $base_url . '/all_product.php';
header('Location: ' . $redirect_url, true, 301);
exit();
?>

