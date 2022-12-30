<?php
declare(strict_types=1);

/**
 * WordPress Multi-Environment Config - Load config settings
 *
 * @package    Studio 24 WordPress Multi-Environment Config
 * @version    2.0.1 (x-release-please-version)
 * @author     Studio 24 <hello@studio24.net>
 */

/**
 * Custom exceptions for any fatal errors
 */
class MultiEnvConfig_Exception extends Exception { }

/**
 * Class to bootstrap the environment and load the appropriate config
 */
final class MultiEnvConfig_Bootstrap {

    private $environmentType = null;
    private $environmentHostname = null;
    private $environmentSettings = [];

    /**
     * Load environments
     * @param array|null $environmentSettings
     * @throws MultiEnvConfig_Exception
     */
    public function __construct(?array $environmentSettings = null)
    {
        if (null === $environmentSettings) {
            require __DIR__ . '/wp-config.env.php';

            /* @phpstan-ignore-next-line */
            if (!isset($env) || !is_array($env)) {
                throw new MultiEnvConfig_Exception('$env array not detected, you must set this in wp-config.env.php');
            }
            /* @phpstan-ignore-next-line */
            $environmentSettings = $env;
        }

        $this->environmentSettings = $environmentSettings;
    }

    /**
     * Is this environment type allowed?
     * @see https://developer.wordpress.org/reference/functions/wp_get_environment_type/
     * @param string $name Environment type
     * @return bool
     */
    function validEnvironment(string $name): bool
    {
        $environments = [
            'local',
            'development',
            'staging',
            'production',
        ];
        return in_array($name, $environments);
    }

    /**
     * Whether the current environment type is set
     * @return bool
     */
    public function hasEnvironment(): bool
    {
        if (null === $this->environmentType) {
            return false;
        }
        if (!$this->validEnvironment((string) $this->environmentType)) {
            $this->environmentType = null;
            return false;
        }
        return true;
    }

    /**
     * Set environment type
     * @param string $type
     * @return void
     */
    public function setEnvironmentType(string $type): void
    {
        if ($this->validEnvironment($type)) {
            $this->environmentType = $type;
        }
    }

    /**
     * Return current environment type
     * @return string|null
     */
    public function getEnvironmentType(): ?string
    {
        return $this->environmentType;
    }

    /**
     * Return the environment settings
     * @return array
     */
    public function getEnvironmentSettings(): array
    {
        return $this->environmentSettings;
    }

    /**
     * Return environment hostname, or null if not set
     * @return string|null
     */
    public function getEnvironmentHostname(): ?string
    {
        return $this->environmentHostname;
    }

    /**
     * Detect environment from environment variable
     * @return void
     */
    public function loadFromEnvironmentVariable(): void
    {
        $environmentType = getenv('WP_ENVIRONMENT_TYPE');
        if (false !== $environmentType) {
            $this->setEnvironmentType($environmentType);
        }
    }

    /**
     * Detect environment from .env file
     * @param string|null $filepath
     * @param string|null $dir
     * @return void
     */
    public function loadFromEnvFile(?string $filepath = null, ?string $dir = null): void
    {
        if (null === $filepath) {
            if (null == $dir) {
                $dir = __DIR__;
            }
            if (file_exists($dir . '/.env')) {
                $filepath = $dir . '/.env';
            } elseif (file_exists($dir . '/../.env')) {
                $filepath = $dir . '/../.env';
            } else {
                return;
            }
        }
        $envFile = file_get_contents($filepath);
        if ($envFile !== false && preg_match('/WP_ENVIRONMENT_TYPE=(.+)/', $envFile, $m)) {
            $this->setEnvironmentType($m[1]);
        }
    }

    /**
     * Detect environment from hostname
     * @param string|null $hostname
     * @return void
     */
    public function loadFromHostname(?string $hostname = null): void
    {
        if (null === $hostname) {
            if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                $hostname = strtolower(filter_var($_SERVER['HTTP_X_FORWARDED_HOST'], FILTER_SANITIZE_STRING));
            } else {
                $hostname = strtolower(filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_STRING));
            }
        }
        if (empty($hostname)) {
            return;
        }

        // Try to match hostname against environment domain
        foreach ($this->getEnvironmentSettings() as $environment => $envVars) {
            if (!isset($envVars['domain'])) {
                throw new MultiEnvConfig_Exception('You must set the domain value in your environment array, see wp-config.env.php');
            }

            $domains = $envVars['domain'];
            if (!is_array($domains)) {
                $domains = [$domains];
            }

            foreach ($domains as $domain) {
                if ($this->isWildcard($domain) && $this->matchWildcardDomain($domain, $hostname)) {
                    $this->setEnvironmentType($environment);
                    break;
                } elseif ($hostname === $domain) {
                    $this->setEnvironmentType($environment);
                    break;
                }
                $this->environmentHostname = $hostname;
            }
        }
    }

    /**
     * Whether a URL contains a wildcard pattern
     * @param string $domain
     * @return bool
     */
    public function isWildcard(string $domain): bool
    {
        return (preg_match('/^\*/', $domain) === 1);
    }

    /**
     * Whether the hostname matches a wildcard domain
     * @param string $domain
     * @param string $hostname
     * @return bool
     */
    public function matchWildcardDomain(string $domain, string $hostname): bool
    {
        $match = '/' . str_replace('\*', '([^.]+)', preg_quote($domain, '/')) . '/';
        return (preg_match($match, $hostname, $m) === 1);
    }

    /**
     * Return envUrls array for the current environment
     * @return array|null
     */
    public function getCurrentEnvUrls(): ?array
    {
        $settings = $this->getEnvironmentSettings();
        if (isset($settings[$this->getEnvironmentType()])) {
            return $settings[$this->getEnvironmentType()];
        }
        return null;
    }

    /**
     * Set WordPress constants from current environment
     * @return void
     * @throws MultiEnvConfig_Exception
     */
    public function setWordPressConstants(): void
    {
        if (!$this->hasEnvironment()) {
            throw new MultiEnvConfig_Exception('Current environment is not set, please ensure you have set the environment type correctly');
        }

        // Load URLs config for the environment
        $envUrls = $this->getCurrentEnvUrls();
        if (null === $envUrls) {
            throw new MultiEnvConfig_Exception(sprintf('Cannot detect current environment %s in wp-config.env.php', $this->getEnvironmentType()));
        }
        if (null !== $this->getEnvironmentHostname()) {
            $hostname = $this->getEnvironmentHostname();
        } else {
            $hostname = $envUrls['domain'];
        }
        $ssl =  (bool) $envUrls['ssl'];
        $protocol = ($ssl) ? 'https://' : 'http://';
        $path = !empty($envUrls['path']) ? '/' . trim($envUrls['path'], '/') : '';

        // @see https://developer.wordpress.org/apis/wp-config-php/#wp-environment-type
        define('WP_ENVIRONMENT_TYPE', $this->getEnvironmentType());

        // @see https://developer.wordpress.org/apis/wp-config-php/#wp-siteurl
        if (!defined('WP_SITEURL')) {
            define('WP_SITEURL', $protocol . rtrim($hostname, '/') . $path);
        }

        // @see https://developer.wordpress.org/apis/wp-config-php/#blog-address-url
        if (!defined('WP_HOME')) {
            define('WP_HOME', $protocol . rtrim($hostname, '/') . $path);
        }

        // @see https://developer.wordpress.org/apis/wp-config-php/#require-ssl-for-admin-and-logins
        if (!defined('FORCE_SSL_ADMIN') && $ssl) {
            define('FORCE_SSL_ADMIN', true);
        }

        // @see https://developer.wordpress.org/apis/wp-config-php/#set-cookie-domain
        if (!defined('COOKIE_DOMAIN')) {
            define('COOKIE_DOMAIN', $hostname);
        }
    }


}


/**
 * Load config
 */

$bootstrap = new MultiEnvConfig_Bootstrap();
$bootstrap->loadFromEnvironmentVariable();
if (!$bootstrap->hasEnvironment()) {
    $bootstrap->loadFromEnvFile();
}
if (!$bootstrap->hasEnvironment()) {
    $bootstrap->loadFromHostname();
}
$bootstrap->setWordPressConstants();

// 1st - Load default config
require  __DIR__ . '/wp-config.default.php';

// 2nd - Load config file for current environment
require  __DIR__ . '/wp-config.' . $bootstrap->getEnvironmentType() . '.php';

// 3rd - Optionally load local-only config file with any sensitive settings
if (file_exists(__DIR__ . '/.wp-config.php')) {
    require __DIR__ . '/.wp-config.php';
}
