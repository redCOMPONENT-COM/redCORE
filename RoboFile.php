<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    /**
     * Executes Selenium System Tests in your machine
     *
     * @param null $seleniumPath
     */
    public function testAcceptance($seleniumPath = null)
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

        // Get Joomla Clean Testing sites
        if (is_dir('tests/joomla-cms3')) {
            $this->taskDeleteDir('tests/joomla-cms3')->run();
        }

        $this->_exec('git clone -b staging --single-branch --depth 1 https://github.com/joomla/joomla-cms.git tests/joomla-cms3');

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