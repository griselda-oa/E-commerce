<?php
/**
 * actions/redirect_products_action.php
 * MVC-compliant redirect action for products.php
 * Handles legacy links and bookmarks that reference products.php
 */

require_once __DIR__ . '/../settings/core.php';

// Use 301 permanent redirect for SEO and browser caching
http_response_code(301);
header('Location: ../all_product.php', true, 301);
exit();
?>

