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
            $cacheCore = $cacheManager->getCache('core');
            //$cacheFile = $cacheManager->getCache('file');

            $optionsCore = array('backend' => $cacheCore->getBackend());
            //$optionsFile = array('backend' => $cacheFile->getBackend());
            $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Cache($optionsCore));
            //$this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Cache($optionsFile));
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
        $this->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Database());
        //var_dump($options);
        //$this->setOptions($options);
    }
}

