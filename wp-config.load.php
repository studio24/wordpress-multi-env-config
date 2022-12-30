<?php
/**
 * WordPress Multi-Environment Config - Load config settings
 *
 * @package    Studio 24 WordPress Multi-Environment Config
 * @version    2.0.1 (x-release-please-version)
 * @author     Studio 24 <hello@studio24.net>
 */

function s24_load_environment_config() {

    /**
     * Detect environment from environment variable
     */
    $environmentType = getenv('WP_ENVIRONMENT_TYPE');
    if ($environmentType !== false) {
        if (s24_allowed_environment($environmentType)) {
            define('WP_ENVIRONMENT_TYPE', $environmentType);
        }
    }

    /**
     * Detect environment from .env file
     */
    if (!defined('WP_ENVIRONMENT_TYPE')) {
        $envFile = null;

        if (file_exists(__DIR__ . '/.env')) {
            $envFile = file_get_contents(__DIR__ . '/.env');
        } elseif (file_exists(__DIR__ . '/../.env')) {
            $envFile = file_get_contents(__DIR__ . '/../.env');
        }

        if ($envFile !== null && preg_match('/WP_ENVIRONMENT_TYPE=(.+)/', $envFile, $m)) {
            $environmentType = $m[1];
            if (s24_allowed_environment($environmentType)) {
                define('WP_ENVIRONMENT_TYPE', $environmentType);
            }
        }
    }

    /**
     * Detect environment from hostname
     */
    if (!defined('WP_ENVIRONMENT_TYPE')) {
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $hostname = strtolower(filter_var($_SERVER['HTTP_X_FORWARDED_HOST'], FILTER_SANITIZE_STRING));
        } else {
            $hostname = strtolower(filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_STRING));
        }
    }

    /**
     * Load environments
     */
    require  __DIR__ . '/wp-config.env.php';

    if (!isset($env) || !is_array($env)) {
        throw new Studio24_MultiEnvConfig_Exception('$env array not detected, you must set this in wp-config.env.php');
    }

    // Set environment constants
    if (defined('WP_ENVIRONMENT_TYPE')) {
        if (isset($env[WP_ENVIRONMENT_TYPE])) {
            define('WP_ENVIRONMENT_DOMAIN', $env[WP_ENVIRONMENT_TYPE]['domain']);
            define('WP_ENVIRONMENT_PATH', trim($env[WP_ENVIRONMENT_TYPE]['path'], '/'));
            define('WP_ENVIRONMENT_SSL', (bool) $env[WP_ENVIRONMENT_TYPE]['ssl']);
        }

    } else {

        /**
         * Detect environment from hostname
         */
        foreach ($env as $envFile => $env_vars) {
            if (!isset($env_vars['domain'])) {
                throw new Studio24_MultiEnvConfig_Exception('You must set the domain value in your environment array, see wp-config.env.php');
            }
            $domain = $env_vars['domain'];

            $wildcard = is_string($domain) && strpos($domain, '*') !== false;
            if ($wildcard) {
                $match = '/' . str_replace('\*', '([^.]+)', preg_quote($domain, '/')) . '/';
                if (preg_match($match, $hostname, $m)) {
                    if (!defined('WP_ENVIRONMENT_TYPE')) {
                        $environmentType = preg_replace('/[^a-z]/', '', $envFile);
                        if (s24_allowed_environment($environmentType)) {
                            define('WP_ENVIRONMENT_TYPE', $environmentType);
                        }
                    }
                    define('WP_ENVIRONMENT_DOMAIN', str_replace('*', $m[1], $domain));
                    if (isset($env_vars['ssl'])) {
                        define('WP_ENVIRONMENT_SSL', (bool)$env_vars['ssl']);
                    } else {
                        define('WP_ENVIRONMENT_SSL', false);
                    }
                    if (isset($env_vars['path'])) {
                        define('WP_ENVIRONMENT_PATH', trim($env_vars['path'], '/'));
                    }

                    /**
                    * Define WordPress Site URLs
                    */
                    $protocol = (WP_ENVIRONMENT_SSL) ? 'https://' : 'http://';
                    $path = (defined('WP_ENVIRONMENT_PATH')) ? '/' . trim(WP_ENVIRONMENT_PATH, '/') : '';

                    if (!defined('WP_SITEURL')) {
                        define('WP_SITEURL', $protocol . trim(WP_ENVIRONMENT_DOMAIN, '/') . $path);
                    }
                    if (!defined('WP_HOME')) {
                        define('WP_HOME', $protocol . trim(WP_ENVIRONMENT_DOMAIN, '/') . $path);
                    }
                    break;
                }
            }
            if (!is_array($domain)) {
                $domain = [$domain];
            }
            foreach ($domain as $domain_name) {
                if ($hostname === $domain_name) {
                    if (!defined('WP_ENVIRONMENT_TYPE')) {
                        define('WP_ENVIRONMENT_TYPE', preg_replace('/[^a-z]/', '', $envFile));
                    }
                    define('WP_ENVIRONMENT_DOMAIN', $domain_name);
                    if (isset($env_vars['ssl'])) {
                        define('WP_ENVIRONMENT_SSL', (bool)$env_vars['ssl']);
                    } else {
                        define('WP_ENVIRONMENT_SSL', false);
                    }
                    if (isset($env_vars['path'])) {
                        define('WP_ENVIRONMENT_PATH', trim($env_vars['path'], '/'));
                    }

                    /**
                    * Define WordPress Site URLs
                    */
                    $protocol = (WP_ENVIRONMENT_SSL) ? 'https://' : 'http://';
                    $path = (defined('WP_ENVIRONMENT_PATH')) ? '/' . trim(WP_ENVIRONMENT_PATH, '/') : '';

                    if (!defined('WP_SITEURL')) {
                        define('WP_SITEURL', $protocol . trim(WP_ENVIRONMENT_DOMAIN, '/') . $path);
                    }
                    if (!defined('WP_HOME')) {
                        define('WP_HOME', $protocol . trim(WP_ENVIRONMENT_DOMAIN, '/') . $path);
                    }
                    break;
                }
            }
        }
    }

    if (!defined('WP_ENVIRONMENT_TYPE')) {
        throw new Studio24_MultiEnvConfig_Exception("Cannot determine current environment");
    }
    if (!defined('WP_ENVIRONMENT_DOMAIN')) {
        throw new Studio24_MultiEnvConfig_Exception("Cannot determine current environment domain, make sure this is set in wp-config.env.php");
    }
    if (!defined('WP_ENVIRONMENT_SSL')) {
        define('WP_ENVIRONMENT_SSL', false);
    }
    if (WP_ENVIRONMENT_SSL && (!defined('FORCE_SSL_ADMIN'))) {
        define('FORCE_SSL_ADMIN', true);
    }

    // Define W3 Total Cache hostname
    if (defined('WP_CACHE')) {
        define('COOKIE_DOMAIN', WP_ENVIRONMENT_DOMAIN);
    }

}

/**
 * Is this environment type allowed?
 * @param string $name Environment type
 * @return bool
 */
function s24_allowed_environment($name)
{
    $environments = [
        'local',
        'development',
        'staging',
        'production',
    ];
    return in_array((string) $name, $environments);
}

/**
 * Custom exception for any fatal errors
 */
class Studio24_MultiEnvConfig_Exception extends Exception { }

s24_load_environment_config();


/**
 * Load config
 */

// 1st - Load default config
require  __DIR__ . '/wp-config.default.php';

// 2nd - Load config file for current environment
require  __DIR__ . '/wp-config.' . WP_ENVIRONMENT_TYPE . '.php';

// 3rd - Load local config file with any sensitive settings
if (file_exists(__DIR__ . '/.wp-config.php')) {
    require __DIR__ . '/.wp-config.php';
}
