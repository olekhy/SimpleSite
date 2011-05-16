<?php
/**
 * App - Motorcycle Booking Tool Action Controller Class
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
 */

class Media_IndexController extends App_Abstract_Controller_Action
{
    /**
     *
     */
    public function init()
    {
        parent::init();
        //$this->view->BASETAG = $this->serverUrl();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $this->getFrontController()->returnResponse(true);
        Tools_Image::init(array(
            //'rootdir'=>$this->getRequest()->getServer('DOCUMENT_ROOT'),
            'rootdir'=>getcwd(),
            'cachedir'=>CACHE_DIR,
            'request'=>$this->getRequest(),
            'response'=>$this->getResponse(),
            //'log'=>$this->getLog()            
        ));
    }

    public function indexAction()
    {
        if(preg_match_all('/([whrcqf]{1})([^\/]+)/', $this->_getParam('params'), $matsches))
        {
            Tools_Image::setParams(array_combine($matsches[1], $matsches[2]));
        }
        else Tools_Image::setParams();

        //Tools_Image::disableResponse();
        Tools_Image::disableCheckOurImage();
    }


    public function postDispatch()
    {
        $this->getResponse()->clearBody();
        $this->getResponse()->setBody(Tools_Image::get());
        $this->getFrontController()->returnResponse(false);
        //$this->getResponse()->sendResponse(true);
    }


}

