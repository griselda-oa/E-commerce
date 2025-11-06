<?php
/**
 * products.php - Redirect to all_product.php for backward compatibility
 * This file handles legacy links and bookmarks that reference products.php
 */

// Use 301 permanent redirect for SEO and browser caching
http_response_code(301);
header('Location: all_product.php', true, 301);
exit();
?>

