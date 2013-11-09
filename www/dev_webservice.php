<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * Development webservice interface
 *
 */

// Avoid over-loading
if (defined('_CBSAFE_'.basename(__FILE__).'_LOADED')) return;
@define('_CBSAFE_'.basename(__FILE__).'_LOADED', true);

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

// ----------------------------------------------------------------
// ---------- USER CONFIG -----------------------------------------

/**
 * The application mode : 'dev' or 'prod'
 */
define('_APP_MODE', 'dev');
//define('_STANDARD_PHP_ERRORS', true); // false by default
//define('_STANDARD_PHP_EXCEPTIONS', true); // false by default
//define('_NONE_ON_SHUTDOWN', true); // false by default
//define('_STANDARD_PHP_ASSERTS', true); // false by default

/**
 * A configuration INI file to use from 'config/'
 */
$config_file = null;

/**
 * A configuration array to use
 */
$user_config = array();

/**
 * The default timezone (default is "Europe/London" - UTC)
 */
#$default_timezone = '';

/**
 * minimum PHP required version
 */
//define('_PHP_MINVERSION', 6);

// ---------- END USER CONFIG -------------------------------------
// ----------------------------------------------------------------
// ---------- DO NOT EDIT BELOW -----------------------------------
//
// Here can be defined the following constants:
//
// - _ROOTFILE : the filename of the current web interface (current filename)
// - _ROOTDIR : the dirname of the whole CarteBlanche installation (this path must exists)
// - _WEBDIR : the dirname of the web accessibles files for this interface and website (this path must exists)
// - _ROOTHTTP : the base URL to use to construct the application routes
//
// Note that these values will be defined after with default values by the application, except for
// the _ROOTFILE, which MUST be defined on the current filename.

/**
 * This file is our root file
 */
define('_ROOTFILE', basename(__FILE__));

/**
 * The global application launcher
 */
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'app.php';

// the application
$main = \CarteBlanche\App\Kernel::create(
    isset($config_file) ? $config_file : null,
    isset($user_config) ? $user_config : null,
    _APP_MODE
);
if ($main) $main
//    ->handles(new \App\Request)
    ->distribute();
else trigger_error("Main application kernel can't be loaded!", E_USER_ERROR);

// Endfile