<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * Download robo.phar from http://robo.li/robo.phar and type in the root of the repo: $ php robo.phar
 * Or do: $ composer update, and afterwards you will be able to execute robo like $ php vendor/bin/robo
 *
 * @see  http://robo.li/
 */
require_once 'vendor/autoload.php';

/**
 * Class RoboFile
 *
 * @since  1.6.14
 */
class RoboFile extends \Robo\Tasks
{
	// Load tasks from composer, see composer.json
	use Joomla\Testing\Robo\Tasks\LoadTasks;

	/**
	 * File extension for executables
	 *
	 * @var string
	 */
	private $executableExtension = '';

	/**
	 * Local configuration parameters
	 *
	 * @var array
	 */
	private $configuration = array();

	/**
	 * Path to the local CMS root
	 *
	 * @var string
	 */
	private $cmsPath = '';

	/**
	 * Current root folder
	 * File extension for executables
	 *
	 * @var string
	 */
	 private $testsFolder = './';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->configuration = $this->getConfiguration();
		$this->cmsPath = $this->getCmsPath();
		$this->executableExtension = $this->getExecutableExtension();

		// Set default timezone (so no warnings are generated if it is not set)
		date_default_timezone_set('UTC');
	}

	/**
	 * Downloads and prepares a Joomla CMS site for testing
	 *
	 * @param   int   $use_htaccess  (1/0) Rename and enable embedded Joomla .htaccess file
	 *
	 * @return mixed
	 */
	public function prepareSiteForSystemTests($use_htaccess = 0)
	{
		// Caching cloned installations locally
		if (!is_dir('cache') || (time() - filemtime('cache') > 60 * 60 * 24))
		{
			if (file_exists('cache'))
			{
				$this->taskDeleteDir('cache')->run();
			}

			$this->_exec($this->buildGitCloneCommand());
		}

		// Get Joomla Clean Testing sites
		if (is_dir($this->cmsPath))
		{
			try
			{
				$this->taskDeleteDir($this->cmsPath)->run();
			}
			catch (Exception $e)
			{
				// Sorry, we tried :(
				$this->say('Sorry, you will have to delete ' . $this->cmsPath . ' manually. ');
				exit(1);
			}
		}

		$this->_copyDir('cache', $this->cmsPath);

		// Optionally change owner to fix permissions issues
		if (!empty($this->configuration->localUser) && !$this->isWindows())
		{
			$this->_exec('chown -R ' . $this->configuration->localUser . ' ' . $this->cmsPath);
		}

		// Optionally uses Joomla default htaccess file
		if ($use_htaccess == 1)
		{
			$this->_copy($this->cmsPath . '/htaccess.txt', $this->cmsPath . '/.htaccess');
			$this->_exec('sed -e "s,# RewriteBase /,RewriteBase /' . $this->cmsPath . '/,g" --in-place ' . $this->cmsPath . '/.htaccess');
		}
	}

	/**
	 * Downloads and prepares a Joomla CMS site for testing
	 *
	 * @return mixed
	 */
	public function prepareSiteForUnitTests()
	{
		// Make sure we have joomla
		if (!is_dir('joomla-cms3'))
		{
			$this->cloneJoomla();
		}

		if (!is_dir('joomla-cms3/libraries/vendor/phpunit'))
		{
			$this->getComposer();
			$this->taskComposerInstall('../composer.phar')->dir('joomla-cms3')->run();
		}

		// Copy extension. No need to install, as we don't use mysql db for unit tests
		$joomlaPath = __DIR__ . '/joomla-cms3';
		$this->_exec("gulp copy --wwwDir=$joomlaPath --gulpfile ../build/gulpfile.js");
	}

	/**
	 * Executes Selenium System Tests in your machine
	 *
	 * @param   array  $opts  Use -h to see available options
	 *
	 * @return mixed
	 */
	public function runTest($opts = [
		'test|t'	    => null,
		'suite|s'	    => 'acceptance'])
	{
		$this->getComposer();

		$this->taskComposerInstall()->run();

		if (isset($opts['suite'])
			&& ('api' === $opts['suite'] || 'apisoap' === $opts['suite']))
		{
			// Do not launch selenium when running API tests
		}
		else
		{
			$this->taskSeleniumStandaloneServer()
				->setURL("http://localhost:4444")
				->runSelenium()
				->waitForSelenium()
				->run()
				->stopOnFail();
		}

		// Make sure to Run the Build Command to Generate AcceptanceTester
		$this->_exec("vendor/bin/codecept build");

		if (!$opts['test'])
		{
			$this->say('Available tests in the system:');

			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(
						$this->testsFolder . $opts['suite'],
					RecursiveDirectoryIterator::SKIP_DOTS
				),
				RecursiveIteratorIterator::SELF_FIRST
			);

			$tests = array();

			$iterator->rewind();
			$i = 1;

			while ($iterator->valid())
			{
				if (strripos($iterator->getSubPathName(), 'cept.php')
					|| strripos($iterator->getSubPathName(), 'cest.php')
					|| strripos($iterator->getSubPathName(), '.feature'))
				{
					$this->say('[' . $i . '] ' . $iterator->getSubPathName());
					$tests[$i] = $iterator->getSubPathName();
					$i++;
				}

				$iterator->next();
			}

			$this->say('');
			$testNumber	= $this->ask('Type the number of the test  in the list that you want to run...');
			$opts['test'] = $tests[$testNumber];
		}

		$pathToTestFile = './' . $opts['suite'] . '/' . $opts['test'];

		// If test is Cest, give the option to execute individual methods
		if (strripos($opts['test'], 'cest'))
		{
			// Loading the class to display the methods in the class
			require './' . $opts['suite'] . '/' . $opts['test'];

			$classes = Nette\Reflection\AnnotationsParser::parsePhp(file_get_contents($pathToTestFile));
			$className = array_keys($classes)[0];

			if (strripos($className, 'cest'))
			{
				$testFile = new Nette\Reflection\ClassType($className);
				$testMethods = $testFile->getMethods(ReflectionMethod::IS_PUBLIC);

				foreach ($testMethods as $key => $method)
				{
					$this->say('[' . $key . '] ' . $method->name);
				}

				$this->say('');
				$methodNumber = $this->askDefault('Choose the method in the test to run (hit ENTER for All)', 'All');

				if ($methodNumber != 'All')
				{
					$method = $testMethods[$methodNumber]->name;
					$pathToTestFile = $pathToTestFile . ':' . $method;
				}
			}
		}

		$this->taskCodecept()
			->test($pathToTestFile)
			->arg('--steps')
			->arg('--debug')
			->arg('--fail-fast')
			->run()
			->stopOnFail();

		if (!('api' == $opts['suite'] || 'apisoap' == $opts['suite']))
		{
			$this->killSelenium();
		}
	}

	/**
	 * Preparation for running manual tests after installing Joomla/Extension and some basic configuration
	 *
	 * @param   int     $use_htaccess     (1/0) Rename and enable embedded Joomla .htaccess file
	 *
	 * @return void
	 */
	public function runTestPreparation($use_htaccess = 0)
	{
		$this->prepareSiteForSystemTests($use_htaccess);

		$this->getComposer();

		$this->taskComposerInstall()->run();

		$this->prepareReleasePackages();

		$this->taskSeleniumStandaloneServer()
			->setURL("http://localhost:4444")
			->runSelenium()
			->waitForSelenium()
			->run()
			->stopOnFail();

		// Make sure to Run the Build Command to Generate AcceptanceTester
		$this->_exec("vendor/bin/codecept build");

		$this->taskCodecept()
			// ->arg('--steps')
			// ->arg('--debug')
			->arg('--tap')
			->arg('--fail-fast')
			->arg($this->testsFolder . 'acceptance/install/')
			->run()
			->stopOnFail();
	}

	/**
	 * Function to Run tests in a Group
	 *
	 * @param   int     $use_htaccess     (1/0) Rename and enable embedded Joomla .htaccess file
	 * @param   string  $database_host    Optional. If using Joomla Vagrant Box do: $ vendor/bin/robo 0 gulp run:tests 33.33.33.58
	 * @param   string  $driver           Chrome / Selenium
	 *
	 * @return void
	 */
	public function runTests($use_htaccess = 0, $database_host = null, $driver = 'Chrome')
	{
		$this->prepareSiteForSystemTests($use_htaccess);

		$this->getComposer();

		$this->taskComposerInstall()->run();

		$this->prepareReleasePackages();

		switch ($driver)
		{
			case 'Selenium':
				$this->taskSeleniumStandaloneServer()
					->setURL("http://localhost:4444")
					->runSelenium()
					->waitForSelenium()
					->run()
					->stopOnFail();
				break;

			default:
				$this->runChromeDriver();
		}

		// Make sure to Run the Build Command to Generate AcceptanceTester
		$this->_exec("vendor/bin/codecept build");

		$this->taskCodecept()
			->arg('--steps')
			->arg('--debug')
			->arg('--tap')
			->arg('--fail-fast')
			->arg($this->testsFolder . 'acceptance/install/')
			->run()
			->stopOnFail();

		$this->taskCodecept()
			// ->arg('--steps')
			// ->arg('--debug')
			->arg('--tap')
			->arg('--fail-fast')
			->arg($this->testsFolder . 'acceptance/administrator/')
			->run()
			->stopOnFail();

		$this->taskCodecept()
			//  ->arg('--steps')
			//  ->arg('--debug')
			->arg('--tap')
			->arg('--fail-fast')
			->arg('api')
			->run()
			->stopOnFail();

		$this->taskCodecept()
			//  ->arg('--steps')
			//  ->arg('--debug')
			->arg('--tap')
			->arg('--fail-fast')
			->arg($this->testsFolder . 'acceptance/uninstall/')
			->run()
			->stopOnFail();

		switch ($driver)
		{
			case 'Selenium':
				$this->killSelenium();
				break;

			default:
				// Kill Chrome Driver (no idea how yet)
		}

	}

	/**
	 * Function to run unit tests
	 *
	 * @return void
	 */
	public function runUnitTests()
	{
		$this->prepareSiteForUnitTests();
		$this->_exec("joomla-cms3/libraries/vendor/phpunit/phpunit/phpunit")
			->stopOnFail();
	}

	/**
	 * Stops Selenium Standalone Server
	 *
	 * @return void
	 */
	public function killSelenium()
	{
		$this->_exec('curl http://localhost:4444/selenium-server/driver/?cmd=shutDownSeleniumServer');
	}

	/**
	 * Downloads Composer
	 *
	 * @return void
	 */
	private function getComposer()
	{
		// Make sure we have Composer
		if (!file_exists('./composer.phar'))
		{
			$this->_exec('curl --retry 3 --retry-delay 5 -sS https://getcomposer.org/installer | php');
		}
	}

	/**
	 * Runs Selenium Standalone Server.
	 *
	 * @return void
	 */
	public function runSelenium()
	{
		$this->_exec("vendor/bin/selenium-server-standalone >> selenium.log 2>&1 &");
	}

	/**
	 * Runs Chrome Driver
	 *
	 * @return void
	 */
	public function runChromeDriver()
	{
		$this->_exec("chromedriver --url-base=/wd/hub &");
	}

	/**
	 * Prepares the .zip packages of the extension to be installed in Joomla
	 */
	public function prepareReleasePackages()
	{
		$this->_exec("gulp release --skip-version --gulpfile ../build/gulpfile.js");
	}

	/**
	 * Looks for PHP Parse errors in core
	 */
	public function checkForParseErrors()
	{
		$this->_exec('php checkers/phppec.php ../extensions/components/com_redcore/ ../extensions/libraries/ ../extensions/modules/ ../extensions/plugins/');
	}

	/**
	 * Looks for missed debug code like var_dump or console.log
	 */
	public function checkForMissedDebugCode()
	{
		$this->_exec('php checkers/misseddebugcodechecker.php ../extensions/components/com_redcore/ ../extensions/libraries/ ../extensions/modules/ ../extensions/plugins/');
	}

	/**
	 * Check the code style of the project against a passed sniffers
	 */
	public function checkCodestyle()
	{
		if (!is_dir('checkers/phpcs/Joomla'))
		{
			$this->say('Downloading Joomla Coding Standards Sniffers');
			$this->_exec("git clone -b master --single-branch --depth 1 https://github.com/joomla/coding-standards.git checkers/phpcs/Joomla");
		}

		$this->taskExec('php checkers/phpcs.php')
				->printed(true)
				->run();
	}
	/**
	 * Check if local OS is Windows
	 *
	 * @return bool
	 */
	private function isWindows()
	{
		return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
	}

	/**
	 * Get the correct CMS root path
	 *
	 * @return string
	 */
	private function getCmsPath()
	{
		if (empty($this->configuration->cmsPath))
		{
			return 'joomla-cms3';
		}

		if (!file_exists(dirname($this->configuration->cmsPath)))
		{
			$this->say("Cms path written in local configuration does not exists or is not readable");

			return 'joomla-cms3';
		}

		return $this->configuration->cmsPath;
	}

	/**
	 * Get the executable extension according to Operating System
	 *
	 * @return void
	 */
	private function getExecutableExtension()
	{
		if ($this->isWindows())
		{
			// Check whether git.exe or git as command should be used, as on windows both are possible
			if (!$this->_exec('git.exe --version')->getMessage())
			{
				return '';
			}
			else
			{
				return '.exe';
			}
		}

		return '';
	}

	/**
	 * Get (optional) configuration from an external file
	 *
	 * @return \stdClass|null
	 */
	public function getConfiguration()
	{
		$configurationFile = __DIR__ . '/RoboFile.ini';

		if (!file_exists($configurationFile))
		{
			$this->say("No local configuration file");

			return null;
		}

		$configuration = parse_ini_file($configurationFile);

		if ($configuration === false)
		{
			$this->say('Local configuration file is empty or wrong (check is it in correct .ini format');

			return null;
		}

		return json_decode(json_encode($configuration));
	}

	/**
	 * Build correct git clone command according to local configuration and OS
	 *
	 * @return string
	 */
	private function buildGitCloneCommand()
	{
		$branch = empty($this->configuration->branch) ? 'staging' : $this->configuration->branch;

		return "git" . $this->executableExtension . " clone -b $branch --single-branch --depth 1 https://github.com/joomla/joomla-cms.git cache";
	}
}
