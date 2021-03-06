<?php
/**
 * User: Arris
 * Date: 24.09.2017, time: 14:53
 */
if (version_compare(phpversion(), '7.0', '<')) {
    die('PHP 7.0+ required for this engine');
}

// defines
define('PATH_CONFIG',   __ROOT__ . '/.config/');
define('PATH_ENGINE',   __ROOT__ . '/engine/');
define('PATH_FRONTEND', __ROOT__ . '/frontend/');
define('PATH_TEMPLATES',__ROOT__ . '/templates/');
define('PATH_STORAGE',  __ROOT__ . '/storage/');

// Base libs
require_once (__ROOT__ . '/engine/core.functions.php');

// WebSun Template Engine
ini_set('pcre.backtrack_limit', 1024*1024);
require_once(__ROOT__ . '/engine/thirdparty/websun.php');

// Classes
require_once (__ROOT__ . '/engine/core.LMEConfig.php');
require_once (__ROOT__ . '/engine/core.LMEAuth.php');

require_once (__ROOT__ . '/engine/classes/class.INI_Config.php');
require_once (__ROOT__ . '/engine/classes/class.DBConnectionLite.php');
require_once (__ROOT__ . '/engine/classes/class.SVGParser.php');
require_once (__ROOT__ . '/engine/classes/class.CLIConsole.php');
require_once (__ROOT__ . '/engine/classes/class.Template.php');
require_once (__ROOT__ . '/engine/classes/proto.UnitPrototype.php');
require_once (__ROOT__ . '/engine/classes/class.LMEMapConfigLoader.php');

// INIT
$main_config = new INI_Config(PATH_CONFIG . 'config.ini');
$main_config->append(PATH_CONFIG . 'db.ini');
LMEConfig::set_mainconfig( $main_config );

$dbi = new DBConnectionLite('livemap', $main_config);

if (!$dbi->connection()) {
    die($dbi->status());
}

LMEConfig::set_dbi( $dbi );

// PHPAuth
if ($main_config->get('auth/phpauth_enabled')) {
    require_once(__ROOT__ . '/engine/thirdparty/phpauth/config.class.php');
    require_once(__ROOT__ . '/engine/thirdparty/phpauth/auth.class.php');

    // PHPAuth init
    LMEAuth::set_config( new PHPAuth\Config( PATH_CONFIG . 'phpauth.ini' ));
    LMEAuth::init( new PHPAuth\Auth( LMEConfig::get_dbi()->getconnection(), LMEAuth::get_config() ) );
}

// LiveMap Engine
require_once (__ROOT__ . '/engine/core.LiveMapEngine.php');

LMEConfig::get_mainconfig()->set('copyright/title', 'LiveMap Engine, version 0.6.14 "Astolfo"');

