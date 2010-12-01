<?php
abstract class App_Abstract_Controller_Action_Rest extends App_Abstract_Controller_Action
{
    /**
     *
     * @var Zend_Rest_Server
     */
    protected $_server;
    
    public function init()
    {
        parent::init();
        
        $lang = $this->_getParam('lang', 'de');
        Zend_Locale::setDefault($this->getCfg()->locale->{$lang});
        Zend_Registry::set(CONST_REG_LOCALE, new Zend_Locale($this->getCfg()->locale->{$lang}));
        
        $trOptions = array(
              'disableNotices'=>true,
              'logUntranslated'=>true,
              'scan'=>Zend_Translate::LOCALE_FILENAME
        );
        if($this->getCfg()->logging->translator)
        {
            // Eine Log Instanz erstellen
            $trOptions['log'] = new Zend_Log(new Zend_Log_Writer_Stream( $this->getCfg()->paths->logfile_translator ));
            $trOptions['logUntranslated'] = true;
        }
        $translate = new Zend_Translate(
                               'Gettext',
                               $this->getCfg()->paths->translationdir,
                               null,
                               $trOptions
        );
        
        $trOptions['scan'] = Zend_Translate_Adapter::LOCALE_FILENAME;
        $validatorTranslator = new Zend_Translate(
            'array',
            realpath($this->getCfg()->paths->validatorLanguagesDir),
            null,
            $trOptions
        );
        //echo realpath($this->getCfg()->paths->validatorLanguagesDir.DIRECTORY_SEPARATOR.'de'); die;
        if($this->getCfg()->useCache &&
           Zend_Registry::isRegistered(CONST_REG_CACHE) &&
           Zend_Registry::get(CONST_REG_CACHE) instanceof Zend_Cache_Core )
        {
            $translate->setCache(Zend_Registry::get(CONST_REG_CACHE));
            $validatorTranslator->setCache(Zend_Registry::get(CONST_REG_CACHE));
        }
        
        Zend_Registry::set(CONST_REG_TRANSLATOR, $translate);
        Zend_Validate_Abstract::setDefaultTranslator($validatorTranslator);
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_server = new Zend_Rest_Server;
        
    }
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    abstract public function indexAction();

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    abstract public function getAction();

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    abstract public function postAction();

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    abstract public function putAction();

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    abstract public function deleteAction();
}