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
     * 
     * @return Zend_Config
     */
    public static function getCfg()
    {
        static $cfg;
        if($cfg == null){
            $cfg = Preset::thruConfig();
        }
        return $cfg;
    }

    /**
     *
     * 
     * @return bool
     */
    public static function isDebug(){
        return Preset::isDebug();
    }

    /**
     *
     *
     * @return bool
     */
    public static function isCacheOn(){
        return Preset::isCachingOn();
    }

    public static function getLog($customLog = null){
        return Preset::log($customLog);
    }
    /** TODO: resources injector must here */
    
}
