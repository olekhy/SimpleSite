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
 */
abstract class App_Abstract_Controller_Action extends Zend_Controller_Action implements App_Abstract_Interface
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
     * @var Zend_Session_Namespace
     */
    protected $_sess;
    /**
     * @var Zend_Log
     */
    protected $_log;
    /**
     * @var Zend_Config
     */
    protected $_cfg;
    /**
     * @var Zend_Cache_Manager
     */
    protected $_cacheManager;
    /**
     * @var Object
     */
    protected $_authUser;
    /**
     * @var string
     */
    protected $_lang;
    /**
     * @var string
     */
    protected $_land;
    /**
     * @var string
     */
    protected $_localeString;

    /**
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * @var array models or table classes
     */
    protected $_models = array();

    /**
     * @var array forms classes
     */
    protected $_forms = array();

    /**
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

        $this->_cfg = $this->getApplicationConfig();
        $this->_sess = $this->getSession();
        $this->view->msg = $this->_helper->flashMessenger->getCurrentMessages();
        $this->view->lastAction = $this->sesLastAction();
        $this->view->lang = $this->getLanguage();
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
        if(!$this->sesServerName())
        {
            $this->sesServerName($serverurl);
        }
        if( isset($this->_cfg->handle->locale) && APPLICATION_ENV == APPLICATION_MODE_PRODUCTION)
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
     * @return mixed
     */
    public function getAuthUserRole()
    {
        if(($storage = Zend_Auth::getInstance()->getStorage()->read()) instanceof stdClass )
        {
            if(isset($storage->{self::AUTH_FIELD_ROLE}) &&
               in_array($storage->{self::AUTH_FIELD_ROLE},
                        array(self::ROLE_ADMIN,
                              self::ROLE_SUPER,
                              self::ROLE_USER))
            )
            {
                return $storage->{self::AUTH_FIELD_ROLE};
            }
        }
        else
        {
            $storage = new stdClass;
        }
        $storage->{self::AUTH_FIELD_ROLE} = self::ROLE_USER;
        Zend_Auth::getInstance()->getStorage()->write($storage);
        return $storage->{self::AUTH_FIELD_ROLE};
    }

    /**
     * Return user object if user was autenticated or null
     * @return mixed null|stdClass
     */
    public function getAuthUser()
    {
        if($this->_authUser !== null)
        {
            return $this->_authUser;
        }
        $this->_authUser = Zend_Auth::getInstance()->getStorage()->read();
        return $this->_authUser;
    }

    /**
     * @return int
     */
    public function getAuthUserIdentity()
    {
        if(!$this->_sess->userId)
        {
            return null;
        }
        return $this->_sess->userId;
    }

    /**
     * Store an authenticated user in property as an object
     * @param stdClass $userObject
     */
    public function setAuthUser($userObject, $identField)
    {
        $this->_authUser = $userObject;
        $this->_sess->userId = $userObject->{$identField};
        return $this;
    }

    public function delAuthUser()
    {
        $this->_authUser = null;
        $this->_sess->userId = null;
        Zend_Auth::getInstance()->getStorage()->clear();
        return $this;
    }

    /**
     *
     * @param string $m called not exists method
     * @param string $a method args
     */
    public function __call($m,$a)
    {
        (($this->isDebug())?$this->getLog()->log('Call magicaly: method:'.$m. '() in '. __METHOD__.':'.__LINE__, Zend_Log::DEBUG):'');

        if(preg_match('/^(ses)(?P<name>(.+))/i', $m, $matches))
        {
            $key = $matches['name'];
            $cls = get_class($this);
            $const = "$cls::SESKEY_".strtoupper((string)$key);
            if(defined($const))
            {
                $key = @constant($const);
            }
            if(count($a))
            {
                $this->_sess->$key = $a[0];
                return $this;
            }
            else
            {
                return (isset($this->_sess->$key))?$this->_sess->$key:null;
            }
        }
        else parent::__call($m, $a);
    }

    /**
     * @return Zend_Log
     */
    public function getLog()
    {
        if($this->_log === null)
        {
            if( ! ($this->_log = $this->getInvokeArg('bootstrap')->getResource('Log')) instanceof Zend_Log)
            {
                if( ! ($this->_log = App_Abstract_Abstract::getLog() ) instanceof Zend_Log )
                {
                    throw new RuntimeException('Logger is not implemented.');
                }
            }
        }
        return $this->_log;
    }

    /**
     * Is debugging on then logging leves "debug" and "info" are working normaly
     * else calls debug or info methods are pass
     *
     *  @return boolean
     */
    public function isDebug()
    {
        return (bool) defined('DEBUG') and DEBUG or false;
    }

    /**
     * @return Zend_Session_Namespace
     */
    public function getSession()
    {
        if($this->_sess === null)
        {
            if( ! ($this->_sess = $this->getInvokeArg('bootstrap')->getResource('Session')) instanceof Zend_Session_Namespace)
            {
                throw new RuntimeException('Session can not be handled, Session object not available.');
            }
        }
        return $this->_sess;
    }

    /**
     * @return Zend_Cache_Manager
     */
    public function getCacheManager()
    {
        if($this->_cacheManager === null)
        {
            if( ! ($this->_cacheManager = $this->getInvokeArg('bootstrap')->getResource('Cache')) instanceof Zend_Cache_Manager)
            {
                throw new RuntimeException('Cache not currently implemented');
            }
        }
        return $this->_cacheManager;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        if(!$this->_lang)
        {
            $this->_lang = $this->getInvokeArg('bootstrap')->getResource('Locale')->getLanguage();
        }
        return $this->_lang;
    }

    /**
     * @return string
     */
    public function getLand()
    {
        if(!$this->_land)
        {
            $this->_land = $this->getInvokeArg('bootstrap')->getResource('Locale')->getRegion();
        }
        return $this->_land;
    }

    /**
     * @return string
     */
    public function getLocaleAsString()
    {
        if(!$this->_localeString)
        {
            $this->_localeString = $this->getInvokeArg('bootstrap')->getResource('Locale')->__toString();
        }
        return $this->_localeString;
    }

    /**
     * @param string $string
     * @return string translated in language for current locale
     */
    public function _($string)
    {
        return $this->getInvokeArg('bootstrap')->getResource('Translator')->translate($string);
    }

    /**
     * Applications wide configs
     * @throws RuntimeException
     * @return mixed|Zend_Config
     */
    public function getApplicationConfig ()
    {
        if($this->_cfg === null)
        {
            if(! ($this->_cfg = $this->getInvokeArg('bootstrap')->getApplicationConfig()) instanceof Zend_Config)
            {
                if(!defined(APPLICATION_REGISTRY_CONFIG) ||
                   !Zend_Registry::isRegistered(APPLICATION_REGISTRY_CONFIG) ||
                   !($this->_cfg = Zend_Registry::get(APPLICATION_REGISTRY_CONFIG))
                )
                {
                    if( ! ($this->_cfg = App_Abstract_Abstract::getConfig()) instanceof Zend_Config)
                    {
                        throw new RuntimeException('Can not retrive Configuration object');
                    }
                }
            }
        }
        return $this->_cfg;
    }

    /**
     * @return void
     */
    public function preDispatch()
    {
        if(in_array('logout', $this->_getAllParams()))
        {
            $params = $this->getRequest()->getParams();
            Zend_Session::stop();
            Zend_Session::destroy();
            $this->_helper->redirector->gotoSimpleAndExit('index', 'index', $params['module']);

        }
        
        $this->getResponse()->setRawHeader('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
        $this->getResponse()->setRawHeader('Cache-Control: no-cache, must-revalidate');
        $this->getResponse()->setRawHeader('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        /*
        $this->view->assign('url_cmp' = array(
            'action'=>$this->getRequest()->getActionName())
            'action'=>$this->getRequest()->getActionName())
            'action'=>$this->getRequest()->getActionName())
        );
         *
         */
    }
}
