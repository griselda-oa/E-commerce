<?php
// settings/security.php
require_once __DIR__ . '/core.php';

class SecurityManager {
    
    /**
     * Generate a secure token for product access
     * @param int $product_id
     * @return string
     */
    public static function generateProductToken($product_id) {
        $secret_key = 'owusu_artisan_secret_2025';
        $timestamp = time();
        $data = $product_id . '|' . $timestamp;
        $token = base64_encode(hash_hmac('sha256', $data, $secret_key, true));
        return $token . '.' . base64_encode($data);
    }
    
    /**
     * Validate and extract product ID from token
     * @param string $token
     * @return int|false
     */
    public static function validateProductToken($token) {
        $secret_key = 'owusu_artisan_secret_2025';
        
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return false;
        }
        
        $signature = $parts[0];
        $data = base64_decode($parts[1]);
        
        if (!$data) {
            return false;
        }
        
        $expected_signature = base64_encode(hash_hmac('sha256', $data, $secret_key, true));
        
        if (!hash_equals($expected_signature, $signature)) {
            return false;
        }
        
        $data_parts = explode('|', $data);
        if (count($data_parts) !== 2) {
            return false;
        }
        
        $product_id = intval($data_parts[0]);
        $timestamp = intval($data_parts[1]);
        
        // Token expires after 24 hours
        if (time() - $timestamp > 86400) {
            return false;
        }
        
        return $product_id;
    }
    
    /**
     * Sanitize and validate integer input
     * @param mixed $input
     * @param int $min
     * @param int $max
     * @return int|false
     */
    public static function validateInteger($input, $min = 1, $max = 999999) {
        $value = filter_var($input, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => $min,
                'max_range' => $max
            ]
        ]);
        
        return $value !== false ? $value : false;
    }
    
    /**
     * Sanitize string input
     * @param string $input
     * @param int $max_length
     * @return string
     */
    public static function sanitizeString($input, $max_length = 255) {
        $input = trim($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return substr($input, 0, $max_length);
    }
    
    /**
     * Generate CSRF token
     * @return string
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     * @param string $token
     * @return bool
     */
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Rate limiting for API calls
     * @param string $action
     * @param int $limit
     * @param int $window
     * @return bool
     */
    public static function checkRateLimit($action, $limit = 60, $window = 3600) {
        $key = 'rate_limit_' . $action . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'reset_time' => time() + $window];
        }
        
        $rate_data = $_SESSION[$key];
        
        if (time() > $rate_data['reset_time']) {
            $_SESSION[$key] = ['count' => 1, 'reset_time' => time() + $window];
            return true;
        }
        
        if ($rate_data['count'] >= $limit) {
            return false;
        }
        
        $_SESSION[$key]['count']++;
        return true;
    }
}
?>
