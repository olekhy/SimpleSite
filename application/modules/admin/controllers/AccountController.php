<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Created by PhpStorm.
 * User: al
 * Date: Nov 26, 2010
 * Time: 11:31:06 PM
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
 * @author     Original Author  ${AUTHOR} <${AUTHOREMAIL}>
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

class Admin_AccountController extends Zend_Controller_Action
{


    /**
     * @return void
     */
    //public function init()
    //{
    //    parent::init();
    //}

    /**
     * @return void
     */
    public function loginAction()
    {
        $url = $this->view->url();
        $form = new Application_FormAdminLogin(array('action'=>$url));
        $this->view->form = $form;

        if($this->_request->isPost() && $form->isValid($this->_request->getPost()))
        {
            if($this->_helper->authenticate($form->getValues(), 'admin-users')->isAuthenticated())
            {
                $this->_redirect($this->_getParam('url', $this->view->url(array(), 'admin-index', true)));
            }
            Application_Message::addError('Incorrect username or password entered');
        }
    }
    
}
