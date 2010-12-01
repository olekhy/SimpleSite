<?php
/**
 * Abstract generic Calss Filter
 *
 * This source file is part of the Webfact Framework.
 *
 * @category   Filter
 * @package    App_Absract_Filter
 * @subpackage App_Absract_Filter_Stripslashes
 * @copyright  Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @author     Oleksandr Khutoretsky <olekhy@googlemail.com>
 * @author     Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 */


/**
 * Strip backslashes from string all was matched.
 * Or remove backslashes in special case when we 
 * could'n set off magic_quotes_gpc directive in php.ini 
 * 
 * @category   Filter
 * @package    App_Absract_Filter
 * @subpackage App_Absract_Filter_Stripslashes
 * @copyright  Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @author     Oleksandr Khutoretsky <olekhy@googlemail.com>
 * @author     Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 */
class App_Abstract_Filter_Stripslashes implements Zend_Filter_Interface
{
    /**
     * @var bool
     */
    private $_filterMagicGpc = false;
    
    /**
     * @param $filterMagicGpc
     * @return void
     */
    public function __construct($filterMagicGpc = false)
    {
        $this->_filterMagicGpc = (bool) $filterMagicGpc;
    }
    
    /**
     * apply the filter
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $v = (string) $value;
        if(true === $this->_filterMagicGpc)
        {
            return @preg_replace('!\\\((\')|(\")|(\\\)|(0)|(NULL))!i', "$1", $v);    
        }
        return stripslashes($v);
    }
}