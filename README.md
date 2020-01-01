# Studio 24 WordPress Multi-Environment Config

Manage WordPress config for multiple environments (e.g. Development, Staging, Production). 

## How it works

The system replaces the traditional `wp-config.php` file and loads the correct config based on the current environment. 
It does this by checking:

1. Whether the environment variable `WP_ENV` is set
2. If there is a `.env` file in the WordPress directory or parent directory (it checks up to two parent directories above 
the folder WordPress is installed to)
3. Match the current environment based on the hostname

Config files are then loaded according to the current environment. There is support for loading a local config file
for sensitive data, which is intended to not be committed to version control (e.g. for passwords or other secrets).

### Config files
 
Up to three different config files are loaded, in the following order:

1. **Default configuration** that applies to all environments (in `wp-config.default.php`, e.g. shared settings such as `$table_prefix`)
2. **Environment configuration** (in `wp-config.{ENVIRONMENT}.php`, e.g. any setting specific to the environment such as database name or debug mode)
3. **Optional local settings** (in `wp-config.local.php`, e.g. any sensitive settings you do not want to commit to version control, e.g. database password)

### Directory structure

It is recommended to place config files outside of your WordPress folder. e.g.

```
config/
       environments.php             (List of environments)
       wp-config.default.php        (Default global config)
       wp-config.development.php    (Development environment config)
       wp-config.local.php          (Any local config you don't want in version control)
       wp-config.production.php     (Production environment config)
       wp-config.staging.php        (Staging environment config)
web/
    wp-config.php
    (WordPress files)
```

### Environment values

By default, environment values are:

* `production` (live website)
* `staging` (test website for client review)
* `development` (local development copy of the website)

You can add other environment values by adding these to the `environments.php` file.

## Setting the environment

The current environment is detected in one of three ways:

### Environment variable

You can set an environment variable called `WP_ENV` to set which environment the website uses in your webserver configuration. 

This is commonly done via Apache in your virtual host declaration:

    SetEnv WP_ENV production
    
If you don't use Apache consult your webserver documentation.

### Server hostname

The current environment can also be detected from matching the hostname with the domain setup in `wp-config.env.php`.

### WP-CLI
If you're using [WP-CLI](http://wp-cli.org/) you can specify your environment using an '.env' file.

You need to create a file called `.env` and simply write the current environment in this file as a string.

Example:

```
development
```
This file needs to be placed among the wp-config.*.php files (this applies if you keep these files in a sub-folder and the wp-config.php file in the web root).

It is recommended you do not add this file to version control.

## The wp-config.env.php file

You need to edit the `wp-config.env.php` file to define some settings related to the current environment URL. This needs to 
be set regardless of which method is used to set the environment, since all methods set the WordPress URL via settings 
contained in this file. 
 
This file contains a simple array, made up of:
 
```
environment names =>
    domain  => The domain name.
               This can also be an array of multiple domains.
               You can also use a wildcard * to indicate all sub-domains at a domain, which is useful when using
               WordPress Multisite. If you use wildcards, set the domain should to a single string, not an array.
    home_path    => Path to the homepage, used to define 'WP_HOME' constant
    site_path   => Path to WordPress core, used to define 'WP_SITEURL' constant
    ssl     => Whether SSL should be used on this domain. If set, this also sets FORCE_SSL_ADMIN to true.
```

Example usage:

```
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
```

If you use localhost for your local test website, just set the development hostname case to `localhost` rather than `domain.local`.

Example usage when setting a sub-folder, and also serving the live site via SSL:

```
    'production'  => [
        'domain' => 'domain.com',
        'home_path'   => 'blog',
        'site_path    => 'blog',
        'ssl'    => true,
    ],
```

Example usage when installing WP in a sub-folder without affecting the home url:

```
    'production'  => [
        'domain' => 'domain.com',
        'home_path'   => '',
        'site_path    => 'wordpress',
        'ssl'    => true,
    ],
```

Example usage for using more than one domain for an environment. 

```
    'production'  => [
        'domain' => ['domain.com', 'domain2.com'],
        'home_path'   => '',
        'site_path'   => '',
        'ssl'    => false,
    ],
```

Example usage when using a wildcard for WordPress multi-site.

```
    'production'  => [
        'domain' => '*.domain.com',
        'home_path'   => '',
        'site_path'   => '',
        'ssl'    => false,
    ],
```

## Installing
Please note this requires PHP5.4 or above. You should really be on PHP5.6 at a minimum!

1. First make a backup of your existing `wp-config.php` file.
2. Copy the following files from this repository to your WordPress installation:

```
environments.php
wp-config.default.php
wp-config.php
wp-config.load.php
```
        
3. Set the correct environments you wish to support via the file `wp-config.env.php`, see the documentation above.
4. Create one `wp-config.{environment}.php` file for each environment. You can use the sample files provided in this repository:

```
wp-config.development.php
wp-config.production.php
wp-config.staging.php
wp-config.local.php
```

5. Review your backup `wp-config.php` file and copy config settings to either the default config file or the environment config files as appropriate. It is suggested to:
    * If the setting is the same across all environments, add to `wp-config.default.php`
    * If the setting is unique to one environment, add to `wp-config.{environment}.php`
    * If the setting is sensitive (e.g. database password) add to `wp-config.local.php`
6. Remember to update the authentication unique keys and salts in `wp-config.default.php`
7. If you use version control exclude `wp-config.local.php`, an example below for Git:

```
# .gitignore
wp-config.local.php
```

You should now be able to load up the website in each different environment and everything should work just fine! It should now be safe to delete your backup *wp-config.php* file.

## Moving your config files outside of the document root

If you want to store your config files outside of the document root for additional security, this is very easy. 

Simply move all the config files except for `wp-config.php` itself into another folder (which can be outside the doc root). 
Next amend the require path for `wp-config.load.php` in `wp-config.php` to point to the new location and everything will work just fine! 

Example directory structure:

```
config/
       wp-config.default.php   (Config folder outside of doc root)
       wp-config.development.php
       wp-config.env.php
       wp-config.load.php
       wp-config.local.php
       wp-config.production.php
       wp-config.staging.php
web/
    wp-config.php              (Your website doc root, where WordPress is installed) 
```
 
Example `wp-config.php`

```
/** Load the Studio 24 WordPress Multi-Environment Config. */
require_once(ABSPATH . '../config/wp-config.load.php');
```

## Tests

Install 

```
./s24-multi-env-config/bin/install-wp-tests.sh wordpress_test <db_user> <db_pass> <db_host> latest
composer install
```

Run PHPUnit

```
./vendor/bin/phpunit
```
## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

* [Simon R Jones](https://github.com/simonrjones)

Credit is due to FocusLabs [EE Master Config](https://github.com/focuslabllc/ee-master-config)
who gave me the inspiration for the organisation of config files.
