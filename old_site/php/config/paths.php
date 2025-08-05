<?php
/**
 * Path Configuration for FloodGuard Project
 * This file defines base paths for the reorganized project structure
 */

// Base project directory
define('BASE_PATH', dirname(__DIR__));

// Main directories
define('HTML_PATH', BASE_PATH . '/html/');
define('PHP_PATH', BASE_PATH . '/php/');
define('CSS_PATH', BASE_PATH . '/css/');
define('JS_PATH', BASE_PATH . '/js/');
define('ASSETS_PATH', BASE_PATH . '/assets/');
define('DATABASE_PATH', BASE_PATH . '/Database/');
define('LOGS_PATH', BASE_PATH . '/logs/');

// PHP subdirectories
define('CONFIG_PATH', PHP_PATH . 'config/');
define('INCLUDES_PATH', PHP_PATH . 'includes/');
define('AUTH_PATH', PHP_PATH . 'auth/');
define('ADMIN_PATH', PHP_PATH . 'admin/');
define('DONOR_PATH', PHP_PATH . 'donor/');
define('VOLUNTEER_PATH', PHP_PATH . 'volunteer/');
define('API_PATH', PHP_PATH . 'api/');

// Web-accessible paths (for use in HTML/CSS)
define('WEB_CSS_PATH', '../css/');
define('WEB_JS_PATH', '../js/');
define('WEB_ASSETS_PATH', '../assets/');
define('WEB_PHP_PATH', '../php/');
define('WEB_AUTH_PATH', '../php/auth/');
define('WEB_ADMIN_PATH', '../php/admin/');
define('WEB_DONOR_PATH', '../php/donor/');
define('WEB_VOLUNTEER_PATH', '../php/volunteer/');
define('WEB_API_PATH', '../php/api/');

/**
 * Get the correct path based on context
 * @param string $type - Type of path needed (css, js, assets, php, auth, admin, donor, volunteer, api)
 * @param bool $web - Whether this is for web/HTML use (true) or server-side PHP use (false)
 * @return string
 */
function getPath($type, $web = false) {
    $prefix = $web ? 'WEB_' : '';
    $constant = strtoupper($prefix . $type . '_PATH');
    
    if (defined($constant)) {
        return constant($constant);
    }
    
    return '';
}

/**
 * Include a PHP file from the includes directory
 * @param string $filename
 */
function includeFile($filename) {
    $path = INCLUDES_PATH . $filename;
    if (file_exists($path)) {
        include $path;
    } else {
        error_log("Include file not found: " . $path);
    }
}

/**
 * Require a PHP file from the config directory
 * @param string $filename
 */
function requireConfig($filename) {
    $path = CONFIG_PATH . $filename;
    if (file_exists($path)) {
        require_once $path;
    } else {
        error_log("Config file not found: " . $path);
    }
}
?>
