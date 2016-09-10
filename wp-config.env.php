<?php
/**
 * Setup environments
 * 
 * Set environment based on the current server hostname, this is stored
 * in the $hostname variable
 * 
 * You can define the current environment via: 
 *     define('WP_ENV', 'production');
 * 
 * @package    Studio 24 WordPress Multi-Environment Config
 * @version    2.0.0
 * @author     Studio 24 Ltd  <info@studio24.net>
 */


/**
 * Define environment URLs
 *
 * If you don't use any of these, remove them
 */
$production  = 'domain.com';
$staging     = 'staging.domain.com';
$development = 'domain.local';

/**
 * If your WordPress site is served from a sub-folder of the domain, then uncomment the next line and set this here
 */
// define('WP_ENV_PATH', 'blog');

/**
 * First set environment based on hostname
 *
 * If you don't use any of these, remove them
 */
if (!defined('WP_ENV')) {
    switch ($hostname) {
        case $development:
            define('WP_ENV', 'development');
            break;

        case $staging:
            define('WP_ENV', 'staging');
            break;

        case $production:
        default:
            define('WP_ENV', 'production');
    }
}

/**
 * Set domain based on hostname / environment value
 *
 * If you don't use any of these, remove them
 */
switch (WP_ENV) {
    case 'development':
        define('WP_ENV_DOMAIN', $development);
        define('WP_ENV_SSL', false);
        break;

    case 'staging':
        define('WP_ENV_DOMAIN', $staging);
        define('WP_ENV_SSL', false);
        break;

    case 'production':
    default:
        define('WP_ENV_DOMAIN', $production);
        define('WP_ENV_SSL', false);
}
