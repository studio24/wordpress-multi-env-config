# The wp-config.env.php file

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
    path    => If WordPress is installed to a sub-folder set it here.
    ssl     => Whether SSL should be used on this domain. If set, this also sets FORCE_SSL_ADMIN to true.
```

Supported environment types are:

* `production`
* `staging`
* `development`
* `local`

Example usage:

```
$env = [
    'production'  => [
        'domain' => 'domain.com',
        'path'   => '',
        'ssl'    => true,
    ],
    'staging'     => [
        'domain' => 'staging.domain.com',
        'path'   => '',
        'ssl'    => true,
    ],
    'development' => [
        'domain' => 'domain.local',
        'path'   => '',
        'ssl'    => false,
    ],
];
```

If you use localhost for your local test website, just set the development hostname case to `localhost` rather than `domain.local`.

Example usage when setting a sub-folder, and also serving the live site via SSL:

```
    'production'  => [
        'domain' => 'domain.com',
        'path'   => 'blog',
        'ssl'    => true,
    ],
```

Example usage for using more than one domain for an environment.

```
    'production'  => [
        'domain' => ['domain.com', 'domain2.com'],
        'path'   => '',
        'ssl'    => false,
    ],
```

Example usage when using a wildcard for WordPress multi-site.

```
    'production'  => [
        'domain' => '*.domain.com',
        'path'   => '',
        'ssl'    => false,
    ],
```
