<?php

class App_Form_TermsAndPrivacy extends App_Abstract_Form 
{
	const NAME = 'terms_and_privacy_policy';
	const EL_TERMS = 'terms';
	const EL_PRIVACY_POLICY = 'privacy_policy';
	
	/**
	 * 
	 */
	public function init()
	{
		parent::init(); 
		
		$this->setName(self::NAME);
		
		$el[self::EL_TERMS] = new Zend_Form_Element_Checkbox(self::EL_TERMS);
		$el[self::EL_TERMS]->setAttrib('class', 'nostyle')
					->setAttrib('style', 'margin: 0;')
					->addValidator(new Zend_Validate_Regex('/^1$/'))
					->setLabel($this->_('Terms of use confirm'))
					->setErrorMessages(array($this->_('Terms must be accepted')))
					//->removeDecorator('HtmlTag')
					;
		//$validator->setMessage($this->_('Privacy policy must be accepted'));						
		$el[self::EL_PRIVACY_POLICY] = clone $el[self::EL_TERMS];
		$el[self::EL_PRIVACY_POLICY]->setName(self::EL_PRIVACY_POLICY)
					->setErrorMessages(array($this->_('Privacy policy be accepted')))
					->setLabel($this->_('Privacy policy confirm'))
		;					
		$this->addElements($el);	
		$group = $this->addDisplayGroup(array_keys($el),self::NAME, array('legend'=>$this->_('Terms and privacy policy confirm')));	  	
	}
}