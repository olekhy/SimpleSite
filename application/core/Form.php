<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Common Form class is a parent for all Form classes application wide
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
 * @category   HTML, Form, Validate
 * @package    Form
 * @subpackage App_Abstract
 * @package    Zend_Form
 * @author     Original Author <olekhy@gmail.com>
 * @author     Another Author <saschaprolic@gmail.com>
 * @copyright  2009-2005 The Webfact GmbH, Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    SVN: $Id:$
 * @link       http://.../PackageName
 * @see        ()
 * @since      File available since Release 1.2.0
 * @deprecated No
 */

// Place includes, constant defines and $_GLOBAL settings here.

/**
 * This generic (HTML)Form class contains some customized methods for better handling of Zend_Form object
 *
 * Long description for class (if any)...
 *
 * @category   HTML, Form, Validate
 * @package    Form
 * @subpackage App_Abstract
 * @subpackage App_Abstract_Form
 * @package    Zend
 * @subpackage Zend_Form
 * @author     Original Author <olekhy@gmail.com>
 * @author     Another Author <saschaprolic@gmail.com>
 * @copyright  2009-2010 The Webfact GmbH, Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://
 * @see        ()
 * @since      Class available since Release 1.2.0
 * @deprecated No
 */

abstract class App_Core_Form extends Zend_Form implements App_Core_Interface
{
    /**
     * Application config holder
     * @var Zend_Config
     */
    protected static $_cfg;
    /**
     * Application config holder
     * @var Zend_Config
     */
    protected $_applicationConfig;
    /**
     * Caching object holder
     * @var Zend_Cache
     */
    protected $_cacheManager;
    /**
     * Application logger
     * @var Zend_Log
     */
    protected static $_log;

    /**
     * We known magic quotes enabled problem
     * @var boolean
     */
    private $_applyFilterMagicGpc = false;

    /**
     * Current language string must be filled
     * @var string
     */
    protected $_lang;

    protected $_teritory;

    /**
     * Add filter magic quotes stripslashes to form element
     * @param Zend_Form_Element $element
     * @return void
     */
    protected function _filterMagicGpc(Zend_Form_Element $element)
    {
        $element->addFilter(new App_Abstract_Filter_Stripslashes($this->_applyFilterMagicGpc));
    }

    public function getLanguage()
    {
        return Zend_Registry::get(APPLICATION_REGISTRY_LOCALE)->getLanguage();
    }

    public function getTeritory()
    {
        return Zend_Registry::get(APPLICATION_REGISTRY_LOCALE)->getTeritory();
    }
    /**
     * Initialize form (used by extending classes)
     *
     * @return void
     */
    public function init()
    {
    	parent::init();
    	if(function_exists('get_magic_quotes_gpc') && 1 == get_magic_quotes_gpc())
        {
            $this->_applyFilterMagicGpc = true;
        }

    }

    /**
     * @return mixed|Zend_Config
     */
    public function getApplicationConfig ()
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
     * @return Zend_Cache_Manager
     */
    public function getCacheManager ()
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
     * @return bool true if application run in Debug mode
     */
    public function isDebug ()
    {
        if(DEBUG)
        {
            return true;
        }
        else return false;
    }
    /**
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
     * @param  $methodName : string
     * @param  $arguments : array
     * @return mixed|string
     */
    /*
    public function __call($methodName, $arguments)
    {
        if(preg_match('/prefix(.+)/i', $methodName, $matches) &&
           method_exists('App_Abstract_Abstract',$matches[1])
        )
        {
            return call_user_func_array('App_Abstract_Abstract::'.$matches[1], $arguments);
        }
        else  return parent::__call($methodName, $arguments);
    }
    *
    */

    /**
     * Add a new element
     *
     * $element may be either a string element type, or an object of type
     * Zend_Form_Element. If a string element type is provided, $name must be
     * provided, and $options may be optionally provided for configuring the
     * element.
     *
     * If a Zend_Form_Element is provided, $name may be optionally provided,
     * and any provided $options will be ignored.
     *
     * @param  string|Zend_Form_Element $element
     * @param  string $name
     * @param  array|Zend_Config $options
     * @return App_Abstract_Form
     */
    public function addElement($element, $name = null, $options = null)
    {
        parent::addElement($element, $name, $options);
        if(is_string($element))
        {
            $element = $this->getElement($name);
        }
        if ($element instanceof Zend_Form_Element)
        {
            if(true === $this->_applyFilterMagicGpc)
            {
                $this->_filterMagicGpc($element);
            }
        }
        return $this;
    }

    /**
     * Create an element
     *
     * Acts as a factory for creating elements. Elements created with this
     * method will not be attached to the form, but will contain element
     * settings as specified in the form object (including plugin loader
     * prefix paths, default decorators, etc.).
     *
     * @param  string $type
     * @param  string $name
     * @param  array|Zend_Config $options
     * @return Zend_Form_Element
     */
    public function createElement($type,$name,$option=null)
    {
        $element = parent::createElement($type,$name,$option);
        if(true === $this->_applyFilterMagicGpc)
        {
            $this->_filterMagicGpc($element);
        }
        return $element;
    }


    /**
     *
     */
    function __destruct ()
    {
        //TODO - Insert your code here
    }

    /**
     * Translate a string
     * @static
     * @param  $string
     * @return
     */
    public static function _($string)
    {
    	return self::getDefaultTranslator()->translate($string);
    }

	/**
     * Add form decorators to an individual sub form
     *
     * @param  Zend_Form_SubForm $subForm
     * @return My_Form_Registration
     */
    public function setSubFormDecorators($subFormName)
    {
		try
		{
			if(!($sf = $this->getSubForm($subFormName)) instanceof Zend_Form)
			{
				$sf = $this;
				throw new RuntimeException('Call not existing subform in '. __METHOD__. ':'.__LINE__);
			}
		}
		catch(RuntimeException $e)
		{
			$this->getLog()->notice($e);
		}
		$sf->setDecorators(array(
	            'FormElements',
	            array('HtmlTag', array('tag' => 'dl',
	                                   'class' => 'zend_form11')),
	            'Form',
	        ));
		$sf->removeDecorator('Form');

        return $this;
    }

    /**
     * Get list errors sub form as indexed array
     *
     * @return mixed
     */
    public function getErrorListDeep($translate=true)
    {   $error=$this->getErrorList();
        foreach ($this as $subForm)
        {
            if($subForm instanceof Zend_Form_SubForm)
            $error+=$this->getErrorList($subForm, $translate);
        }
        $error = array_merge($error, $this->getErrorMessages());
        return $error;
    }

    /**
     * @param  $form
     * @return mixed
     */
    public function getErrorList($form=null, $translate=true)
    {
        $errors = array();
        if($form === null)
        {
            $form = $this;
        }
        foreach ($form->getElements() as $elementName => $element)
        {
            /** @var $element Zend_Form_Element_Xhtml */
            if($element->hasErrors())
            {
                $ary =($translate)?$element->getMessages():$element->getErrors();
                $errors[$elementName] = array_shift($ary);
                if(is_array($errors[$elementName]))
                {
                    $errors[$elementName] = array_shift($errors[$elementName]);
                }

                $element->addDecorator('Label', array('tag'=>'dt', 'class' => 'error'))
                    ->setAttrib ('class',' error '.$element->getAttrib('class'))
                    ->removeDecorator('Errors')
                ;


            }
        }
        return $errors;
    }
}
