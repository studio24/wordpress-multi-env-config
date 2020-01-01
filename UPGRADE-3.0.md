# UPGRADE to 3.0

Previous versions of multi-environment config were standalone files intended to be copied manually to your WordPress 
site. From v3.0 code is organised into a WordPress plugin, we have tests to help ensure quality, and there are WP CLI 
commands to make installing and upgrading easier.

## Changes

`wp-config.env.php` has been renamed to `environments.php`

In the environment file `path` has been changed to `site_path`

`wp-config.php` has been updated, see `src/template/wp-config.php`
