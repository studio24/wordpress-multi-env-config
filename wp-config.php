<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */


/**
 * WordPress Multi-Environment Config
 * 
 * Loads config file based on current environment, environment can be set
 * in either the environment variable 'WP_ENV' or can be set based on the 
 * server hostname.
 * 
 * This also overrides the option_home and option_siteurl settings in the 
 * WordPress database to ensure site URLs are correct between environments.
 * 
 * Common environment names are as follows, though you can use what you wish:
 * 
 *   production
 *   staging
 *   development
 * 
 * For each environment a config file must exist named wp-config.{environment}.php
 * with any settings specific to that environment. For example a development 
 * environment would use the config file: wp-config.development.php
 * 
 * Default settings that are common to all environments can exist in wp-config.default.php
 * 
 * @package    Studio 24 WordPress Multi-Environment Config
 * @version    1.0
 * @author     Studio 24 Ltd  <info@studio24.net>
 */

// Try environment variable 'WP_ENV'
if (getenv('WP_ENV') !== false) {
    // Filter non-alphabetical characters for security
    define('WP_ENV', preg_replace('/[^a-z]/', '', getenv('WP_ENV')));
} 

// Define site host
if (isset($_SERVER['X_FORWARDED_HOST']) && !empty($_SERVER['X_FORWARDED_HOST'])) {
    $hostname = $_SERVER['X_FORWARDED_HOST'];
} else {
    $hostname = $_SERVER['HTTP_HOST'];
}
    
// Try server hostname
if (!defined('WP_ENV')) {
    // Set environment based on hostname
    include 'wp-config.env.php';
}

// Are we in SSL mode?
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    $protocol = 'https://';
} else {
    $protocol = 'http://';
}

// Load default config
include 'wp-config.default.php';

// Load config file for current environment
include 'wp-config.' . WP_ENV . '.php';

// Define WordPress Site URLs if not already set in config files
if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', $protocol . rtrim($hostname, '/'));
}
if (!defined('WP_HOME')) {
    define('WP_HOME', $protocol . rtrim($hostname, '/'));
}

// Clean up
unset($hostname, $protocol);

/** End of WordPress Multi-Environment Config **/


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
