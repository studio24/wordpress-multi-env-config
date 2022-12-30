# Upgrading

## Upgrade from v2

To upgrade please follow these steps.

### wp-config.local.php

WordPress now includes `local` as a valid environment type. Therefore, `wp-config.local.php` is no longer used for a 
local-only sensitive config and is used instead as a valid environment config file (that can be comitted to git).  

In its place `.wp-config.php` is now used for storing sensitive config settings.

1. If you are using `wp-config.local.php` for sensitive config settings, rename this file to `.wp-config.php`
2. Remove `wp-config.local.php` from your `.gitignore` file if it exists
3. Add the following to your `.gitignore` file:

```
# .gitignore
.wp-config.php
```

### WP_ENV

In v2 this package used the environment variable `WP_ENV` to set the current environment. With the support of environment 
types in WordPress this has now changed to `WP_ENVIRONMENT_TYPE`

1. Update your environment variable from `WP_ENV` to `WP_ENVIRONMENT_TYPE`

### Renamed PHP constants

If you have any code that relies on the following PHP constants please update to the new value. You can also use 
`wp_get_environment_type()` to return the current environment type.

* `WP_ENV` to `WP_ENVIRONMENT_TYPE`
* `WP_ENV_DOMAIN` to `WP_ENVIRONMENT_DOMAIN`
* `WP_ENV_PATH` to `WP_ENVIRONMENT_PATH`
* `WP_ENV_SSL` to `WP_ENVIRONMENT_SSL`

### .env file

If you use an .env file to set your environment type, the format of this has changed to match the standard dotenv format.

Old version:

```
development
```

New version:

```
WP_ENVIRONMENT_TYPE=development
```

### CLI --env param

Removed the `--env` CLI parameter since this does not work reliably. Instead, set the environment in the `.env` file. See 
[setting the environment](setting-the-environment.md).