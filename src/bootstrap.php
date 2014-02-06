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

/**
 * The application default mode
 */
@define('_APP_MODE', 'prod');

/**
 * are we in CLI
 */
@define('_CLI_CALL', (strtolower(php_sapi_name()) == 'cli'));

/**
 * Useful links
 */
@define('_CBBOOTSTRAP_CB_INFO', 'http://github.com/php-carteblanche/carteblanche');
@define('_CBBOOTSTRAP_PHP_INFO', 'http://www.php.net/');
@define('_CBBOOTSTRAP_COMPOSER_INFO', 'http://getcomposer.org/doc/00-intro.md#using-composer');

/**
 * PHP version
 * Error if php < 5.3 (or _PHP_MINVERSION)
 * NOTE : Above this line, everything must be PHP4 COMPATIBLE !!
 */
@define('_PHP_MINVERSION', 5.3);
if (version_compare(PHP_VERSION, _PHP_MINVERSION, '<')) {
    die(
        (true===_CLI_CALL ? PHP_EOL : '<br />')
        .'We are sorry but this application requires PHP in version '
        .(true===_CLI_CALL ? '' : '<var>')
        ._PHP_MINVERSION
        .(true===_CLI_CALL ? '' : '</var>')
        .' minus [current version: '
        .(true===_CLI_CALL ? '' : '<var>')
        .PHP_VERSION
        .(true===_CLI_CALL ? '' : '</var>')
        .'].'
        .(true===_CLI_CALL ? PHP_EOL : '<br />')
        .'For more information, go to php.net website ('
        .(true===_CLI_CALL ? '' : '<a href="'._CBBOOTSTRAP_PHP_INFO.'">')
        ._CBBOOTSTRAP_PHP_INFO
        .(true===_CLI_CALL ? '' : '</a>')
        .') or contact your server\'s administrator.'
        .(true===_CLI_CALL ? PHP_EOL : '<br />')
        .'For Carte Blanche informations, see: '
        .(true===_CLI_CALL ? '' : '<a href="'._CBBOOTSTRAP_CB_INFO.'">')
        ._CBBOOTSTRAP_CB_INFO
        .(true===_CLI_CALL ? '' : '</a>')
        .'.'
        .(true===_CLI_CALL ? PHP_EOL.PHP_EOL : '<br />')
    );
}

/**
 * Security of this interface
 * Error if `_ROOTFILE` is not defined
 */
if (!defined('_ROOTFILE')) {
    die(
        (true===_CLI_CALL ? PHP_EOL : '<br />')
        .'The '
        .(true===_CLI_CALL ? '\'' : '<var>')
        .'_ROOTFILE'
        .(true===_CLI_CALL ? '\'' : '</var>')
        .' constant can not be found! You can not access this file directly [file path: '
        .(true===_CLI_CALL ? '' : '<var>')
        .basename(__FILE__)
        .(true===_CLI_CALL ? '' : '</var>')
        .']!'
        .(true===_CLI_CALL ? PHP_EOL : '<br />')
        .'For Carte Blanche informations, see: '
        .(true===_CLI_CALL ? '' : '<a href="'._CBBOOTSTRAP_CB_INFO.'">')
        ._CBBOOTSTRAP_CB_INFO
        .(true===_CLI_CALL ? '' : '</a>')
        .'.'
        .(true===_CLI_CALL ? PHP_EOL.PHP_EOL : '<br />')
    );
}

/**
 * Application file names, paths & directories
 *
 * Rules to follow:
 * - all paths MUST contain the trailing slash
 * - `_` prefix is used for all contsants names
 * - `FILE` suffix is used for file names (without path)
 * - `DIRNAME` suffix is used for directory names (without path)
 * - `DIR` suffix is used for relative paths (most of the time relative from root)
 * - `PATH` suffix is used for absolute paths
 * - `HTTP` suffix is used for relative paths HTTP accessible
 *
 * The default schema is:
 *      
 *     | [root dir]             => _ROOTPATH
 *     |
 *     | bin/                   => _BINDIR
 *     | ---- ...
 *     |
 *     | config/                => _CONFIGDIR
 *     | ----- ...
 *     |
 *     | [doc/]                 => _DOCDIR
 *     | ---- ...
 *     |
 *     | i18n/                  => _LANGUAGEDIR
 *     | ---- ...
 *     |
 *     | src/                   => _SRCDIR
 *     | ---- App/
 *     | ---- CarteBlanche/
 *     | ---- bundles/          => _BUNDLESDIR
 *     | ---- tools/            => _TOOLSDIR
 *     | ---- vendor/           => _VENDORDIRNAME
 *     | ---- [lib/]            => _LIBDIR
 *     |
 *     | user/                  => _USERDIR
 *     | ---- ...
 *     |
 *     | var/                   => _VARDIR
 *     | ---- app_cache/        => _APPCACHEDIR
 *     | ---- ...
 *     |
 *     | www/                   => _WEBDIR _ROOTHTTP
 *     | ---- assets/           => _ASSETSDIR
 *     | ---- skins/            => _SKINSDIR
 *     | ---- vendor/           => _VENDORDIRNAME
 *     | ---- tmp/              => _TMPDIRNAME
 *     | ---- ...
 *      
 */

/**
 * _ROOTPATH : the dirname of the whole CarteBlanche installation
 *
 * MUST exists
 * MUST NOT be writable
 * MUST NOT be apache accessible
 */
@define('_ROOTPATH', realpath(dirname(__FILE__)) == DIRECTORY_SEPARATOR ?
    realpath('..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR
    :
    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR
);

/**
 * _ROOTHTTP : the base URL to use to construct the application routes (found from the current domain and path URL)
 */
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

// -----------------------------
// Root directories

/**
 * _BINDIR : the binaries directory
 *
 * MUST exists
 * MUST NOT be writable
 * MUST NOT be apache accessible
 */
@define('_BINDIR', 'bin'.DIRECTORY_SEPARATOR);

/**
 * _CONFIGDIR : the config files directory
 *
 * MUST exists
 * MUST be writable
 * MUST NOT be apache accessible
 */
@define('_CONFIGDIR', 'config'.DIRECTORY_SEPARATOR);

/**
 * _DOCDIR : the documentation files directory
 *
 * MUST exists
 * MUST be writable
 * MUST NOT be apache accessible
 */
@define('_DOCDIR', 'doc'.DIRECTORY_SEPARATOR);

/**
 * _LANGUAGEDIR : the language files directory
 *
 * MUST exists
 * MUST be writable
 * MUST NOT be apache accessible
 */
@define('_LANGUAGEDIR', 'i18n'.DIRECTORY_SEPARATOR);

/**
 * _SRCDIR : the scripts directory
 *
 * MUST exists
 * MUST NOT be writable
 * MUST NOT be apache accessible
 */
@define('_SRCDIR', 'src'.DIRECTORY_SEPARATOR);

/**
 * _USERDIR : the user overrides directory
 *
 * MAY NOT be writable
 * MUST NOT be apache accessible
 */
@define('_USERDIR', 'user'.DIRECTORY_SEPARATOR);

/**
 * _VARDIR : the variable files directory
 *
 * MUST exists
 * MUST be writable
 * MUST NOT be apache accessible
 */
@define('_VARDIR', 'var'.DIRECTORY_SEPARATOR);

/**
 * _WEBDIR : the www directory for PHP execution
 *
 * MUST exists
 * MUST NOT be writable
 * MUST be apache accessible
 */
@define('_WEBDIR', 'www'.DIRECTORY_SEPARATOR);

// -----------------------------
// Global directory names

/**
 * _VIEWSDIR : the default views sub-directory name
 */
@define('_VIEWSDIRNAME', 'views'.DIRECTORY_SEPARATOR);

/**
 * _TMPDIRNAME : the temporary sub-directory name
 */
@define('_TMPDIRNAME', 'tmp'.DIRECTORY_SEPARATOR);

/**
 * _VENDORDIRNAME : the vendor sub-directory name
 */
@define('_VENDORDIRNAME', 'vendor'.DIRECTORY_SEPARATOR);

// -----------------------------
// PHP scripts sub-directories

/**
 * _BUNDLESDIR : the bundles third-party sub-directory
 */
@define('_BUNDLESDIR', 'bundles'.DIRECTORY_SEPARATOR);

/**
 * _TOOLSDIR : the tools third-party sub-directory
 */
@define('_TOOLSDIR', 'tools'.DIRECTORY_SEPARATOR);

/**
 * _LIBDIR : the php third-party libraries
 */
@define('_LIBDIR', 'lib'.DIRECTORY_SEPARATOR);

// -----------------------------
// Var sub-directories

/**
 * _APPCACHEDIR : the application (PHP) temporary directory
 *
 * MUST exists
 * MUST be writable
 * MUST NOT be apache accessible
 */
@define('_APPCACHEDIR', _VARDIR.'app_cache'.DIRECTORY_SEPARATOR);

/**
 * _APPLOGSDIR : the application log files
 *
 * MUST exists
 * MUST be writable
 * MUST NOT be apache accessible
 */
@define('_APPLOGSDIR', _VARDIR.'log'.DIRECTORY_SEPARATOR);

// -----------------------------
// Web accessible sub-directories

/**
 * _WEBTMPDIR : special relative from root path for creation
 *
 * MUST exists
 * MUST be writable
 * MUST be apache accessible
 */
@define('_WEBTMPDIR', _WEBDIR._TMPDIRNAME);

/**
 * _SKINSDIR : skins sets directory
 *
 * MUST be apache accessible
 */
@define('_SKINSDIR', 'skins'.DIRECTORY_SEPARATOR);

/**
 * _ASSETSDIR: The assets web path
 *
 * MUST be apache accessible
 */
@define('_ASSETSDIR', 'assets'.DIRECTORY_SEPARATOR);

// -----------------------------
// CarteBlanche specifics

/**
 * The application default bootstrapper
 */
define('_APP_BOOTSTRAPFILE', 'safeBootstrap.php');

// -----------------------------
// initial setup

// defining include pathes
set_include_path(
    get_include_path()
    .PATH_SEPARATOR._ROOTPATH
    .PATH_SEPARATOR._ROOTPATH._SRCDIR
);

/**
 * If we are in CLI builder, stop here
 */
if (true===_CLI_CALL && defined('_CARTEBLANCHE_BUILDER') && true===_CARTEBLANCHE_BUILDER) return;

/**
 * PHP namespaces autoloader : Composer autoloader
 */
$composerAutoLoader = __DIR__.DIRECTORY_SEPARATOR._VENDORDIRNAME.'autoload.php';
if (@file_exists($composerAutoLoader)) {
    require_once $composerAutoLoader;
} else {
    die(
        (true===_CLI_CALL ? PHP_EOL : '<br />')
        .'You need to build your Carte Blanche installation: '
        .(true===_CLI_CALL ? '' : 'in a console, ')
        .'run '
        .(true===_CLI_CALL ? '\'' : '<var>')
        .'~$ (sh or bash) (/path/to/carte-blanche/)bin/build.sh -h'
        .(true===_CLI_CALL ? '\'' : '</var>')
        .'.'
        .(true===_CLI_CALL ? PHP_EOL : '<br />')
        .'Alternately, you can run Composer on the project to build dependencies and auto-loading (see: '
        .(true===_CLI_CALL ? '' : '<a href="'._CBBOOTSTRAP_COMPOSER_INFO.'">')
        ._CBBOOTSTRAP_COMPOSER_INFO
        .(true===_CLI_CALL ? '' : '</a>')
        .').'
        .(true===_CLI_CALL ? PHP_EOL : '<br />')
        .'For Carte Blanche informations, see: '
        .(true===_CLI_CALL ? '' : '<a href="'._CBBOOTSTRAP_CB_INFO.'">')
        ._CBBOOTSTRAP_CB_INFO
        .(true===_CLI_CALL ? '' : '</a>')
        .'.'
        .(true===_CLI_CALL ? PHP_EOL.PHP_EOL : '<br />')
    );
}

// Endfile