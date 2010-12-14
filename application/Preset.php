<?php
/**
 * Created by PhpStorm.
 * User: al
 * Date: Nov 19, 2010
 * Time: 12:31:26 PM
 * @version $Id$
 * To change this template use File | Settings | File Templates.
 */
class Preset
{
    const CACHE_TPL_CONFIG = 'config-cache';
    /**
     * @var string
     */
    protected static $_env;
   /**
    * @var ?#M#P#CZend_Tool_Project_Context_Zf_BootstrapFile._applicationDirectory.getPath|string
    */
    protected static $_path = APPLICATION_PATH;
    /**
     * @var string  Path to Logs directory
     */
    protected static $_logDir;
    /**
     * @var Zend_Log
     */
    protected static $_log;
    /**
     * @var string  Php Errors log file name 
     */
    protected static $_logFilePhpErrors = 'php.log';
    /**
     * @var string Path to Cache Directory
     */
    protected static $_cacheDir;

    /**
     * @var string Directory name contained config files ie application.ini
     */
    protected static $_configsDirectory = 'configs';

    /**
     * @var Array allowed config files
     */
    protected static $_masterConfigFiles = array
    (
        'application.ini',
        'login.ini',
        'resources.db.ini',
        'dbmanager.ini',
        'resources.log.ini',
        'resources.session.ini',
        'resources.frontcontroller.ini',
        'resources.cachemanager.core.ini',
        'resources.cachemanager.file.ini',
        'resources.cachemanager.memcached.ini',
        'resources.router.ini',
        'resources.translate.ini',
        'domains.locale.mapping.ini',
        //'someconfigfile.ini',
    );

    /**
     *
     * 
     * @static
     * @throws RuntimeException
     * @param string $env
     * @param string $logDir
     * @param string $cacheDir
     * @return Zend_Config
     */
    public static function get($env, $logDir, $cacheDir)
    {
        self::$_env = $env;
        self::$_logDir = $logDir;
        self::$_cacheDir = $cacheDir;

        try {
            define('DEBUG', (file_exists(self::$_path.DIRECTORY_SEPARATOR.self::$_configsDirectory.DIRECTORY_SEPARATOR.'debug.on')));

            if(!is_writable(dirname(self::$_logDir))) {
                throw new RuntimeException('Logs directory must be writeable, '.LOG_DIR);
            }
            ini_set('log_error', 1);
            ini_set('error_log', self::$_logDir.DIRECTORY_SEPARATOR.self::$_logFilePhpErrors);

            define('CACHING', (file_exists(self::$_path.DIRECTORY_SEPARATOR.self::$_configsDirectory.DIRECTORY_SEPARATOR.'cache.on')));

            if(!is_dir(self::$_cacheDir) || !is_writable(self::$_cacheDir)) {
                throw new RuntimeException('Please check cache directory exists and is writable, '.self::$_cacheDir . ','.__METHOD__.':'.__LINE__);
            }
            $config = self::thruConfig();
            if(!$config instanceof Zend_Config) {
                throw new RuntimeException('Can not initiate Config object');
            }
            self::thruPluginLoader();
            return $config;
        } catch (Exception $e) {
            $msg = "ENV [".self::$_env."] ".$e->getMessage();
            echo $msg;
            error_log("$msg \n{$e->getTraceAsString()}");
            die('<br/><strong>check also Log file for more information</strong>');
        }
    }
    
    /**
     *
     * 
     * @static
     * @return bool
     */
    public static function isDebug()
    {
        return (defined('DEBUG'))?DEBUG:false;
    }

    /**
     *
     * 
     * @static
     * @return bool
     */
    public static function isCachingOn()
    {
        return (defined('CACHING'))?CACHING:false;
    }

    /**
     *
     * 
     * @static
     * @return void
     */
    public static function log($customLogger = null)
    {
        if(self::$_log == null && $customLogger == null) {
            self::$_log = new Zend_Log(new Zend_Log_Writer_Stream(self::$_logDir.DIRECTORY_SEPARATOR.'app.log'));
        } elseif ($customLogger != null) {
            self::$_log = $customLogger;
        }
        return self::$_log;
    }

    /**
     *
     * 
     * @static
     * @return Zend_Config
     */
    public static function thruConfig()
    {
        if(self::isCachingOn() && self::getCacheManager()->hasCacheTemplate(self::CACHE_TPL_CONFIG)) {
            /** $cache Zend_Cache_Core */
            $cache = self::getCacheManager()->getCache(self::CACHE_TPL_CONFIG);
            if (isset($_REQUEST['cc'])) {
                $cache->clean('all');
                unset($_REQUEST['cc']);
            }
            $cacheId = 'congfig'.preg_replace('/[^\w]/i','_', self::$_cacheDir.self::$_env);
            if(null == ($config = $cache->load($cacheId))) {
                $config = self::_config();
                $cache->save($config, $cacheId, array('CONFIG_OBJ'));    
            }
            return $config;
        }    
        return self::_config();
    }

    /**
     * @static
     * @return void
     */
    public static function initCache()
    {
        $backType = 'file';

        if(file_exists(self::$_path.DIRECTORY_SEPARATOR.self::$_configsDirectory.DIRECTORY_SEPARATOR.'apc.on') &&
           extension_loaded('apc')) {
            $backType = 'apc';
        }

        $front = array(
            'write_control' => true,
            'automatic_serialization' => true,
            'ignore_user_abort' => true,
            'caching' => self::isCachingOn(),
            'logging' => false
        );    

        if(self::isDebug()) {
            $front['logging'] = true;
            $front['logger'] = self::log();
        }
        $fileFront = $front;
        $fileFront['ignore_missing_master_files'] = true;
        $fileFront['master_files'] = self::getConfigMasterFiles();

        //$fileBack['file_name_prefix'] = 'Im_Config_Object_';

        $cacheTemplate['frontend']['name'] = 'file';
        $cacheTemplate['frontend']['options'] = $fileFront;
        $cacheTemplate['backend']['name'] = $backType;
        $cacheTemplate['backend']['options']['cache_dir'] = self::$_cacheDir;

        self::getCacheManager()->setCacheTemplate(self::CACHE_TPL_CONFIG, $cacheTemplate);
    }

    /**
     *
     * 
     * @static
     * @return Zend_Cache_Manager
     */
    public static function getCacheManager()
    {
        static $cm;
        if(!$cm) {
            $cm = new Zend_Cache_Manager;
            self::initCache();
        }
        return $cm;
    }

    /**
     *
     * 
     * @static
     * @throws RuntimeException
     * @return Zend_Config
     */
    private static function _config()
    {       
        $config = new Zend_Config(array(), true);
        $masterFiles = self::getConfigMasterFiles();
        if(empty($masterFiles)) {
            throw new RuntimeException('Array within configurations master files contains no informations.');
        }
        foreach($masterFiles as $file) {

            if(!file_exists($file)) continue;
            $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if($suffix == "php" || $suffix == "inc") {
                $config->merge(new Zend_Config(include $file, true));
            } elseif ($suffix == "xml") {
                $config->merge(new Zend_Config_Xml($file, self::$_env, true));
            } elseif ($suffix == "ini") {
                $config->merge(new Zend_Config_Ini($file, self::$_env, true));
            }
        }
        $config->setReadOnly();
        if($config->count() < 1) {
            throw new RuntimeException('Empty Config object is provided, in '.__METHOD__.':'.__LINE__);
        }
        return $config;
    }

    /**
     *
     * 
     * @static
     * @return array
     */
    public static function getConfigMasterFiles()
    {
        static $mf;
        if(!$mf) {
            $mf = array_map(
                create_function('$v', 'return APPLICATION_PATH.DIRECTORY_SEPARATOR."'.self::$_configsDirectory.'".DIRECTORY_SEPARATOR.$v;'),
                self::$_masterConfigFiles
            );
        }
        return $mf;
    }

    /**
     *
     * 
     * @static
     * @return void
     */
    static public function thruPluginLoader()
    {
      $classFileIncCache = self::$_cacheDir.DIRECTORY_SEPARATOR.'pluginLoaderCache.php';
      if (file_exists($classFileIncCache)) {
          include_once $classFileIncCache;
      }
      $config = self::thruConfig();
      if ($config->pluginLoaderCache) {
          Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
      }
    }

}

