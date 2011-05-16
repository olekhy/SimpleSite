<?php

/**
 * this class declaration for source code DOCUMENTATION only ig Intellisense
 *  
 * 
 * @method renderToPlaceholder($script, $placeholder) Renders a template and stores the rendered output as a placeholder
 * variable for later use.
 * @method Zend_View_Helper_Translate translate($string) translate given string to current locale language 
 * @method Zend_View_Helper_HeadScript headScript() append script to html tag head 
 * @method Zend_View_Helper_BaseUrl baseUrl($string = '') get base URL 
 * @method Zend_View_Helper_FilterMenu filterMenu() helper build navigation menu right 
 * @method Zend_View_Helper_Url url(array $urlOptions = array(), $name = null, $reset = false, $encode = true) helper build URL
 * @method Zend_View_Helper_ImageUri imageUri($fileNameImage = '')  helper build image URL with transformations params
 * @method Zend_View_Helper_HeadLink headLink(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)  
 * @method Zend_View_Helper_HeadMeta headMeta(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)  
 * @method Zend_View_Helper_HeadScript headScript(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)  
 * @method Zend_View_Helper_HeadStyle headStyle(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)  
 * @method Zend_View_Helper_HeadTitle headTitle(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)  
 *
 * @method Zend_View_Helper_Placeholder placeholder($placeholderName)
 *  
 * @method Zend_Layout layout()  
 *  helper build image URL with transformations params
 */
final class Zend_View_Interface
{
    /**           
     * Form or other error array
     * @var array
     */
    public $errors;
    /**
     * @var App_Form_TellAFriend
     */
    public $form;
    /**
     * @var boolean
     */
    public $hasVoted;
    /**
     * @var string
     */
    public $reset;
    /**
     * @var Zend_Db_Table_Rowset
     */
    public $images;
    /*
    public $hasVoted;
    public $hasVoted;
     */
    /**
     * @var string
     */
    public $facebookUri;
    /**             
     * Contains value for html document base tag href
     * @var string  
     */
    public $BASETAG;
    /**            
     * hash for identify an user in browser
     * @var string
     */    
    public $hash;
    /**             
     * Team URI key this indicate a team in the URI
     * @var string 
     */    
    public $turikey;
    /**
     * @var string
     */
    public $from;
    /**
     * @var string
     */
    public $to;
    /**
     * array params for first voting page
     */
    public $urlParamsStartVoting;

    /**
     * @var Header title text in gallery image view 
     */
    public $headerTextImage;
    /**         
     * count voting for an picture
     * @var int
     */    
    public $votingCurrentResult;
    /**           
     * next team row values relative to current team
     * @var array
     */
    public $next;
}

final class Zend_Config 
{
    /**
     * @var list of domains
     */
    public $domain;
    /**                                          
     * @var ssl will be used if value 1 or not 2 
     */
    public $usingSSL;
    /**
     * @var string salt for crypt
     */
    public $salt;
    /**
     * @var string
     */
    public $facebookUri;
    /**
     * @var stdClass settings for mailer
     */
    public $mailer;
    
    /**
     * @var on or off is the logging
     */
    public $logging;
}




