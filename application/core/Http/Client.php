<?php
/**
 * @author al
 *
 *
 */
abstract class App_Abstract_Http_Client extends Zend_Http_Client implements App_Lib_Interface
{

    /**
     *
     * @var Zend_Log
     */
    protected $_log = null;


    /**
     * @return Zend_Log
     */
    public function getLog()
    {
        if($this->_log instanceof Zend_Log)
        {
            return $this->_log;
        }
        else if(!defined('APPLICATION_REGISTRY_LOG') ||
                !Zend_Registry::isRegistered(APPLICATION_REGISTRY_LOG) ||
                !(($this->_log = Zend_Registry::get(APPLICATION_REGISTRY_LOG)) instanceof Zend_Log))
        {
            throw new RuntimeException('Zend_Log is not registered in ZendRegistry.');
        }
        else
        {
            return $this->_log;
        }
    }



}
