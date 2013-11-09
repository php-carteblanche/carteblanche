<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * This is the global application bootstraper
 */

// Avoid over-loading
if (defined('_CBSAFE_'.basename(__FILE__).'_LOADED')) return;
@define('_CBSAFE_'.basename(__FILE__).'_LOADED', true);

/*
// some specific dev tools
define('_STANDARD_PHP_ERRORS', true); // false by default
define('_STANDARD_PHP_EXCEPTIONS', true); // false by default
define('_NONE_ON_SHUTDOWN', true); // false by default
define('_STANDARD_PHP_ASSERTS', true); // false by default
*/

require_once __DIR__.DIRECTORY_SEPARATOR.'bootstrap.php';

//spl_autoload_register(array('App\Loader', 'autoload'));

// the internal errors & exceptions handlers
/*
$abcdefghijklmnopqrstuvwxyz = \App\Debugger::getInstance();
if (!defined('_STANDARD_PHP_ERRORS') || (defined('_STANDARD_PHP_ERRORS') && true!==_STANDARD_PHP_ERRORS)) {
    define('_DEVDEBUG_ERROR_HANDLER', true);
    set_error_handler('appErrorHandler', error_reporting());
}
if (!defined('_STANDARD_PHP_EXCEPTIONS') || (defined('_STANDARD_PHP_EXCEPTIONS') && true!==_STANDARD_PHP_EXCEPTIONS)) {
    define('_DEVDEBUG_EXCEPTION_HANDLER', true);
    set_exception_handler('appExceptionHandler');
}
if (!defined('_NONE_ON_SHUTDOWN') || (defined('_NONE_ON_SHUTDOWN') && true!==_NONE_ON_SHUTDOWN)) {
    //define('_DEVDEBUG_SHUTDOWN_HANDLER', true);
    //define('_DEVDEBUG_SHUTDOWN_CALLBACK', "your callback");
    register_shutdown_function('appShutdownHandler', '\App\Router::render_exception');
}
define('_DEVDEBUG_DOCUMENT_ROOT', _ROOTPATH._WEBDIR);

// The internal assertions handler
if (!defined('_STANDARD_PHP_ASSERTS') || (defined('_STANDARD_PHP_ASSERTS') && true!==_STANDARD_PHP_ASSERTS)) {
    @assert_options(ASSERT_ACTIVE, 1);
    @assert_options(ASSERT_WARNING, 0);
    @assert_options(ASSERT_QUIET_EVAL, 1);
    @assert_options(ASSERT_BAIL, 0);
    @assert_options(ASSERT_CALLBACK, 'appAssertHandler');
}
*/
// -----------------------------
// application system functions

/**
 * Application specific "class_exists()" function with silent SPL autoload
 * This will not throw exception or error if the class doesn't exist but return false
 * @param string $classname The class name to test
 * @return bool TRUE if the class exists
 */
function appClassExists($classname)
{
    if (@class_exists('\App\Loader')) {
        return \App\Loader::classExists($classname);
    } else {
    	class_exists($classname);
	    return class_exists($classname, false);
	}
}
/**
 * System autoloader : will load a class file
 * @param string $classname The name of the class to load
 * @return bool TRUE if the classfile had been found
 */
function appAutoloadHandler($classname) 
{
    if (@class_exists('\App\Loader')) {
    	return \App\Loader::autoload($classname);
    } else {
        if (true===@class_exists($classname, false)) return true;
        $cls_file = (string) str_replace('\\', DIRECTORY_SEPARATOR, $classname.'.php');
        if ($_f = @file_exists($cls_file)) {
            require_once $cls_file;
            return true;
        } elseif (0!==error_reporting() && count(spl_autoload_functions())==1) {
            trigger_error( "Class '$classname' not found!", E_USER_ERROR);
        }
        return false;
    }
}

/**
 * Application specific shutdown handling
 */
function appShutdownHandler(&$arg = null, $callback = null)
{
	if (true===appClassExists( '\DevDebug\Debugger' )) {
		return \DevDebug\Debugger::shutdown(true, $callback);
	}
}

/**
 * Application specific error handling
 */
function appErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
  	// This error code is not in the error_reporting()
	if (!(error_reporting() & $errno)) return false;
	if (true===appClassExists( '\App\Exception\ErrorException' )) {
		$e = new \App\Exception\ErrorException($errstr, $errno, $errno, $errfile, $errline);
		echo $e;
	} else {
		return false;
	}
}

/**
 * Application specific exception handling
 */
function appExceptionHandler($e)
{
  	// The last call was escaped with '@'
	if (0===error_reporting()) return false;
	if (true===appClassExists( '\App\Exception\Exception' )) {
		$e = new \App\Exception\Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	echo $e;
}

/**
 * Assertions failure handler
 *
 * For PHP<5.4.8, a description can be setted writing a PHP comment in the evaluated code:
 *    assert('$a===$b; // my description ...');
 *
 * @return bool TRUE if the file had been find, FALSE otherwise
 */
function appAssertHandler($filename, $line, $error, $description = null)
{
	if (defined('_APP_MODE') && _APP_MODE=='dev') {
		if (is_null($description) && false!==strpos($error, '//')) {
			list($error, $description) = explode('//', $error);
			$description = trim($description);
		}
		$error = rtrim(trim($error), ';');
		if (true===appClassExists( '\UnitTest\Lib\Assertion' )) {
			$assert = new \UnitTest\Lib\Assertion( $filename, $line, $error, $description );
			$assert->setFailure();
			echo $assert;
		} else {
			$info = 'An assertion failed';
			if (!empty($description)) $info .= ' : '.$description;
			echo '<p><strong>' . $info . '</strong><br />' . $error . ' at [' . $filename . ':' . $line . ']</p>';
		}
	}
}
/*
$a=10;
//assert('$a<0', 'mlkjmlkj'); // only since PHP5.4.8
assert('$a==0');
assert('$a<0; // my comment for this assertion');
*/

// Endfile