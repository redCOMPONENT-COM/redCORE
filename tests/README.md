Testing redCORE
==========

## Getting Joomla
The first step to execute the System tests at redCORE is to get the two versions of the Joomla CMS: 2.5 and 3.x. To do it automatically you can execute the following command from the root of the repository:

```
php vendor/phing/phing/bin/phing -f tests/getjoomla.xml
```

## Using codecept.phar

Get codeception phar:

```
wget http://codeception.com/codecept.phar .
```

Build codeception testers classes:

```
php ./codecept.phar build
```

## using composer to get Codeception

Execute
```
# You need to have Composer in your system, if not download it from here: https://getcomposer.org/
composer update
```
After that you will be able to run Codeception doing:

```
php vendor/codeception/codeception/codecept build
```

## Running the tests

Rename tests/acceptance.suite.dist.yml to tests/acceptance.suite.yml

Modify the configuration at tests/acceptance.suite.yml to fit your server details. Find the instructions in the same file: https://github.com/redCOMPONENT-COM/redCORE/blob/develop/tests/acceptance.suite.dist.yml#L3

Run Selenium server:

```
# Download
curl -O http://selenium-release.storage.googleapis.com/2.41/selenium-server-standalone-2.41.0.jar

# And start the Selenium Server
java -Xms40m -Xmx256m -jar /Applications/XAMPP/xamppfiles/htdocs/selenium/selenium-server-standalone-2.41.0.jar
```


Execute the tests:

```
php codecept.phar run acceptance -g Joomla2 --env joomla2
# Or php vendor/codeception/codeception/codecept run acceptance -g Joomla2 --env joomla2
php codecept.phar run acceptance -g Joomla3 --env joomla3
# Or php vendor/codeception/codeception/codecept run acceptance -g Joomla3 --env joomla3


; Or with --steps to see a step-by-step report on the performed actions.
php codecept.phar run --steps
# Or php vendor/codeception/codeception/codecept run --steps

; Or with --html. This command will run all tests for all suites, displaying the steps, and building HTML and XML reports. Reports will be store in tests/_output/ directory.
php codecept.phar run --html
# Or php vendor/codeception/codeception/codecept run --html
```

You can also run tests individually. In the following example we run only the States test: 
```
# note: you will not be able to run the following test if Joomla is not installed with redCORE in it 
run tests/acceptance/ManageStatesAdministratorCept.php -g Joomla2 --env joomla2 
``

## Firefox Addons
To generate tests really fast you can use these firefox addons:

- Selenium IDE (records your screen)
- Selenium IDE Codeception Formatter (Export your Selenium IDE test to Codeception language)