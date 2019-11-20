<?php
/**
 * Setup environments
 *
 * @package    Studio 24 WordPress Multi-Environment Config
 * @version    2.0.0
 * @author     Studio 24 Ltd  <hello@studio24.net>
 */


/**
 * Define array of environment URLs
 *
 * Array of:
 * environment names =>
 *      domain  => The domain name
 *                 This can also be an array of multiple domains
 *                 You can also use a wildcard * to indicate all sub-domains at a domain, which is useful when using
 *                 WordPress Multisite. If you use wildcards, set the domain should to a single string, not an array
 *      home_path    => Path to the homepage, used to define 'WP_HOME' constant
 *      site_path   => Path to WordPress core, used to define 'WP_SITEURL' constant
 *      ssl     => Whether SSL should be used on this domain
 *
 * If you don't use any environments, remove them
 */
$env = [
    'production'  => [
        'domain' => 'domain.com',
        'home_path' => '',
        'site_path'   => '',
        'ssl'    => false,
    ],
    'staging'     => [
        'domain' => 'staging.domain.com',
        'home_path' => '',
        'site_path'   => '',
        'ssl'    => false,
    ],
    'development' => [
        'domain' => 'domain.local',
        'home_path' => '',
        'site_path'   => '',
        'ssl'    => false,
    ],
];
