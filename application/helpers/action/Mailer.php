<?php
/**
 *
 * @author al
 * @version
 */
/**
 * Mailer Action Helper
 *
 * @uses actionHelper Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_Mailer extends Zend_Controller_Action_Helper_Abstract
{
    /**
     *
     * @var Zend_Mail
     */
    protected $_mailer = null;
    /**
     *
     * @var Zend_Config
     */
    protected $_cfg;
    /**
     * @var Zend_Loader_PluginLoader
     */
    public $pluginLoader;
    
    /**
     * Constructor: initialize plugin loader
     *
     * @return void
     */
    public function __construct ()
    {
        // TODO Auto-generated Constructor
        $this->pluginLoader = new Zend_Loader_PluginLoader();
        
        $this->_mailer = new Zend_Mail($this->_cfg->mailer->encoding);
        
        $this->_cfg = $this->getActionController()->getCfg();
        
        $this->_mailer->setFrom(
                (string) $this->_cfg->mailer->from->mail,
                (string) $this->_cfg->mailer->from->name
        );
    }
    /**
     * Strategy pattern: call helper as broker method
     */
    public function direct ()
    {    // TODO Auto-generated 'direct' method
    }
    
    
    
    public function send($transport = 'smtp', $toEmailAddress, $toName, $bodyTemplate, $bodyReplaces, $mailSubject )
    {
        
        try
        {
            if(!method_exists($this, 'getTransport'.$transport))
            {
                throw new RuntimeException('Can not initiate mail transport object');
            }
        }
        catch (RuntimeException $e)
        {
            $this->getActionController()->getLog()->err($e);
            return null;
        }
        
        $this->_mailer->addTo($toEmailAddress, $toName);
        
        $search = array(
            '/{url}/'
        );
        $replace = array(
            $regUrl
        );
        
        //$body = preg_replace($search, $replace, $body);
        
        $this->_mailer->setBodyHtml(preg_replace($search, $replace, $bodyTemplate));
        $this->_mailer->setSubject($mailSubject);
        
        try
        {
            $this->_mailer->send($transport);
        }
        catch(Zend_Mail_Transport_Exception $e)
        {
            $this->getActionController()->getLog()->err($e);
            return null;
        }
        
    }
    
    /**
     *
     */
    public function getTransportSmtp()
    {
         $config = array(
                   'username'=>$this->_cfg->mailer->username,
                   'password'=>$this->_cfg->mailer->password,
                   'auth'=>$this->_cfg->mailer->auth,
                   'host'=>$this->_cfg->mailer->host,
                   //'ssl' => 'ssl',
                   //'port' => 587
            ); // Optional port number supplied

        return new Zend_Mail_Transport_Smtp($this->_cfg->mailer->host, $config);
            
//        $mail = new Zend_Mail();
//        $hash = md5(
//            $user['username'].
//            $user['email'].
//            $user['password'].
//            $user['visible'].
//            $user['date_lastlogin']
//        );
//        $hash .= '-'.$user['id'];
    }
    
}

