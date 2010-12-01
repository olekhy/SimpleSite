<?php

class App_Plugin_ProfilerFirePHP extends Zend_Controller_Plugin_Abstract
{
	private $_adapter;
	/**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
    	$this->_adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
		$profiler->setEnabled(true);
		$this->_adapter->setProfiler($profiler);
    }
}

