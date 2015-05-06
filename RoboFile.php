<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
require_once 'vendor/autoload.php';


class RoboFile extends \Robo\Tasks
{
    use \redcomponent\robo\loadTasks;

    public function sayHelloWorld()
    {
        $result = $this->taskHelloWorld()->run();

        return $result;
    }

    /**
     * @param string $slackChannel              The Slack Channel ID
     * @param string $slackToken                Your Slack authentication token.
     * @param string $codeceptionOutputFolder   Optional. By default tests/_output
     *
     * @return mixed
     */
    public function sendCodeceptionOutputToSlack($slackChannel, $slackToken = null, $codeceptionOutputFolder = null)
    {
        if (is_null($slackToken)) {
            $slackToken = getenv('SLACK_ENCRYPTED_TOKEN');
        }

        $result = $this->taskSendCodeceptionOutputToSlack($slackChannel, $slackToken, $codeceptionOutputFolder)->run();

        return $result;
    }

    /**
     * Downloads and prepares a Joomla CMS site for testing
     */
    public function prepareSiteForSystemTests()
    {
        // Get Joomla Clean Testing sites
        if (is_dir('tests/joomla-cms3')) {
            $this->taskDeleteDir('tests/joomla-cms3')->run();
        }

        $this->_exec('git clone -b staging --single-branch --depth 1 https://github.com/joomla/joomla-cms.git tests/joomla-cms3');
        $this->say('Joomla CMS site created at tests/joomla-cms3');
    }

    /**
     * Executes Selenium System Tests in your machine
     *
     * @param null $seleniumPath
     */
    public function runTests($seleniumPath = null)
    {
        if(!$seleniumPath) {
            if (!file_exists('selenium-server-standalone.jar')) {
                $this->say('Downloading Selenium Server, this may take a while.');
                $this->taskExec('wget')
                    ->arg('http://selenium-release.storage.googleapis.com/2.45/selenium-server-standalone-2.45.0.jar')
                    ->arg('-O selenium-server-standalone.jar')
                    ->printed(false)
                    ->run();
            }
            $seleniumPath = 'selenium-server-standalone.jar';
        }

        // running Selenium server in background
        $this->taskExec('java -jar ' . $seleniumPath)
            ->background()
            ->run();

        // Make sure we have Composer
        if (!file_exists('./composer.phar')){
            $this->_exec('curl -sS https://getcomposer.org/installer | php');
        }

        $this->taskComposerUpdate()->run();
        
        // Loading Symfony Command and running with passed argument
        $this->taskCodecept()->getCommand('build');

        $this->taskCodecept()
            ->suite('acceptance')
            ->arg('--steps')
            ->arg('--debug')
            ->run();

        // Kill selenium server
        //$this->_exec('curl http://localhost:4444/selenium-server/driver/?cmd=shutDownSeleniumServer');
    }
}