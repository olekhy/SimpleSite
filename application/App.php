<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Created by JetBrains PhpStorm.
 * User: al
 * Date: 3/13/11
 * Time: 11:35 AM
 *
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * PHP versions 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   ?CategoryName?
 * @package    ?PackageName?
 * @author     Original Author ${AUTHOR} <${AUTHOREMAIL}>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2010 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    SVN: $Id: App.php 4448 2011-03-14 00:53:21Z khueoreeskyy@webfact.de $
 * @link       http://
 * @see        ...
 * @since      File available since Release 1.0.0
 * @deprecated File is not deprecated
 */

// Place includes, constant defines and $_GLOBAL settings here.

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   ?CategoryName?
 * @package    ?PackageName?
 * @author     Original Author  ${AUTHOR} <${AUTHOREMAIL}>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2010 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://
 * @see        ...
 * @since      Class available since Release 1.0.0
 * @deprecated Class is not deprecated
 */
 
/**
 * @property
 * @method 
 */

class App 
{
    static private $dbTableObjects;
    
    /**
     * @static
     * @param  $modelClassName
     * @return App_Model_Table
     */
    static public function model($modelClassName)
    {                   
        if(isset(self::$dbTableObjects[$modelClassName]) && self::$dbTableObjects[$modelClassName] != null)
        {
            return self::$dbTableObjects[$modelClassName];
        }
        $class = 'App_Model_' . $modelClassName;
        return self::$dbTableObjects[$modelClassName] = new $class(array('table' => self::table($modelClassName)));
    }
    
    /**
     * @static
     * @param  $class
     * @return App_Model_Table
     */
    static public function modelByFullClassName($class)
    {
        $parts = explode('_', $class);
        $className = array_pop($parts);
        return self::model($className);
    }
    
    /**
     * @static
     * @param  $tableObject
     * @return App_Model_Table
     */
    static public function modelByObject($tableObject)
    {
        return self::modelByFullClassName(get_class($tableObject));    
    }
    /**
     * @static
     * @return Zend_Config
     */
    static public function cfg()
    {
        return Zend_Registry::get('APPCFG');
    }
    /**
     * @static
     * @return bool
     */
    static public function isDebug()
    {
        return Preset::isDebug();
    }
    
    static public function table($tableClass)
    {                     
        $tableClass = 'App_Model_Table_' . $tableClass;
        return new $tableClass;
    }
    
    static public function log()
    {
        return Preset::log();
    }
}
