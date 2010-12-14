<?php
/**
 * App - Front Controller Action abstract class
 *
 * This source file is part of the App - Motorcycle Booking Tool.
 *
 * @category   App
 * @package    App_Abstract
 * @subpackage App_Abstact_Controller
 * @copyright  Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @author     Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 * @author     Olekhy Khutor <olekhy@googlemail.com>
 */

/**
 * @see Zend_Controller_Action
 */

/**
 * @category   App
 * @package    App_Abstract
 * @subpackage App_Abstract_Controller
 * @copyright  Copyright (c) 2010 Webfact GmbH (http://www.webfact.de)
 * @author     Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 * @author     Olekhy Khutor <olekhy@googlemail.com>
 *
 * @property App_Model_Table_Team $team
 * @property App_Model_Table_User $user
 * @property App_Model_Table_Image $image
 * @property App_Model_Table_Message $message
 * @property App_Model_Table_Persist $persist
 * @property App_Model_Table_Persist2Image $persist2Image
 * @property App_Model_Table_Challenge $challenge
 *
 * @method sesServerName() string|null sesServerName([$val|null]) if arg nul then value will be unset else return value from session by key "ServerName"
 *
 */
abstract class App_Abstract_Controller_Action extends Zend_Controller_Action implements App_Core_Interface
{
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPER = 'super';

    const RESOURCE_PAGE = 'page';
    const RESOURCE_PAGE_ACTION = 'action';
    const RESOURCE_PAGE_CONTROLLER = 'controller';
    const RESOURCE_PAGE_MODULE = 'module';

    const PRIVILEGE_PAGE_GET = 'page-get';


    const RESOURCE_IMAGE = 'image';


    const RESOURCE_USER = 'user';

    const PRIVILEGE_USER_EDIT = 'user-edit';
    const PRIVILEGE_USER_VIEW = 'user-view';
    const PRIVILEGE_USER_CREATE = 'user-create';
    const PRIVILEGE_USER_DELETE = 'user-delete';
    const PRIVILEGE_USER_CHANGE_STATUS = 'user-change-status';

    const AUTH_FIELD_ID = 'uid';
    const AUTH_FIELD_NAME = 'uname';
    const AUTH_FIELD_PSWD = 'upswd';
    const AUTH_FIELD_ROLE = 'urole';


    const RESOURCE_TEAM = 'team';

    const PRIVILEGE_TEAM_EDIT = 'team-edit';
    const PRIVILEGE_TEAM_VIEW = 'team-view';
    const PRIVILEGE_TEAM_CREATE = 'team-create';
    const PRIVILEGE_TEAM_DELETE = 'team-delete';
    const PRIVILEGE_TEAM_CHANGE_STATUS = 'team-change-status';

    const PRIVILEGE_IMAGE_UPLOAD = 'image-upload';
    const PRIVILEGE_IMAGE_DELETE = 'image-delete';

    const RESOURCE_CHALLENGE = 'challenge';
    const PRIVILEGE_CHALLENGE_EDIT = 'challenge-edit';
    const PRIVILEGE_CHALLENGE_DELETE = 'challenge-delete';
    const PRIVILEGE_CHALLENGE_PUBLISH = 'challenge-publish';


    /**
     *
     * 
     * @var Zend_Session_Namespace
     */
    protected $_sess;

    /**
     *
     * 
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     *
     * 
     * @var array models or table classes
     */
    protected $_models = array();

    /**
     *
     *
     * @var array forms classes
     */
    protected $_forms = array();

    /**
     * Get model object if they class exists
     * 
     * @param  $name
     * @return array|mixed
     */
    public function __get($name)
    {
        $class = 'App_Model_Table_'.ucfirst($name);
        if(!class_exists($class))
        {
            return $this->{$name};
        }
        if(isset($this->_models[$name]) && ($this->_models[$name] instanceof $class))
        {
            return $this->_models[$name];
        }
        return $this->_models[$name] = new $class;
    }

    /**
     * Initialize object
     */
    public function init()
    {
        parent::init();

        $this->_sess = $this->getSession();
        $this->view->msg = $this->_helper->flashMessenger->getCurrentMessages();
        $this->_acl = new Zend_Acl;
        $this->_acl->addRole(new Zend_Acl_Role(self::ROLE_USER));
        $this->_acl->addRole(new Zend_Acl_Role(self::ROLE_ADMIN), self::ROLE_USER);
        $this->_acl->addRole(new Zend_Acl_Role(self::ROLE_SUPER), self::ROLE_ADMIN);



        $this->_acl->addResource(new Zend_Acl_Resource(self::RESOURCE_USER));
        $this->_acl->addResource(new Zend_Acl_Resource(self::RESOURCE_TEAM));
        $this->_acl->addResource(new Zend_Acl_Resource(self::RESOURCE_IMAGE));
        $this->_acl->addResource(new Zend_Acl_Resource(self::RESOURCE_CHALLENGE));

        $this->_acl->addResource(new Zend_Acl_Resource(self::RESOURCE_PAGE));
        $this->_acl->addResource(new Zend_Acl_Resource(self::RESOURCE_PAGE_ACTION), self::RESOURCE_PAGE);
        $this->_acl->addResource(new Zend_Acl_Resource(self::RESOURCE_PAGE_CONTROLLER), self::RESOURCE_PAGE);
        $this->_acl->addResource(new Zend_Acl_Resource(self::RESOURCE_PAGE_MODULE), self::RESOURCE_PAGE);


        //$acl->addResource(new Zend_Acl_Resource('create'));
        //$acl->addResource(new Zend_Acl_Resource('update'));
        //$acl->addResource(new Zend_Acl_Resource('delete'));
        /*
        $acl->addResource(new Zend_Acl_Resource('read-page'),  'page');
        $acl->addResource(new Zend_Acl_Resource('create-page'),'page');
        $acl->addResource(new Zend_Acl_Resource('update-page'),'page');
        $acl->addResource(new Zend_Acl_Resource('delete-page'),'page');

        $acl->addResource(new Zend_Acl_Resource('read-image'),  'image');
        $acl->addResource(new Zend_Acl_Resource('create-image'),'image');
        $acl->addResource(new Zend_Acl_Resource('update-image'),'image');
        $acl->addResource(new Zend_Acl_Resource('delete-image'),'image');

        //$acl->
        */


        $this->_acl->allow(
            array(self::ROLE_ADMIN, self::ROLE_SUPER),
            array(self::RESOURCE_PAGE_ACTION),
            array(self::PRIVILEGE_PAGE_GET)
        );

        $this->_acl->allow(
            array(self::ROLE_ADMIN, self::ROLE_SUPER),
            array(self::RESOURCE_USER),
            array(self::PRIVILEGE_USER_VIEW)
        );

        $this->_acl->allow(
            array(self::ROLE_SUPER),
            array(self::RESOURCE_USER),
            array(
                self::PRIVILEGE_USER_CREATE,
                self::PRIVILEGE_USER_DELETE,
                self::PRIVILEGE_USER_EDIT,
                self::PRIVILEGE_USER_CHANGE_STATUS
            )
        );

        $this->_acl->allow(
            array(self::ROLE_ADMIN, self::ROLE_SUPER),
            array(self::RESOURCE_TEAM),
            array(
                self::PRIVILEGE_TEAM_VIEW,
                self::PRIVILEGE_TEAM_CREATE,
            )
        );

        $this->_acl->allow(
            array(self::ROLE_SUPER),
            array(self::RESOURCE_TEAM),
            array(
                self::PRIVILEGE_TEAM_DELETE,
                self::PRIVILEGE_TEAM_EDIT,
                self::PRIVILEGE_TEAM_CHANGE_STATUS
            )
        );

        $this->_acl->allow(
            array(self::ROLE_ADMIN, self::ROLE_SUPER),
            array(self::RESOURCE_IMAGE),
            array(
                self::PRIVILEGE_IMAGE_DELETE,
                self::PRIVILEGE_IMAGE_UPLOAD
            )
        );

        $this->_acl->allow(
            array(self::ROLE_SUPER),
            array(self::RESOURCE_CHALLENGE),
            array(
                self::PRIVILEGE_CHALLENGE_DELETE,
                self::PRIVILEGE_CHALLENGE_EDIT,
                self::PRIVILEGE_CHALLENGE_PUBLISH
            )
        );

    }

    /**
     * @param array $actionsInCurrentControllerToHttps
     * @return string
     */
    public function serverUrl(array $actionsInCurrentControllerToHttps = array())
    {
        $serverurl = 'http://' . trim($this->getRequest()->SERVER_NAME, '/');
        if(!$this->sesServerName()) {
            $this->sesServerName($serverurl);
        }
        $cfg = self::getGlogalConfig();
        if(isset($cfg->handle->locale) && APPLICATION_ENV == APPLICATION_MODE_PRODUCTION)
        {
            if(in_array($this->getRequest()->getActionName(), $actionsInCurrentControllerToHttps))
            {
                $serverurl = 'https://' . rtrim($this->_cfg->domains->ssl->{$this->getLanguage()}, '/');
            }
            else
            {
                if ($this->sesServerName() && $this->getRequest()->HTTPS == 'on')
                {
                    $url = $this->sesServerName().'/'.
                           $this->getRequest()->getControllerName() .'/'.
                           $this->getRequest()->getActionName().
                           (($query = http_build_query($_GET))? "?{$query}" : '');
                    $this->_helper->redirector->gotoUrl($url);
                }
            }
        }
        return $serverurl;
    }

    /**
     *
     * @param string $m called not exists method
     * @param string $a method args
     */
    public function __call($m,$a)
    {
        ((self::isDebug())?self::getLog()->log('Call magicaly: method:'.$m. '() in '. __METHOD__.':'.__LINE__, Zend_Log::DEBUG):'');

        if(preg_match('/^(ses)(?P<name>(.+))/i', $m, $matches)) {
            $key = $matches['name'];
            if(count($a)) {
                $this->_sess->$key = $a[0];
                return $this;
            } else {
                return (isset($this->_sess->$key))?$this->_sess->$key:null;
            }
        } else parent::__call($m, $a);
    }

    /**
     *
     * 
     * @static
     * @return Zend_Log
     */
    public static function getLog()
    {
        static $l;
        if($l == null){
            $l = App_Core_Abstract::getLog(self::getResource('Log'));
        }
        return $l;
    }

    /**
     *
     * 
     * @static
     * @param  $name
     * @return 
     */
    public static function getResource($name)
    {
        $fc = Zend_Controller_Front::getInstance();
        $bs = $fc->getParam('bootstrap');
        return $bs->getResource($name);
    }
    
    /**
     * Is debugging on then logging leves "debug" and "info" are working normaly
     * else calls debug or info methods are pass
     *
     *  @return boolean
     */
    public static function isDebug()
    {
        return App_Core_Abstract::isDebug();
    }

    /**
     *
     * 
     * @return Zend_Cache_Manager
     */
    public function getCacheManager()
    {
        return self::getResource('CacheManager');    
    }

    /**
     *  Get current website language as string
     * 
     * @return string
     */
    public function getLanguage()
    {
        return self::getResource('Locale')->getLanguage();
    }


    /**
     *
     * 
     * @param string $string
     * @return string translated in language for current locale
     */
    public function _($string)
    {
        return self::getResource('Translator')->translate($string);
    }

    /**
     * Applications wide config
     * 
     * @throws RuntimeException
     * @return mixed|Zend_Config
     */
    public static function getGlogalConfig()
    {
        return App_Core_Abstract::getCfg();
    }

    /**
     *
     * 
     * @return void
     */
    public function preDispatch()
    {
        $this->getResponse()->setRawHeader('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
        $this->getResponse()->setRawHeader('Cache-Control: no-cache, must-revalidate');
        $this->getResponse()->setRawHeader('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }
}
