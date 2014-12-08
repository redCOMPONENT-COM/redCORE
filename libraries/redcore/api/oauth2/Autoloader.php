<?php

namespace OAuth2;

/**
 * Autoloads OAuth2 classes
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class Autoloader
{
    private $dir;

    public function __construct($dir = null)
    {
        if (is_null($dir)) {
            $dir = dirname(__FILE__).'/..';
        }
        $this->dir = $dir;
    }
    /**
     * Registers OAuth2\Autoloader as an SPL autoloader.
     */
    public static function register($dir = null)
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self($dir), 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    public function autoload($class)
    {
        if (0 !== strpos($class, 'OAuth2')) {
            return;
        }

	    $class = str_replace('\\', '/', $class);

	    if (strpos($class, 'OAuth2') === 0)
	    {
		    $count = 1;
		    $class = str_replace('OAuth2', 'oauth2', $class, $count);
	    }

        if (file_exists($file = $this->dir . '/' . $class . '.php')) {
            require $file;
        }
    }
}
