<?php
declare(strict_types=1);

namespace Studio24\MultiEnvConfig;

/**
 * Wrapper class to load multi-environment config
 *
 * @package Studio24\MultiEnvConfig
 */
class LoadConfig
{
	/**
	 * Run code to load multi-environment config
	 */
	public function __construct()
	{
		$this->loadEnvironmentConfig();

		// 1st - Load default config
		require  __DIR__ . '/wp-config.default.php';

		// 2nd - Load config file for current environment
		require  __DIR__ . '/wp-config.' . WP_ENV . '.php';

		// 3rd - Load local config file with any sensitive settings
		if (file_exists( __DIR__ . '/wp-config.local.php')) {
			require  __DIR__ . '/wp-config.local.php';
		}
	}

	public function loadEnvironmentConfig()
	{
		/**
		 * Setup environment
		 */
		// Set env if set via environment variable
		if (getenv('WP_ENV') !== false) {
			define('WP_ENV', preg_replace('/[^a-z]/', '', getenv('WP_ENV')));
		}

		// Set env via --env=<environment> argument if running via WP-CLI
		if (!defined('WP_ENV') && PHP_SAPI == "cli" && defined('WP_CLI_ROOT')) {

			// We need to set $argv as global to be able to access it
			global $argv;

			if (isset($argv)) {
				foreach ($argv as $arg) {
					if (preg_match('/--env=(.+)/', $arg, $m)) {
						define('WP_ENV', preg_replace('/[^a-z]/', '', $m[1]));
						break;
					}
				}
			}

			// Also support via .env file in config directory
			if (!defined('WP_ENV')) {
				if (file_exists(__DIR__ . '/.env')) {
					$environment = trim(file_get_contents(__DIR__ . '/.env'));
					define('WP_ENV', preg_replace('/[^a-z]/', '', $environment));
				}
			}
		}

		// Define ENV from hostname
		if (!defined('WP_ENV')) {
			if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
				$hostname = strtolower(filter_var($_SERVER['HTTP_X_FORWARDED_HOST'], FILTER_SANITIZE_STRING));
			} else {
				$hostname = strtolower(filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_STRING));
			}
		}

		// Load environments
		require __DIR__ . '/wp-config.env.php';

		if (!isset($env) || !is_array($env)) {
			throw new Exception('$env array not detected, you must set this in wp-config.env.php');
		}

		// Set environment constants
		if (defined('WP_ENV')) {
			if (isset($env[WP_ENV])) {
				define('WP_ENV_DOMAIN', $env[WP_ENV]['domain']);
				define('WP_ENV_HOME_PATH', trim($env[WP_ENV]['home_path'], '/'));
				define('WP_ENV_SITE_PATH', trim($env[WP_ENV]['site_path'], '/'));
				define('WP_ENV_SSL', (bool)$env[WP_ENV]['ssl']);
			}

		} else {

			// Detect environment from hostname
			foreach ($env as $environment => $env_vars) {
				if (!isset($env_vars['domain'])) {
					throw new Exception('You must set the domain value in your environment array, see wp-config.env.php');
				}
				$domain = $env_vars['domain'];

				$wildcard = (strpos($domain, '*') !== false) ? true : false;
				if ($wildcard) {
					$match = '/' . str_replace('*', '([^.]+)', preg_quote($domain, '/')) . '/';
					if (preg_match($match, $hostname, $m)) {
						if (!defined('WP_ENV')) {
							define('WP_ENV', preg_replace('/[^a-z]/', '', $environment));
						}
						define('WP_ENV_DOMAIN', str_replace('*', $m[1], $domain));
						if (isset($env_vars['ssl'])) {
							define('WP_ENV_SSL', (bool)$env_vars['ssl']);
						}
						if (isset($env_vars['home_path'])) {
							define('WP_ENV_HOME_PATH', trim($env_vars['home_path'], '/'));
						}
						if (isset($env_vars['site_path'])) {
							define('WP_ENV_SITE_PATH', trim($env_vars['site_path'], '/'));
						}
						break;
					}
				}
				if (!is_array($domain)) {
					$domain = [$domain];
				}
				foreach ($domain as $domain_name) {
					if ($hostname === $domain_name) {
						if (!defined('WP_ENV')) {
							define('WP_ENV', preg_replace('/[^a-z]/', '', $environment));
						}
						define('WP_ENV_DOMAIN', $domain_name);
						if (isset($env_vars['ssl'])) {
							define('WP_ENV_SSL', (bool)$env_vars['ssl']);
						}
						if (isset($env_vars['home_path'])) {
							define('WP_ENV_HOME_PATH', trim($env_vars['home_path'], '/'));
						}
						if (isset($env_vars['site_path'])) {
							define('WP_ENV_SITE_PATH', trim($env_vars['site_path'], '/'));
						}
						break;
					}
				}
			}
		}

		if (!defined('WP_ENV')) {
			throw new Exception("Cannot determine current environment");
		}
		if (!defined('WP_ENV_DOMAIN')) {
			throw new Exception("Cannot determine current environment domain, make sure this is set in wp-config.env.php");
		}
		if (!defined('WP_ENV_SSL')) {
			define('WP_ENV_SSL', false);
		}
		if (WP_ENV_SSL && (!defined('FORCE_SSL_ADMIN'))) {
			define('FORCE_SSL_ADMIN', true);
		}

		/**
		 * Define WordPress Site URLs
		 */
		$protocol = (WP_ENV_SSL) ? 'https://' : 'http://';
		$homePath = (defined('WP_ENV_HOME_PATH')) ? '/' . trim(WP_ENV_HOME_PATH, '/') : '';
		$sitePath = (defined('WP_ENV_SITE_PATH')) ? '/' . trim(WP_ENV_SITE_PATH, '/') : '';

		if (!defined('WP_SITEURL')) {
			define('WP_SITEURL', $protocol . trim(WP_ENV_DOMAIN, '/') . $sitePath);
		}
		if (!defined('WP_HOME')) {
			define('WP_HOME', $protocol . trim(WP_ENV_DOMAIN, '/') . $homePath);
		}

		// Define W3 Total Cache hostname
		if (defined('WP_CACHE')) {
			define('COOKIE_DOMAIN', $hostname);
		}

	}
}
