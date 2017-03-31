<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class ApisoapTester extends \Codeception\Actor
{
    use _generated\ApisoapTesterActions;

   /**
    * Define custom actions here
    */

    // @todo: I haven't seen an easy way to get the parameter from the config module but there must be a way. See http://phptest.club/t/how-to-get-a-config-parameter-from-a-module/701
    public function getWebserviceBaseUrl()
    {
        $yaml = new \Symfony\Component\Yaml\Parser();

        $config = $yaml->parse(file_get_contents(dirname(__DIR__) . '/api.suite.yml'));

        $url = $config['modules']['enabled'][2]['REST']['url'];
        $url = rtrim($url, "/");

        return $url;
    }
}
