# Setting the environment

The current environment is detected in one of three ways:

1. Environment variable `WP_ENVIRONMENT_TYPE` is set
2. Environment is detected in an `.env` file
3. Hostname matches an expected list of environment hostnames

## Environment variable

You can set an environment variable called `WP_ENVIRONMENT_TYPE` to set which environment the website uses in your
webserver configuration. 

### Apache

This is commonly done via Apache in your virtual host declaration:

```
SetEnv WP_ENVIRONMENT_TYPE development
```

If you don't use Apache consult your webserver documentation.

### Command line

If you are using WP CLI then you can set an environment variable on the command line via:

```
export WP_ENVIRONMENT_TYPE="development"
```

## .env file

You can also specify your environment using an `.env` file. Simply create a file called `.env` and set the environment using 
the following format:

```
WP_ENVIRONMENT_TYPE=development
```

This file must either exist in the same folder as `wp-config.load.php` or the parent folder (this is useful if you store 
config files in the `config/` sub-folder.

It is recommended you do not add this file to version control. You can exclude the file in your `.gitignore` file via:

```
.env
```

## Server hostname

The current environment can also be detected from matching the hostname with the domain setup in `wp-config.env.php`.

See the [wp-config.env.php file](wp-config-env.md).