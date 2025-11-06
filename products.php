<?php
// products.php - Redirect to all_product.php for backward compatibility
// Use 301 permanent redirect for SEO and browser caching
header('HTTP/1.1 301 Moved Permanently');
header('Location: all_product.php');
exit();
?>

