# How it works

## The problem

It is common practise in web development to create multiple environments for a website when testing it, for example
development, staging and production. It is also a common requirement to need different configuration depending on the
environment type, for example different database credentials. WordPress has no out-of-the box support for this. 

## Environment types in WordPress

Since [version 5.5](https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/) 
WordPress has supported environment types which allow you to set which environment type your website is running as, to 
help toggle functionality in your website and plugins.

Supported environment types are:

* `production` 
* `staging`
* `development`
* `local`

Production represents the live website, and is the default if an environment cannot be detected. 

Typically, staging is a test version of a site for client review.

Development is normally the local test version of a site. However, some people use development to indicate a test version 
of a site for internal review (not for review by the client). In this instance, local is used to indicate the local test
version of a site.

Please note, the intention is for the environment type to represent the type of environment you are running 
rather than a specific instance, which is why the WordPress Core team decided to settle on a limited list of environment 
types. These should be enough to toggle different functionality.

## How this package works

The `wp-config.load.php` file detects what environment the current website is in and loads the relevant config for 
that environment.

### Detecting the environment

The environment is detected is done in one of the following ways (the first match wins):

1. Environment variable `WP_ENVIRONMENT_TYPE` is set
2. Environment is detected in an `.env` file
3. Hostname matches an expected list of environment hostnames

See [installation](install.md) and [wp-config.env.php file](wp-config-env.md) docs for how to set this up.

### Config files

Config files are loaded in the following order:

1. `wp-config.default.php` the default configuration (e.g. shared settings such as `$table_prefix`)
2. `wp-config.{ENVIRONMENT}.php` environment configuration (e.g. any setting specific to the environment such as database name or debug mode)
3. `.wp-config.php`, local settings (e.g. any sensitive settings you do not want to commit to version control such as database password)
