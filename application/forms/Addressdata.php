<?php

class App_Form_Addressdata extends App_Abstract_Form
{
	const NAME = 'address_form';
	const EL_STREET = 'street';
	const EL_STREET_NO = 'street_no';
	const EL_CITY = 'city';
	const EL_ZIP = 'zip';
	const EL_COUNTRY = 'country';
	
	const CACHE_ID_COUNTRYLIST = '__fff__fff__fff__contry_';
	
	/**
	 *
	 */
	public function init()
	{
		parent::init();
		
		$this->setName(self::NAME);
		
		$el[self::EL_STREET] = new Zend_Form_Element_Text(self::EL_STREET);
		$el[self::EL_STREET]->setAllowEmpty(false)
					 ->setRequired(true)
					 ->setErrorMessages(array($this->_('Street length must be between 1 and 255 characters')))
					 ->setLabel($this->_('Street:*'))
					 ;
		
		$el[self::EL_STREET_NO] = clone $el[self::EL_STREET];
		$el[self::EL_STREET_NO]->setName(self::EL_STREET_NO)
					   ->removeValidator('StringLength')
					   ->addValidator(new Zend_Validate_StringLength(array('min' =>1, 'max' => 10)))
					   ->setErrorMessages(array($this->_('Street Num. length must be between 1 and 255 characters')))
					   ->setLabel($this->_('Street Num.:*'))
					   ;
		
		$el[self::EL_CITY] = clone $el[self::EL_STREET];
		$el[self::EL_CITY]->setName(self::EL_CITY)
				   ->setErrorMessages(array($this->_('City length must be between 1 and 255 characters')))
				   ->setLabel($this->_('City:*'))
				   ;
		
		$el[self::EL_ZIP] = clone $el[self::EL_STREET];
		$el[self::EL_ZIP]->setName(self::EL_ZIP)
				  ->removeValidator('StringLength')
				  ->setErrorMessages(array($this->_('Zip is invalid')))
				  ->setLabel($this->_('Zip:*'))
				  ;

		$countries = $this->_getCountryList();
		$countriesE = $countries;
		array_unshift($countries, $this->_('-- Please choose --'));
		$el[self::EL_COUNTRY] = new Zend_Form_Element_Select(self::EL_COUNTRY);
		$el[self::EL_COUNTRY]->setMultiOptions($countries)
					  ->addValidator(new Zend_Validate_InArray(array_flip($countriesE)))
					  //->removeDecorator('Label')
					  ->setLabel($this->_('Country:*'))
					  //->removeDecorator('HtmlTag')
					  ->setValue(strtoupper($this->teritory))
					  ->setErrorMessages(array($this->_('Please choose a country')))
		;
		$this->addElements($el);
		$group = $this->addDisplayGroup(array_keys($el), self::NAME, array('legend'=>$this->_('Address form')));
		  	
	}
	
/**
	 * get the country list
	 * @return array
	 */
	protected function _getCountryList()
	{
	    $cacheid = self::CACHE_ID_COUNTRYLIST;
	    if(method_exists($this->getDefaultTranslator(), 'getlocale'))
	    {
    	    $cacheid .= $this->getDefaultTranslator()->getLocale();

	    }
	    elseif(method_exists($this->getDefaultTranslator(), 'getadapter') &&  method_exists($this->getDefaultTranslator()->getAdapter(), 'getlocale'))
	    {
	        $cacheid .= $this->getDefaultTranslator()->getAdapter()->getLocale();
	    }
	    
	    if($this->isCacheOn())
	    {
    	    if(NULL == ($c = $this->getCache()->load($cacheid)))
    	    {
    	        $c = $this->_getCountryListSorted();
                $this->getCache()->save($c, $cacheid, array($cacheid));
    	    }
	    }
	    else
	    {
	        $c = $this->_getCountryListSorted();
	    }
		return $c;
	}
	
	/**
	 *
	 */
	protected function _getCountryListSorted()
	{
        $c = Zend_Locale::getTranslationList('territory', $this->getDefaultTranslator()->getLocale(), 2);
//        foreach ($c as $k => $v)
//        {
//            if (!array_key_exists($k, Zend_Locale::getTranslationList('postaltoterritory')))
//            {
//                unset($c[$k]);
//            }
//        }
        asort($c);
        return $c;
	}
	
	/**
	 * get the post code format
	 * @param string 2-letter country code
	 */
	protected function _getPostCodeFormat($countryCode)
	{
		$countryCode = (string) $countryCode;
		$data = Zend_Locale::getTranslationList('postaltoterritory');
		if (array_key_exists($countryCode, $data)) {
			return $data[$countryCode];
		}
		return false;
	}
	
	/**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
	public function isValid($data)
	{
	//var_export($this->getElement(self::EL_ZIP)); die;
		if (
		    $this->getElement(self::EL_ZIP)
		    && isset($data[self::EL_COUNTRY])
			&& $data[self::EL_COUNTRY] != ''
		    && $this->getElement(self::EL_COUNTRY)->isValid($data[self::EL_COUNTRY])
		)
		{
			$this->getElement(self::EL_ZIP)->addValidator(
				new Zend_Validate_PostCode(array(
					'format' => $this->_getPostCodeFormat(
					$this->getElement(self::EL_COUNTRY)->getValue()
					)
				))
			);
		}
		return parent::isValid($data);
	}
	
/*TODO with full list countries
 *
 * protected function _getCountryList()
        {
                if ($this->_cache->test(self::BMW_COUNTRYLIST)) {
                        return $this->_cache->load(self::BMW_COUNTRYLIST);
                }
                $countrylist = Zend_Locale::getTranslationList('territory', null, 2);
//                foreach ($countrylist as $key => $value) {
//                        if (!array_key_exists($key, Zend_Locale::getTranslationList('postaltoterritory'))) {
//                                unset($countrylist[$key]);
//                        }
//                }
                asort($countrylist);
                $this->_cache->save($countrylist, self::BMW_COUNTRYLIST, array(self::BMW_COUNTRYLIST));
                return $countrylist;
        }





public function isValid($data)
        {
                if (!isset($data['reg_newsletter'])) {
                        $data['reg_newsletter'] = 0;
                }
                $this->getElement('reg_newsletter')->setValue($data['reg_newsletter']);
                if (!isset($data['reg_email']) || $data['reg_email'] == '') {
                        $data['reg_email'] = '****';
                }
                if (isset($data['reg_country'])
                        && $data['reg_country'] != ''
                    && $this->getElement('reg_country')->isValid($data['reg_country'])
                ) {
                        if (array_key_exists($this->getElement('reg_country')->getValue(), Zend_Locale::getTranslationList('postaltoterritory'))) {
                                $this->getElement('reg_zip')->addValidator(
                                        new Zend_Validate_PostCode(array(
                                                'format' => $this->_getPostCodeFormat(
                                                        $this->getElement('reg_country')->getValue()
                                                )
                                        ))
                                );
                        }
                }
                $valid = parent::isValid($data);
                return $valid;
        }
*/
}