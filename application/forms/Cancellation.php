<?php

class App_Form_Cancellation extends App_Abstract_Form
{
	const NAME = 'cancellation';
	const EL_EMAIL = 'email';
	const EL_BOOKING_CODE = 'booking_code';
	const EL_SUBMIT = 'submit';
	
	/**
	 *
	 */
	public function init()
	{
		parent::init();
		$this->setName(self::NAME);

		$el[self::EL_EMAIL] = new Zend_Form_Element_Text(self::EL_EMAIL);
		$el[self::EL_EMAIL]->addFilter(new Zend_Filter_StringTrim())
					->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Zend_Filter_StripNewlines())
					->setAttrib('maxlength', 255)
					->setAllowEmpty(false)
					->setRequired(true)
					//->removeDecorator('HtmlTag')
					->removeDecorator('Errors')
					->removeDecorator('Label')
					->setLabel($this->_('Email:*'))//'Email*')
					//->addValidator($existsEmailVld)
					//->addValidator(new Zend_Validate_EmailAddress(), true)
		;
		
		$el[self::EL_BOOKING_CODE] = clone $el[self::EL_EMAIL];;
		$el[self::EL_BOOKING_CODE]->setName(self::EL_BOOKING_CODE)
					//->removeDecorator('HtmlTag')
					//->removeDecorator('Errors')
					//->removeDecorator('Label')
					->setLabel($this->_('Booking code:*'))
					->removeValidator('EmailAddress')
		
		;
		
		$el[self::EL_SUBMIT] = new Zend_Form_Element_Submit(self::EL_SUBMIT);
		$el[self::EL_SUBMIT]->setLabel($this->_('Next'));
		
		$this->addElements($el);
	}
	
	
	
}