<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('CACHE_DIR')
    || define('CACHE_DIR', ((getenv('CACHE_DIR'))?getenv('CACHE_DIR'):APPLICATION_PATH. '/../cache'));

defined('LOG_DIR')
    || define('LOG_DIR', ((getenv('LOG_DIR'))?getenv('LOG_DIR'):APPLICATION_PATH . '/../logs'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
require_once APPLICATION_PATH.'/Preset.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    Preset::get(APPLICATION_ENV, LOG_DIR, CACHE_DIR)
);
$application->bootstrap()
    ->run();
