<?php

class IndexController extends App_Core_Controller_Action
{

    
    public function init()
    {
        /* Initialize action controller here */
        //$this->setSecureActions(array('index'));
        $this->view->BASETAG = $this->serverNameWithProtocol();
        //$this->_helper->login();
    }
    /**
     * @return void
     */
    //public function indexAction()
    //{
        //print "<pre/>";
        //var_dump($_SESSION);
        //var_dump($this->getRequest()->getParams());
        //var_dump($this->_getAllParams());  die;
        
        //$log = $this->_invokeArgs['bootstrap']->getResource('Log');
        //$log->debug('hallo');

        //$cacheManager = $this->_invokeArgs['bootstrap']->getResource('CacheManager');
        /** @var $cacheManager Zend_Cache_Manager */


        //$cacheCore = $cacheManager->getCache('core');

        //if(!($cached = $cacheCore->load('test')))
        //{
        //    $cached = time();
        //    $cacheCore->save($cached, 'test');
        //}

        //var_dump($cached);

        //$cacheFile = $cacheManager->getCache('onfile');
        //$file = APPLICATION_PATH.'/../tmp/test.php';

        //if(method_exists($cacheFile, 'setMasterFiles'))
        //{
        //    $cacheFile->setMasterFiles(array($file));
        //}

        //if(!($cached1 = $cacheFile->load('test1')))
        //{
        //    $cached1 = include $file;
        //    $cacheFile->save($cached1, 'test1');
        //}

        //var_dump($cached1);

        //$cacheMemcached = $cacheManager->getCache('memcached');
        //var_export($cacheMemcached);
        //if(!($cached2 = $cacheMemcached->load('test2')))
        //{
        //    $cached2 = time();
        //    $cacheMemcached->save($cached2, 'test2');
        //}
        //var_dump($cached2);
        //Zend_Session::start();
        //$sess = new Zend_Session_Namespace();
        //if(!isset($sess->hallo)){
        //    $sess->hallo='session test';
        //}
        //var_export($sess->hallo);
        /** @var $translate Zend_Translate **/
        //$translate = $this->_invokeArgs['bootstrap']->getResource('Translate');
        //var_dump($translate->getCache());
        //var_dump($translate->getAdapter()->getLocale());
        //var_dump(get_class_methods($translate->getAdapter()));
        //$this->view->hallo = "HALLO";
    //}

    //public function localeAction()
    //{
    //    print "<pre/>";
    //    var_dump($this->_getAllParams());
    //}

    /**
     * @return void
     */
    public function indexAction()
    {
        
    }
}

