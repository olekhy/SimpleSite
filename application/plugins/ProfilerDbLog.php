<?php

class App_Plugin_ProfilerDbLog extends Zend_Controller_Plugin_Abstract
{
	private $_adapter;
	/**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    /*
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
    	$this->_adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
		$profiler->setEnabled(true);

		$this->_adapter->setProfiler($profiler);

        //$db = Zend_Registry::get(DB_MASTER);
        //$db = Zend_Registry::get(DB_SLAVE);
        var_dump($this->_adapter->getProfiler());
    }
     *
     */
    public function dispatchLoopShutdown()
    {
        parent::dispatchLoopShutdown();
        $dbm = Zend_Registry::get(DB_MASTER);
        $dbs = Zend_Registry::get(DB_SLAVE);

        $log = new Zend_Log(new Zend_Log_Writer_Stream(LOG_DIR.DIRECTORY_SEPARATOR.'db.log'));
        $log->info('Master DB: '.var_export($dbm->getProfiler()->getQueryProfiles(null, true), 1));
        $log->info('Slave DB: '.var_export($dbs->getProfiler()->getQueryProfiles(null, true), 1));
    }

}

