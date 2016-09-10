<?php
/**
 * WordPress Multi-Environment Config - Load config settings
 *
 * @package    Studio 24 WordPress Multi-Environment Config
 * @version    2.0.0
 * @author     Studio 24 <hello@studio24.net>
 */

/**
 * Setup
 */

// Set env if set via environment variable
if (getenv('WP_ENV') !== false) {

    // Filter non-alphabetical characters for security
    define('WP_ENV', preg_replace('/[^a-z]/', '', getenv('WP_ENV')));
}

// Set env via --env=<environment> argument if running via WP-CLI
if (PHP_SAPI == "cli" && defined('WP_CLI_ROOT')) {
    foreach ($argv as $arg) {
        if (preg_match('/--env=(.+)/', $arg, $m)) {
            define('WP_ENV', $m[1]);
        }
    }
}

// Define site host
if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $hostname = strtolower(filter_var($_SERVER['HTTP_X_FORWARDED_HOST'], FILTER_SANITIZE_STRING));
} else {
    $hostname = strtolower(filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_STRING));
}

if (!defined('WP_ENV') && empty($hostname)) {
    throw new Exception("Cannot determine current environment via WP_ENV or hostname");
}

// Set environment based on hostname or environment variable
require ABSPATH . '/wp-config.env.php';


/**
 * Load config
 */

// 1st - Load default config
require ABSPATH . '/wp-config.default.php';

// 2nd - Load config file for current environment
require ABSPATH . '/wp-config.' . WP_ENV . '.php';

// 3rd - Load local config file with any sensitive settings
if (file_exists(ABSPATH . '/wp-config.local.php')) {
    require ABSPATH . '/wp-config.local.php';
}


/**
 * Define WordPress Site URLs
 */

if (!defined('WP_ENV_DOMAIN')) {
    throw new Exception("Cannot determine current environment domain, make sure this is set in wp-config.env.php");
}
if (!defined('WP_ENV_SSL')) {
    define('WP_ENV_SSL', false);
}
$protocol = (WP_ENV_SSL) ? 'https://' : 'http://';
$path = (defined('WP_ENV_PATH')) ? '/' . trim(WP_ENV_PATH, '/') : '';

if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', $protocol . trim($hostname, '/') . $path);
}
if (!defined('WP_HOME')) {
    define('WP_HOME', $protocol . trim($hostname, '/') . $path);
}

// Define W3 Total Cache hostname
if (defined('WP_CACHE')) {
    define('COOKIE_DOMAIN', $hostname);
}


/**
 * Clean up
 */
unset($hostname, $protocol, $production, $staging, $development, $local);