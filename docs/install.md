# Installation

Please note this requires PHP 5.6 or above. You should really be on PHP 7.4 at a minimum!

1. Download the required files via [wordpress-multi-env-config.zip](https://github.com/studio24/wordpress-multi-env-config/releases/latest/download/wordpress-multi-env-config.zip)
2. First make a backup of your existing `wp-config.php` file.

1. Copy the following files from this repository to your WordPress installation:

```
wp-config.default.php
wp-config.env.php
wp-config.php
wp-config.load.php
```

3. Set the correct environments you wish to support via the file `wp-config.env.php`, see the [wp-config.env.php file](wp-config-env.md) docs.
4. Decide how to set the environment for your website, see [setting the environment](setting-the-environment.md) for options.
5. Create one `wp-config.{environment}.php` file for each environment. You can use the sample files provided in this repository if you wish:

```
wp-config.development.php
wp-config.production.php
wp-config.staging.php
```

6. Add the local-only config file if you wish to use it for storing sensitive settings (this step is optional):

```
.wp-config.php
```

7. If you use Git version control exclude `.wp-config.php` in your `.gitignore` file:

```
.wp-config.php
```

8. Review your backup `wp-config.php` file and copy config settings to either the default config file or the environment config files as appropriate. It is suggested to:
    * If the setting is the same across all environments, add to `wp-config.default.php`
    * If the setting is unique to one environment, add to `wp-config.{environment}.php`
    * If the setting is sensitive (e.g. database password) add to `.wp-config.php`
9. Remember to update the authentication unique keys and salts in `wp-config.default.php`


You should now be able to load up the website in each different environment and everything should work just fine! It should now be safe to delete your backup *wp-config.php* file.

## Moving your config files outside of the document root

It is recommended to store your config files outside of the document root for additional security.

First, simply move all the config files except for `wp-config.php` itself into another folder (which must be outside the 
document root).

Example directory structure:

```
├── config/                      (config folder outside of doc root)
│   ├── .wp-config.php
│   ├── wp-config.default.php    
│   ├── wp-config.development.php
│   ├── wp-config.env.php
│   ├── wp-config.load.php
│   ├── wp-config.production.php
│   └── wp-config.staging.php
└──  web/                        (your website document root, where WordPress is installed)
    └──  wp-config.php               
```

Next, amend the require path for the `wp-config.load.php` file in `wp-config.php` to point to the new location and everything will work just fine!

Example `wp-config.php`:

```
/** Load the Studio 24 WordPress Multi-Environment Config. */
require_once(ABSPATH . '../config/wp-config.load.php');
```