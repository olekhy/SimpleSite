<?php

class App_Form_SimpleUser extends App_Abstract_Form
{
	const NAME = 'simple_user';
	const EL_NAME = 'uname';
	const EL_PSWD = 'upswd';
	const EL_ROLE= 'urole';
	const EL_EMAIL = 'uemail';
	const EL_SEND = 'send';

    protected $_hideElemets = array(self::EL_EMAIL);
	/**
	 *
	 */
	public function init()
	{
        parent::init();
        $this->setName(self::NAME);
        $this->setMethod('post');

        $el[self::EL_ROLE] = new Zend_Form_Element_Select(self::EL_ROLE);
        $el[self::EL_ROLE]
                ->setMultiOptions(
                    array(''=>$this->_('form.please.choose'),
                          'user' => $this->_('admin.user.role.user'),
                          'admin' => $this->_('admin.user.role.admin'),
                          'super' => $this->_('admin.user.role.super'))
                )
                ->setRequired(true)
                ->setAllowEmpty(false)
                ->setLabel($this->_('admin.user.role').':*')
                ->setErrorMessages(
                    array(
                        Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.role'))
        )
        ;

        $el[self::EL_NAME] = new Zend_Form_Element_Text(self::EL_NAME);;
        $el[self::EL_NAME]
                ->setRequired(true)
                ->setAllowEmpty(false)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StripNewlines())
                ->addValidator(new Zend_Validate_StringLength(array('min' => 5, 'max' => 21)))
                ->setAttrib('maxlength', 21)
                ->setLabel($this->_('form.create.user.username').':*')
                //->setErrorMessages(array($this->_('admin.form.error.please.choose.username')))
        ;

        $el[self::EL_PSWD] = new Zend_Form_Element_Password(self::EL_PSWD);
        $el[self::EL_PSWD]
                ->setRequired(true)
                ->setAllowEmpty(false)
                ->addValidator(new Zend_Validate_StringLength(array('min' => 5, 'max' => 21)))
                ->setLabel($this->_('form.create.user.password').':*')
                ->setAttrib('maxlength', 21)
                //->setErrorMessages(array($this->_('admin.form.error.must.be.between.5.and.8.characters')))
        ;


        $emailValidator = new Zend_Validate_EmailAddress();
        $el[self::EL_EMAIL] = new Zend_Form_Element_Text(self::EL_EMAIL);
        $el[self::EL_EMAIL]->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StripNewlines())
                ->setAttrib('maxlength', 255)
                ->setAllowEmpty(false)
                ->setRequired(true)
                ->setLabel($this->_('Email:*'))//'Email*')
                ->addValidator($emailValidator, false)
        ;

        $el[self::EL_SEND] = new Zend_Form_Element_Button(self::EL_SEND);
        $el[self::EL_SEND]->setLabel($this->_('form.create.user.submit'))
                ->setAttrib('type', 'submit')

        ;


        $this->addElements($el);

        if(is_array($this->_hideElemets))
        {
            foreach ($this->_hideElemets as $elToHide)
            {
                if($this->getElement($elToHide) instanceof Zend_Form_Element)
                {
                    $this->removeElement($elToHide);
                    $this->addElement(new Zend_Form_Element_Hidden($elToHide));
                    $this->getElement($elToHide)->setAttrib('disabled','disabled');
                }
            }
        }

        $group = $this->addDisplayGroup(array_keys($el),'gr_'.self::NAME,array('legend'=>$this->_('admin.create.new.user.form')));
	}
}
