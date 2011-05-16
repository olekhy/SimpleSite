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
class App_Form_Validate_DbRecordExistsCrypted extends Zend_Validate_Db_RecordExists
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
		$valid = true;
		$valueI = (string) $value;
		$valueI = call_user_func($this->_cryptMethod, $valueI);
		$this->_setValue($valueI);

        $this->_result = $this->_query($valueI);
        if (!$this->_result) {
            $valid = false;
            $this->_error(self::ERROR_NO_RECORD_FOUND);
        }
        return $valid;
	}
	
	/**
	 *
	 */
	public function getResult()
	{
		return $this->_result;
	}
	
	protected function _query($value)
    {
        /**
         * Check for an adapter being defined. if not, fetch the default adapter.
         */
        if ($this->_adapter === null) {
            $this->_adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
            if (null === $this->_adapter) {
                // require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception('No database adapter present');
            }
        }

        /**
         * Build select object
         */
        $select = new Zend_Db_Select($this->_adapter);
        $select->from($this->_table, array($this->_field), $this->_schema)
               ->where($this->_adapter->quoteIdentifier($this->_field).' = ?', $value)
               ->columns('*');
        if ($this->_exclude !== null) {
            if (is_array($this->_exclude)) {
                $select->where($this->_adapter->quoteIdentifier($this->_exclude['field']).' != ?', $this->_exclude['value']);
            } else {
                $select->where($this->_exclude);
            }
        }
        $select->limit(1);

        /**
         * Run query
         */
        $result = $this->_adapter->fetchRow($select, array(), Zend_Db::FETCH_OBJ);

        return $result;
    }
}