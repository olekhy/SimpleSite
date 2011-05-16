<?php
/**
 *
 * This source file is part of the Webfact Framework.
 *
 * @category   App_Form
 * @package    App_FORM_Validate
 * @subpackage App_Form_Validate_DbRecordExistsCrypted
 * @copyright  Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @author     Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 */


/**
 * @category   App
 * @package    App_Form_Validate
 * @subpackage App_Form_Validate_DbRecordExistsCrypted
 * @copyright  Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @author     Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 */
class App_Form_Validate_DbNoRecordExistsCrypted extends Zend_Validate_Db_NoRecordExists
{
	private $_cryptMethod;
    
	public function __construct($options = null)
	{
		parent::__construct($options);
		if(array_key_exists('crypt_method', $options))
		{
			$this->setCryptMethod($options['crypt_method']);
		}
		else 
		{
			$this->setCryptMethod();
		}
	} 
	
	/**
	 * 
	 * @param unknown_type $t
	 */
	public function setCryptMethod($t = 'md5')
	{
		if(function_exists($t))
		{
			$this->_cryptMethod = $t;
		}
		else 
		{
			throw new Zend_Validate_Exception('Provide not existing crypt callback function "'.$t.'"');
		}
	}
	
	/**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  string $value
     * @return boolean
     * @throws Zend_Valid_Exception If validation of $value is impossible
     */
	public function isValid($value)
	{
		$valueI = (string) $value;
		$valueI = call_user_func($this->_cryptMethod, $valueI);
		$this->_setValue($valueI);
		return parent::isValid($valueI);
	}
}