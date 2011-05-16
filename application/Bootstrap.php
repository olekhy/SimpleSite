<?php
/**
 * 
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    /**
     * @return void
     */
    protected function _initCoreLoader()
    {
        $loader = $this->getResourceLoader();
        $loader->addResourceType('Core','core','Core');
    }
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
            $cacheManager->setCache('core', $cacheManager->getCache('blackhole'));
        }

        if(!$cacheManager->hasCache('onfile')){
            $cacheManager->setCache('onfile', $cacheManager->getCache('blackhole'));
        }

        if(!$cacheManager->hasCache('memcached')){
            $cacheManager->setCache('memcached', $cacheManager->getCache('blackhole'));
        }
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
        /** @var $router Zend_Controller_Router_Rewrite */
        $router = $this->getPluginResource('Router')->init();
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
            /** @var $locale Zend_Locale */
            $locale = $this->getResource('Locale');
            if(!$localeString){
                $localeString = array_shift(array_flip($locale->getDefault()));
            } else $router->setParam('localeInUrl', true);
            $locale->setLocale($localeString); // also in Registry even
        }
        return $router;
    }
    /**
     *
     *
     * @return void
     */
    protected function _initSession()
    {
        $options = $this->getOption('session');
        if(empty($options)) return;
        if(array_key_exists('cookie_bind', $options)) {
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

        if($this->hasPluginResource('Session')) {
            $sessionResources = $this->getPluginResource('Session');
            $resourceOptions = $sessionResources->getOptions();
            $sessionResources->setOptions($options + $resourceOptions);
        } else {
            Zend_Session::setOptions($options);
        }
        if(!Zend_Session::isStarted() && $options['remember_me_seconds']) {
            Zend_Session::rememberMe($options['remember_me_seconds']);
        }
    }
    /**
     *
     * 
     * @return Zend_Log
     */
    protected function _initLog()
    {   
        if($this->hasPluginResource('Log'))
        {
            return $this->getPluginResource('Log')->init();
        }
        $option = $this->getOption('presetClass');
        if($option && array_key_exists('class', $option)) 
        {
            if(class_exists($option['class']) && method_exists($option['class'], 'log'))
            {
                return call_user_func("{$option['class']}::log");
            }
        }
        
        return new Zend_Log(new Zend_Log_Writer_Null());
    }
    /**
     *
     * 
     * @return Zend_Translate
     */
    protected function _initTranslate()
    {   
        if($this->hasPluginResource('translate'))
        {   
            $this->bootstrap('CacheManager');
            if($this->hasOption('domain')){          
                $this->bootstrap('FrontController');
                $router = $this->getResource('FrontController')->getRouter();
                if(!$router->getParam('localeInUrl')){ 
                    $this->bootstrap('Locale');
                    foreach($this->getOption('domain') as $localeStr => $domain){
                        if(array_shift($domain['http']) == $_SERVER['SERVER_NAME'] &&
                           Zend_Locale::isLocale($localeStr)){
                            $this->getResource('Locale')->setLocale($localeStr);
                            break;
                        }
                    }
                }
            }
            return $this->getPluginResource('translate')->init();
        }
    }
    /**
     *
     * 
     * @return stdClass
     */
    protected function _initdbManager()
    {
        $dbs = new stdClass();
        $dbs->masterdb = null;
        $dbs->slavedb = null;
        if($options = $this->getOption('dbmanager')) {
            if(array_key_exists('defaultMetadataCache', $options)) {
                $this->bootstrap('CacheManager');
                $cacheManager = $this->getResource('CacheManager');
                if($cacheManager instanceof Zend_Cache_Manager) {
                    if($cacheManager->hasCache($options['defaultMetadataCache'])) {
                        $cache = $cacheManager->getCache($options['defaultMetadataCache']);
                        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
                    }
                }
            }
            if(!Zend_Registry::isRegistered('masterdb') && array_key_exists('rw', $options)) {
                $masterOpt = $options['rw'][array_rand($options['rw'], 1)];
                $dbs->masterdb = Zend_Db::factory($masterOpt['adapter'], $masterOpt);
                Zend_Registry::set('masterdb', $dbs->masterdb);
            }
            if(!Zend_Registry::isRegistered('slavedb') && array_key_exists('r', $options)) {
                $slaveOpt = $options['r'][array_rand($options['r'], 1)];
                $dbs->slavedb = Zend_Db::factory($slaveOpt['adapter'], $slaveOpt);
                Zend_Registry::set('slavedb', $dbs->slavedb);
            }
        }
        return $dbs;
    }
}

