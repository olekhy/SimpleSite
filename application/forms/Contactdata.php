<?php

class App_Form_Contactdata extends App_Abstract_Form
{
	const NAME = 'contact_data';
	const EL_TITLE = 'title';
	const EL_SALUTATION = 'salutation';
	const EL_LASTNAME = 'lastname';
	const EL_FIRSTNAME = 'firstname';
	const EL_EMAIL = 'email';
	const EL_TERMS = 'terms';
	const EL_SEND = 'send';

	//protected $_hideElemets = array(self::EL_EMPLOYMENT);
	/**
	 *
	 */
	public function init()
	{
		parent::init();
		$this->setName(self::NAME);

		$el[self::EL_SALUTATION] = new Zend_Form_Element_Select(self::EL_SALUTATION);
		$el[self::EL_SALUTATION]
		              ->setMultiOptions(array(''=>$this->_('Please choose'),CONST_MALE => $this->_('Herr'), CONST_FEMALE => $this->_('Frau')))
					  ->setRequired(true)
					  ->setAllowEmpty(false)
					  ->setLabel($this->_('Anrede').':*')
					  ->setErrorMessages(array(Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form_error: Please choose salutation')));

	    $el[self::EL_TITLE] = clone $el[ self::EL_SALUTATION ];
        $el[self::EL_TITLE]
                      ->setName(self::EL_TITLE)
                      ->setMultiOptions(array(''=>$this->_('Please choose'), 210=> $this->_('Dr.'), 410 => $this->_('Prof.'), 411=> $this->_('Prof. Dr.') ) )
                      ->setRequired(false)
                      ->setAllowEmpty(true)
                      ->setLabel($this->_('Title').':')
                      ->setErrorMessages(array(Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form_error: Please choose title')));

		$el[self::EL_LASTNAME] = new Zend_Form_Element_Text(self::EL_LASTNAME);
		$el[self::EL_LASTNAME]->setAllowEmpty(false)
					  ->setRequired(true)
					  ->addFilter(new Zend_Filter_StringTrim())
					  ->addFilter(new Zend_Filter_StripTags())
					  ->addFilter(new Zend_Filter_StripNewlines())
					  ->addValidator(new Zend_Validate_StringLength(array('min' => 1, 'max' => 255)))
					  ->setLabel($this->_('Name').':*')
					  ->setAttrib('maxlength', 255)
					  ->setErrorMessages(array($this->_('form_error: Lastname length must be between 1 and 255 characters')))
		;

		$el[self::EL_FIRSTNAME] = clone $el[self::EL_LASTNAME];
		$el[self::EL_FIRSTNAME]->setName(self::EL_FIRSTNAME)
					    ->setErrorMessages(array($this->_('form_error: Firstname length must be between 1 and 255 characters')))
					    ->setLabel($this->_('Firstname').':*')
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

		$el[self::EL_TERMS] = new Zend_Form_Element_Checkbox(self::EL_TERMS);
        $el[self::EL_TERMS]->setAttrib('class', ' nostyle ')
                    ->setAttrib('style', 'margin: 0;')
                    ->addValidator(new Zend_Validate_Regex('/^1$/'))
                    ->setLabel($this->_('Terms of use confirm'))
                    ->setErrorMessages(array($this->_('Terms must be accepted')))
        ;

        $el[self::EL_SEND] = new Zend_Form_Element_Button(self::EL_SEND);
		$el[self::EL_SEND]->setLabel($this->_('Registration complete'))
		->setAttrib('type', 'submit')
		->setAttrib('onclick','submitNewsletterForm(this);')
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

		$group = $this->addDisplayGroup(array_keys($el),'register_new_user',array('legend'=>$this->_('Register new user form')));
	}

    /**
     *
     * @param array $data
     */
//	public function isValid($data)
//	{
//	    if(!$this->getElement(self::EL_EMAIL)->isValid($data[self::EL_EMAIL]))
//	    {
//
//	        $this->getElement(self::EL_EMAIL)->setErrorMessages(array('invlid Emailaddress'=>'hallo'));
//	    }
//	    $rs = parent::isValid($data);
//	    return $rs;
//	}
}
