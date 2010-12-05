<?php

class App_Plugin_ProfilerFirePHP extends Zend_Controller_Plugin_Abstract
{
    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
    */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);

        $fc = Zend_Controller_Front::getInstance();
        $bs = $fc->getParam('bootstrap');
        if($bs->hasResource('dbmanager')){
            $dbmanager = $bs->getResource('dbmanager');
            $profilerMaster = new Zend_Db_Profiler_Firebug('Master DB: all queries');
            $profilerMaster->setEnabled(true);
            $dbmanager->masterdb->setProfiler($profilerMaster);
            $profilerSlave = new Zend_Db_Profiler_Firebug('Slave DB: all queries');
            $profilerSlave->setEnabled(true);
            $dbmanager->slavedb->setProfiler($profilerSlave);
        } else {
            Zend_Db_Table_Abstract::getDefaultAdapter()->setProfiler($profiler);
        }
        //$rs = $dbmanager->slavedb->query('SELECT uuid() as SLAVE');
        //$rs = $dbmanager->masterdb->query('SELECT uuid() as MASTER');
    }
}

