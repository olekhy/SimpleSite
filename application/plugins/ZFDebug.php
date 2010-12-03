<?php

class App_Plugin_ZFDebug extends ZFDebug_Controller_Plugin_Debug
{
    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $frontCtrl = Zend_Controller_Front::getInstance();

        $bs = $frontCtrl->getParam('bootstrap');

        if($bs && $bs->hasPluginResource('CacheManager')){
            $cacheManager = $bs->getResource('CacheManager');

            $options = array();
            if($cacheManager->hasCache('core')){
                $options['backend']['core'] = $cacheManager->getCache('core')->getBackend();
            }
            if($cacheManager->hasCache('onfile')){
                $options['backend']['onfile'] = $cacheManager->getCache('onfile')->getBackend();
            }
            if($cacheManager->hasCache('memcached')){
                $options['backend']['memcached'] = $cacheManager->getCache('memcached')->getBackend();
            }
            //if($cacheManager->hasCache('file')){
                //$options['backend']['file'] = $cacheManager->getCache('file')->getBackend();
            //}
            if(!empty($options)){     
                $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Cache($options));
            }
        }
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Variables());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Auth());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Exception());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_File());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Html());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Registry());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Variables());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Text());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Memory());
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Time());

        if($dbmanager = $bs->getResource('dbmanager')){
            $dbOptions['adapter']['masterdb'] = $dbmanager->masterdb;
            $dbOptions['adapter']['slavedb'] = $dbmanager->slavedb;
            $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Database($dbOptions));
        }elseif($bs->hasResource('db')){
            $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Database());    
        }
    }
}

