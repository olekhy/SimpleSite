<?php
/**
 * Webfact Framework
 *
 * This source file is part of the Webfact Framework.
 *
 * @category   Webfact
 * @package    Webfact_Validate
 * @subpackage Webfact_Validate_PasswordRepeat
 * @copyright  Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @author     Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 */


/**
 * @category   Webfact
 * @package    Webfact_Validate
 * @subpackage Webfact_Validate_PasswordRepeat 
 * @copyright  Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @author     Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 */
class App_Form_Validate_EqFields extends Zend_Validate_Abstract
{
    const NOT_EQUAL = 'not_equal';

    private $_field1 = NULL;
    private $_field2 = NULL;

    protected $_messageTemplates = array(
        self::NOT_EQUAL => 'Fields not equal.'
    );

    public function __construct(Zend_Form_Element $field1, Zend_Form_Element $field2)
    {
        $this->_field1 = $field1;
        $this->_field2 = $field2;
    }

    public function isValid($value, $context = null)
    {
        if ((string) $this->_field1->getValue() == (string) $this->_field2->getValue()) {
            return true;
        }
        $this->_error(self::NOT_EQUAL);
        return false;
    }
}