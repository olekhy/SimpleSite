<?php


/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Common Application class is a parent for all classes application wide
 *
 * Long description for file (if any)...
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Application Webpage
 * @package    Application
 * @subpackage    App_Abstract_Abstract
 * @author     Original Author <olekhy@gmail.com>
 * @author     Another Author <saschaprolic@gmail.com>
 * @copyright  2009-2005 The Webfact GmbH, Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id$
 * @link       http://.../PackageName
 * @see        ()
 * @since      File available since Release 1.0.0
 * @deprecated No
 */

// Place includes, constant defines and $_GLOBAL settings here.

/**
 * Common Application class is a parent for all classes application wide
 *
 * Long description for class (if any)...
 *
 * @category   Application
 * @package    Application
 * @subpackage    App_Abstract_Abstract
 * @author     Original Author <olekhy@gmail.com>
 * @copyright  2009-2010 The Webfact GmbH, Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id$
 * @link       http://
 * @see        ()
 * @since      Class available since Release 1.0.0
 * @deprecated No
 */

abstract class App_Core_Abstract implements App_Core_Interface
{
    /**
     *
     * @var Zend_Config
     */
    protected static $_cfg;

    /**
     *
     * @var Zend_Log
     */
    protected static $_log;

    /**
     * @var Zend_Cache_Manager
     */
    protected static $_cacheManager;

    /**
     * @static
     * @throws RuntimeException|UnexpectedValueException
     * @return Zend_Log
     */
    public function getLog()
    {
        if(! self::$_log instanceof Zend_Log)
        {
            if(!defined('APPLICATION_REGISTRY_LOG'))
            {
                throw new RuntimeException('Constant APPLICATION_REGISTRY_LOG must be defined with value "Zend_Log"');
            }
            try
            {
                if( ! (self::$_log = Zend_Registry::get(APPLICATION_REGISTRY_LOG)) instanceof Zend_log)
                {
                    throw new UnexpectedValueException('Zend_Log was not registered in Zend_Registry.');
                }
            }
            catch(Exception $e)
            {
                error_log($e->getMessage());
                echo $e->getMessage();
            }
        }
        return self::$_log;
    }

    /**
     * Is debugging is on then logging leves "debug" and "info" are working normaly
     * else calls debug or info methods are pass
     *
     * @static
     * @return boolean
     */
    public function isDebug()
    {
        if(DEBUG)
        {
            return true;
        }
        else return false;
    }

    /**
     * @static
     * @throws RuntimeException|UnexpectedValueException
     * @return Zend_Config
     */
    public function getConfig()
    {
        if(self::$_cfg === null)
        {
            if(!defined('APPLICATION_REGISTRY_CONFIG'))
            {
                throw new RuntimeException('Constant APPLICATION_REGISTRY_CONFIG must be defined with value "Zend_Config"');
            }

        }
        try
        {
            if( ! (self::$_cfg = Zend_Registry::get(APPLICATION_REGISTRY_CONFIG)))
            {
                throw new UnexpectedValueException('Config object was not registered in Zend_Registry or is empty');
            }
        }
        catch(Exception $e)
        {
            error_log($e->getMessage());
            echo $e->getMessage();
        }
        return self::$_cfg;
    }
    /**
     *
     * @static
     * @throws RuntimeException|UnexpectedValueException
     * @return Zend_Cache_Manager
     */
    public function getCacheManager()
    {
        if(! self::$_cacheManager instanceof Zend_Log)
        {
            if(!defined('APPLICATION_REGISTRY_CACHE_MANAGER'))
            {
                throw new RuntimeException('Constant APPLICATION_REGISTRY_CACHE_MANAGER must be defined with value "Zend_Cache_Manager"');
            }
            try
            {
                if( ! (self::$_cacheManager = Zend_Registry::get(APPLICATION_REGISTRY_CACHE_MANAGER)) instanceof Zend_Cache_Manager)
                {
                    throw new UnexpectedValueException('Zend_Cache_Manager was not registered in Zend_Registry.');
                }
            }
            catch(Exception $e)
            {
                error_log($e->getMessage());
                echo $e->getMessage();
            }
        }
        return self::$_cacheManager;
    }

    /**
     * Wrapping of get Config
     * @see self::getConfig()
     * @static
     * @return Zend_Config
     */
    public function getApplicationConfig()
    {
        return self::getConfig();
    }

}
