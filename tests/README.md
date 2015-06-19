Testing redCORE
==========

We have prepared scripts to run the tests automatically (only for Linux and MacOS, Windows coming soon)

## Getting Joomla
The first step to execute the System tests at redCORE is to get a Joomla CMS 3.x. site To do it automatically you can execute the following command from the root of the repository:

```
vendor/bin/robo prepare:site-for-system-tests
```

## Running the tests

First you need to create the configuration files:

- rename `tests/acceptance.suite.dist.yml` into `tests/acceptance.suite.yml`
- edit `tests/acceptance.suite.yml` with your server configuration
- rename `tests/api.suite.dist.yml` into `tests/api.suite.yml`
- edit `tests/api.suite.yml` with your server configuration

Run the tests in the following order:

```
vendor/bin/robo vendor/bin/robo run:test --test=01-InstallJoomlaCept.php
vendor/bin/robo vendor/bin/robo run:test --test=02-InstallExtensionCept.php
vendor/bin/robo run:tests --suite=api
vendor/bin/robo run:test --test=ZZ-UninstallExtensionCept.php
```

## Running the tests manually
You can also run tests manually in any platform. See detailed instructions at: https://docs.joomla.org/Testing_Joomla_Extensions_with_Codeception
