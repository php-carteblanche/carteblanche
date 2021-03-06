<?php
/**
 * This file is part of the CarteBlanche PHP framework.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * License Apache-2.0 <http://github.com/php-carteblanche/carteblanche/blob/master/LICENSE>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Web-services production request interface
 */

/**
 * Show errors at least initially
 *
 * `E_ALL` => for hard dev
 * `E_ALL & ~E_STRICT` => for hard dev in PHP5.4 avoiding strict warnings
 * `E_ALL & ~E_NOTICE & ~E_STRICT` => classic setting
 */
//@ini_set('display_errors','1'); @error_reporting(E_ALL);
//@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_STRICT);
@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

//define('_STANDARD_PHP_ERRORS', true);     // false by default
//define('_STANDARD_PHP_EXCEPTIONS', true); // false by default
//define('_NONE_ON_SHUTDOWN', true);        // false by default
//define('_STANDARD_PHP_ASSERTS', true);    // false by default

// Here can be defined the following constants:
//
// - _ROOTFILE : the filename of the current web interface (current filename)
// - _ROOTDIR : the dirname of the whole CarteBlanche installation (this path must exists)
// - _WEBDIR : the dirname of the web accessibles files for this interface and website (this path must exists)
// - _ROOTHTTP : the base URL to use to construct the application routes
//
// Note that these values will be defined after with default values by the application, except for
// the _ROOTFILE, which MUST be defined on the current filename.

// _ROOTFILE : the filename handling the current request
define('_ROOTFILE', basename(__FILE__));

// _ROOTPATH : the dirname of the whole CarteBlanche installation
define('_ROOTPATH', realpath(dirname(__FILE__)) == DIRECTORY_SEPARATOR ?
    realpath('..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR
    :
    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR
);

// _ROOTHTTP : the base URL to use to construct the application routes (found from the current domain and path URL)
if (!defined('_ROOTHTTP')) {
    $_roothttp = '';
    if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
        $_roothttp = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])!='off') ? 'https://' : 'http://';
        $_roothttp .= $_SERVER['HTTP_HOST'];
    }
    if (isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF'])) {
        $_roothttp .= str_replace( '\\', '/', dirname($_SERVER['PHP_SELF']));
    }
    if (strlen($_roothttp)>0 && substr($_roothttp, -1) != '/') $_roothttp .= '/';
    define('_ROOTHTTP', $_roothttp);
}

// Composer autoloader
$launcher = __DIR__.'/../src/vendor/autoload.php';
if (@file_exists($launcher)) {
    require_once $launcher;
} else {
    die("You need to run to install your application using Composer!");
}

// the application
$main = \CarteBlanche\App\Kernel::create(
    null,   // a configuration INI file
    null,   // user options array
    'prod'   // the application mode: 'dev' or 'prod'
);
if ($main) {
    $main->getContainer()->get('config')->set(
        array('routing.mvc.default_controller'=>'WebserviceController'), true
    );
    $main->distribute();
} 
else trigger_error("Main application kernel can't be loaded!", E_USER_ERROR);

// Endfile