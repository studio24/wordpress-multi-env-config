# Accessing the environment

## Returning the current environment type

You can use the WordPress function `wp_get_environment_type()` to return the current environment. This defaults to `production`
if the current environment cannot be determined.

You can see example usage on the [wp_get_environment_type() reference](https://developer.wordpress.org/reference/functions/wp_get_environment_type/) 
documentation page.

## Environment PHP constants

The following PHP constants are available for the current environment:

* `WP_ENVIRONMENT_TYPE` - environment type
* `WP_ENV_DOMAIN` - 
* `WP_ENV_PATH` - 
* `WP_ENV_SSL` - 