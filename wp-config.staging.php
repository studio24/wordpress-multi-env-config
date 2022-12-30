<?php
/**
 * Staging environment config settings
 *
 * Enter any WordPress config settings that are specific to this environment 
 * in this file.
 *
 * @package    Studio 24 WordPress Multi-Environment Config
 */
  

// ** MySQL settings - You can get this info from your web host ** //
/** MySQL hostname */
define('DB_HOST', '');

/** The name of the database for WordPress */
define('DB_NAME', '');

/** MySQL database username */
define('DB_USER', '');

/** MySQL database password - set in .wp-config.php */

/**
 * For developers: WordPress debugging mode.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);


// Recommended WP config settings, uncomment to use these

/**
 * Disable theme/plugin upload.
 */
//define( 'DISALLOW_FILE_MODS', true );