# Contributing

We welcome contributions to WordPress Multi-Environment Config.

All contributors must agree to our [Code of Conduct](CODE_OF_CONDUCT.md).

WordPress Multi-Environment Config is released under the MIT license and is copyright Studio 24 Ltd. All contributors must accept these license and copyright conditions.

## Issues

Please submit any issues to the [issues log](https://github.com/studio24/wordpress-multi-env-config/issues). You are 
welcome to fork the project and create suggested fixes.

## Pull Requests

All contributions must be made on a branch and must be merged into the main branch via Pull Requests.  

All Pull Requests need at least one approval from the Studio 24 development team.

## Tests

Simple PHPUnit tests can be added to the `tests/` folder. Please try to add tests for any future changes.

## Continuous integration

[GitHub actions](https://github.com/studio24/wordpress-multi-env-config/actions) runs PHPUnit tests and PHPStan for code 
quality (checks `tests/*` and the `wp-config.load.php` file). You can set this up locally via:

```
composer install
```

Run PHPUnit via:

```
vendor/bin/phpunit
```

Run PHPStan via:

```
vendor/bin/phpstan analyse
```

## Creating new releases

This repo uses [Release Please](https://github.com/marketplace/actions/release-please-action) to automatically create releases, based on [semantic versioning](https://semver.org/).

To create a new release use [conventional commits](https://www.conventionalcommits.org/en/v1.0.0/) in your commit message. 
On merging new code to the `main` branch this will automatically create a release PR, which you can merge to create the 
new release when you are ready.

Use the following keywords in your commits:

* `fix:` this indicates a bug fix and creates a new patch version (e.g. 1.0.1).
* `feat:` this indicates a new feature and creates a new minor version (e.g. 1.1.0).
* To create a new major version (e.g. 2.0.0) either append an exclamation mark to `fix!:` or `feat!:` or add a footer of `BREAKING CHANGE:` with details of what breaking changes there are.

If the action fails to run you can view the action and select `Re-run all jobs` to re-run it.
