<?php
/**
 * 
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Initiate cache manager resource
     * 
     * @return mixed|Zend_Cache_Manager
     */
    protected function _initCacheManager()
    {
        $this->bootstrap('log');
        $log = $this->getResource('log');
        $cacheManager = $this->getPluginResource('CacheManager')->init();
        
        /** @var $cacheManager Zend_Cache_Manager */
        if(!$cacheManager->hasCache('core')){
            $log->info('Core Cache is not available, we using Cache Black hole instead.');
            $cacheManager->setCache('core', $cacheManager->getCache('blackhole'));
        } $log->debug('Cache core: '.(int)CACHING);

        if(!$cacheManager->hasCache('onfile')){
            $log->info('Onfile Cache is not available, we using Cache Black hole instead.');
            $cacheManager->setCache('onfile', $cacheManager->getCache('blackhole'));
        } $log->debug('Cache onfile: '.(int)CACHING);
        
        if(!$cacheManager->hasCache('memcached')){
            $log->info('Memcached Cache is not available, we using Cache Black hole instead.');
            $cacheManager->setCache('memcached', $cacheManager->getCache('blackhole'));
        } $log->debug('Cache memcached: '.(int)CACHING);
        $cacheCore = $cacheManager->getCache('core');
        //Zend_Locale::setCache($cacheManager->getCache('core'));
        // calling Zend_Date::setOptions(array('cache'=>$cache)) set also cache in the Zend_Locale object 
        //Zend_Db_Table_Abstract::setDefaultMetadataCache( $cacheCore);
        Zend_Date::setOptions(array('cache'=>$cacheCore));
        Zend_Paginator::setCache($cacheCore);
        Zend_Registry::set('Zend_Cache_Manager', $cacheManager);
        return $cacheManager;
    }
    
    /**
     *
     * 
     * @return mixed|Zend_Controller_Router_Rewrite
     */
    protected function _initRouter()
    {
        if(!$this->hasPluginResource('Router')) return;
        $router = $this->getPluginResource('Router')->init();
        /** @var $router Zend_Controller_Router_Rewrite */
        $request = new Zend_Controller_Request_Http();
        $router->route($request);
        $localeString = $request->getParam('lang').'_'.
                        strtoupper($request->getParam('land'));

        if(!Zend_Locale::isLocale($localeString, true)){
            $localeString = null;
            if(stristr($router->getCurrentRouteName(),'locale')){
                $router->removeRoute($router->getCurrentRouteName());
            }
        }

        if($this->hasPluginResource('Locale')){
            $this->bootstrap('Locale');
        }
        if($this->hasResource('Locale')){
            $locale = $this->getResource('Locale');
            /** @var $locale Zend_Locale */
            if(!$localeString){
                $localeString = array_shift(array_flip($locale->getDefault()));
            }
            $locale->setLocale($localeString); // also in Registry even
        }

        return $router;
    }

    /**
     * 
     * @return void
     */
    protected function _initSession()
    {
        $options = $this->getOption('session');
        if(empty($options)) return;
        if(array_key_exists('cookie_bind', $options)){
            $m = array();
            $search = "/^(?P<subdomain>([^\.]+))\.(?P<domain>([^\.]+))\.(?P<tld>([^\.]{2,6}))/i";
            if(preg_match($search,$_SERVER['SERVER_NAME'], $m)) {
                if('domain' == $options['cookie_bind']){
                    $options['cookie_domain'] = ".{$m['domain']}.{$m['tld']}";
                } elseif('subdomain' == $options['cookie_bind']){
                    $options['cookie_domain'] = ".{$m['subdomain']}.{$m['domain']}.{$m['tld']}";
                }
            }
            unset($options['cookie_bind']);
        }

        if($this->hasPluginResource('Session')){
            $sessionResources = $this->getPluginResource('Session');
            $resourceOptions = $sessionResources->getOptions();
            $sessionResources->setOptions($options + $resourceOptions);
        } else {
            Zend_Session::setOptions($options);
        }
        if(!Zend_Session::isStarted() && $options['remember_me_seconds']){
            Zend_Session::rememberMe($options['remember_me_seconds']);
        }
    }

    /**
     * 
     * @return Zend_Log
     */
    protected function _initLog()
    {   
        if($this->hasPluginResource('Log')){
            return $this->getPluginResource('Log')->init();
        }
        if(class_exists('Configure')){
            return Configure::log();    
        }
    }

    protected function _initTranslate()
    {
        if($this->hasPluginResource('translate')){
            $this->bootstrap('CacheManager');
            return $this->getPluginResource('translate')->init();
        }
    }
    
}

