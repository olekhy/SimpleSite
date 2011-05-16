<?php

class App_Model_Table_Row extends Zend_Db_Table_Row_Abstract
{
    /**
     * @var bool
     */
    private $isDebug = false;
    /**
     * @var Zend_Log
     */
    private $logger;
    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->isDebug = App::isDebug();
        $this->logger = App::log();
        parent::__construct($config);
    }
    /**
     * @return void
     */
    protected function _insert()
    {
        if($this->isDebug)
        {
            $this->logger->debug(var_export(array('data'=>$this->_data,'insert'=>$this->_tableClass),1));
        }
    }

    /**
     * @return void
     */
    protected function _update()
    {
        if($this->isDebug)
        {
            $this->logger->debug(var_export(array('data'=>$this->_data,'update'=>$this->_tableClass),1));
        }
    }
}
