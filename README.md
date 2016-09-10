# Studio 24 WordPress Multi-Environment Config

This repository contains Studio 24's standard config setup for WordPress, which 
loads different config based on current environment. This allows you to have different
site configuration (e.g. debug mode, or database settings) for different environments (e.g. production and 

Credit is due to FocusLabs [EE Master Config](https://github.com/focuslabllc/ee-master-config)
who gave me the inspiration for the organisation of the config files.

Please note the current version is v2, if you need to use the older v1 version [please see the v1 release](https://github.com/studio24/wordpress-multi-env-config/releases/tag/v1.0.2).

## How it works

The system detects what environment the current website is in and loads the relevant config file for that environment. 

By default the environment is defined by the hostname, though you can also set this as an environment variable.

Config files are then loaded according to the current environment. There is support for loading a local config file
for sensitive data, which is intended to not be committed to version control.

### Config files
 
Up to three different config files are loaded:

1. **Default configuration** (in `wp-config.default.php`, e.g. shared settings such as `$table_prefix`)
2. **Environment configuration** (in `wp-config.{ENVIRONMENT}.php`, e.g. any setting specific to the environment such as database name or debug mode)
3. **Optional local settings** (in `wp-config.local.php`, e.g. any sensitive settings you do not want to commit to version control, e.g. database password)

### Environment values

By default, environment values are:

* `production` (live website)
* `staging` (test website for client review)
* `development` (local development copy of the website)

You can add other environment values by adding these to the `wp-config.env.php` file.

## Setting the environment

The current environment is detected in one of three ways:

### Environment variable

You can set an environment variable called `WP_ENV` to set which environment the website uses in your webserver configuration. 

This is commonly done via Apache in your virtual host declaration:

    SetEnv WP_ENV production
    
If you don't use Apache consult your webserver documentation.

### Server hostname

You can also edit the `wp-config.env.php` file and define what hostnames are used for which environments. 

These are simply set at the top of this file, e.g.

```
$production  = 'domain.com';
$staging     = 'staging.domain.com';
$development = 'domain.local';
```

For example to set *www.mywebsite.com* as the live production environment,  *staging.mywebsite.com* as the staging environment and *mywebsite.local* as the local development environment the code is: 

```
$production  = 'www.mywebsite.com';
$staging     = 'staging.mywebsite.com';
$development = 'mywebsite.local';
```
        
You'll notice the live website URL is also the default case. So if you don't set this correctly, you'll load the production website settings.

If you use localhost for your local test website, just set the development hostname case to `localhost`.

### SSL support
If a domain supports SSL you can ensure this is set correctly in WordPress by forcing the domain to be served via SSL.

To do this, in `wp-config.env.php` set the constant `WP_ENV_SSL` to true for the environments you wish to force SSL. 

For example, to force SSL for production:

```
switch (WP_ENV) {

    ...

    case 'production':
    default:
        define('WP_ENV_DOMAIN', $production);
        define('WP_ENV_SSL', true);
}
```

### WordPress in a sub-folder
If your WordPress site is served from a sub-folder, then in `wp-config.env.php` set the constant `WP_ENV_PATH` to the sub-folder path. 

For example, to set the sub-folder to blog, so the site is served from www.domain.com/blog/

```
define('WP_ENV_PATH', 'blog');
```

## WP-CLI argument
If you're using [WP-CLI](http://wp-cli.org/) you can specify your environment via the `--env` argument. Usage is:

    --env=<environment>

For example:

    wp help --env=development    
    
This will then load the correct environment settings. 

## Installing
1. First make a backup of your existing `wp-config.php` file.
2. Copy the following files from this repository to your WordPress installation:

```
wp-config.default.php
wp-config.env.php
wp-config.php
wp-config-load.php
```
        
3. Set the correct environments you wish to support via adding the correct hostnames to `wp-config.env.php` or as environment variables via your webserver.
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

