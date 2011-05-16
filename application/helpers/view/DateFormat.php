<?php
/**
 * @package
 * @subpackage
 * @category
 * @name Zend_View_Helper
 * @author Oleksandr Khutoretskyy <olekhy@googlemail.com>
 * @author Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 * @version 0.0.1
 * @example
 * @copyright (c) 2009 <>
 * @encoding UTF-8
 */

/**
 * DateFormat helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class App_View_Helper_DateFormat
{
    /**
     * @var Zend_View_Interface
     */
    public $view;
    /**
     *
     */
    public function dateFormat ($date, $part = Zend_Date::DATE_FULL, $locale = NULL)
    {

        if($date instanceof Zend_Date){ return $date->get($part, $locale);}
        // ie 0000-00-00 00:00:00 or 0000/00/00_00.00.00
        //echo "ff".$date."DD"; die;
        if(preg_match('/0{4}[^0]0{2}[^0]0{2}[^0]0{2}[^0]0{2}[^0]0{2}/', $date) || !$date)
        {
            return NULL;
        }
        try
        {
            $zendDate = new Zend_Date();
            if(null === $locale)
            {
                if(!Zend_Registry::isRegistered(APPLICATION_REGISTRY_LOCALE) ||
                   !($locale = Zend_Registry::get(APPLICATION_REGISTRY_LOCALE)) instanceof Zend_Locale)
                {
                    throw new RuntimeException("Can not find Zend_Loacale in Zend_Registry.");
                }
                $zendDate->setLocale($locale);
            }
            else
            {
                $zendDate->setLocale($locale);
            }
            if(true !== $zendDate->isDate($date, 'YYYY.MM.dd'))
            {
                throw new InvalidArgumentException('Invalid date argument provided.');
            }
            $zendDate->set($date, 'en_GB');

            return $zendDate->get($part);
        }
        catch (Exception $e)
        {
            if(Zend_Registry::isRegistered(APPLICATION_REGISTRY_LOG))
            {
                Zend_Registry::get(APPLICATION_REGISTRY_LOG)->log($e, Zend_Log::WARN);
                return null;
            }
            else
            {
                error_log($e->getMessage().PHP_EOL.$e->getTraceAsString());
                echo $e->getMessage() . PHP_EOL . $e->getTraceAsString();
            }
        }
        return (string) $zendDate;
    }

    /**
     * Sets the view field
     * @param $view Zend_View_Interface
     */
    public function setView (Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
