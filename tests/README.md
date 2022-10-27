Testing redCORE
==========

We have prepared scripts to run the tests automatically (only for Linux and MacOS, Windows coming soon)

## Prepare for running the tests
Before being able to run the tests you need to install several libraries in your system. Please execute the following commands:

Go to the root of your local cloned redCORE repository, and then type:

```
git submodule update --init --recursive
```

### Start working with Gulp

[Gulp](http://gulpjs.com/) is the task runner that takes care of compiling less, concat js, etc. Our automated tests layer uses it to generate the installable packages

The following instructions assumes that you already have Gulp and NodeJS in your system. If not, try:

- Install [Node.js](https://nodejs.org/)
- Then, install Gulp globally: `sudo npm install gulp -g`

Now test if Gulp is properly installed in your system. Try: `gulp -v`

Your system should reply with a response like: 

```
[10:48:24] CLI version 3.8.11
```

Now is time to start working with Gulp in the redCORE repository. Do:

```
cd build
```

rename `gulp-config.json.dist` into `gulp-config.json`

Edit `gulp-config.json` adding your local machine details.

Now execute: 

```
sudo npm install
```

That will install all the required Node libraries to be able to create the installable redCORE package.


## Getting Joomla

To execute the System tests at redCORE we need a a Joomla CMS 3.x. website. 
To do it automatically you can execute the following command from the root of the repository:

```
composer install
vendor/bin/robo prepare:site-for-system-tests
```

## Running the tests

First you need to create the configuration files:

- rename `tests/acceptance.suite.yml.dist` into `tests/acceptance.suite.yml`
- edit `tests/acceptance.suite.yml` with your server configuration
- rename `tests/api.suite.yml.dist` into `tests/api.suite.yml`
- edit `tests/api.suite.yml` with your server configuration

Tests are run from the `tests/` folder. Do:

`cd tests`

Then, run the tests executing the following CLI command:

```
vendor/bin/robo run:tests
```

And for unit tests:
```
vendor/bin/robo run:unit-tests
```
Note: make sure you have PDO_SQLite extension installed on apache (ubuntu: sudo apt-get install php5-sqlite)

## Running the tests manually
You can also run tests manually in any platform. See detailed instructions at: https://docs.joomla.org/Testing_Joomla_Extensions_with_Codeception
