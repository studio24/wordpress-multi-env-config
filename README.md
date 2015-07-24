# Studio 24 WordPress Multi-Environment Config

This repository contains Studio 24's standard config setup for WordPress, which 
supports multiple environments such as your local development copy, a test
staging site, and the live production site. 

Using the multi-environment config you don't need to edit your config files between environments or update site URLs in your database, WordPress will just work!

Credit is due to FocusLabs [EE Master Config](https://github.com/focuslabllc/ee-master-config)
who gave me the inspiration for the organisation of the config files.

## How it works

The system detects what environment the current website is in and loads the relevant config file for that environment. 

A default config file is loaded first, which can contain any shared settings. This is  `wp-config.default.php`

The environment-specific file is then loaded, this is  `wp-config.{environment}.php` where *{environment}* is replaced by the environment name.

### Environment values

Environment values can be whatever you like. They match to their corresponding `wp-config.{environment}.php` config file.

Common environment values we use at Studio 24 include:

* `production` (the live website)
* `staging` (the test website for client review)
* `development` (the local development copy of the website)

### Setting the environment

The current environment is detected in one of three ways:

#### Environment variable

You can set an environment variable called `WP_ENV` to set which environment the website uses in your webserver configuration. 

This is commonly done via Apache in your virtual host declaration:

    SetEnv WP_ENV production
    
If you don't use Apache consult your webserver documentation.

#### Server hostname

You can also edit the `wp-config.env.php` file and define what hostnames are used for which environments. 

Just edit the PHP switch statement and enter the correct hostname (without http:// or the trailing slash) as the case value. You can then define which environment is used for that hostname via the `WP_ENV` constant. 

For example to set *www.mywebsite.com* as the live production environment,  *staging.mywebsite.com* as the staging environment and *mywebsite.dev* as the local development environment the code is: 

    switch ($hostname) {
        case 'mywebsite.dev':
            define('WP_ENV', 'development');
            break;
        case 'staging.mywebsite.com':
            define('WP_ENV', 'staging');
            break;
        case 'www.mywebsite.com':
        default: 
            define('WP_ENV', 'production');
    }
        
You'll notice the live website URL is also the default case.

If you use localhost for your local test website, just set the development hostname case to `localhost`.

### WP-CLI argument
If you're using [WP-CLI](http://wp-cli.org/) you can specify your environment via the `--env` argument. Usage is:

    --env=<environment>

For example:

    wp help --env=development    

## Installing
1. First make a backup of your existing `wp-config.php` file.
2. Copy the following files to your WordPress installation:

        wp-config.env.php
        wp-config.default.php
        wp-config.php
        
3. Either set the website environments you wish to support via `wp-config.env.php` or via your webserver configuration.
4. Create one `wp-config.{environment}.php` file for each environment. You can use the sample files provided in this repository:

        wp-config.development.php
        wp-config.production.php
        wp-config.staging.php

5. Review your backup `wp-config.php` file and copy config settings to either the default config file or the environment config files as appropriate.
6. Update config settings across your config files as appropriate, for example database settings are usually different between environments.
7. Remember to update the authentication unique keys and salts in `wp-config.default.php`

You should now be able to load up the website in each different environment and everything should work just fine! It should now be safe to delete your backup *wp-config.php* file.

