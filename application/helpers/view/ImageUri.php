<?php
/**
 * Created by PhpStorm.
 * User: al
 * Date: Nov 2, 2010
 * Time: 10:48:31 AM
 * @version $Id: ImageUri.php 4448 2011-03-14 00:53:21Z khueoreeskyy@webfact.de $
 * To change this template use File | Settings | File Templates.
 */

class Zend_View_Helper_ImageUri
{
    const DEL = '-';

    /**
     * @var Zend_View_Helper_Url
     */
    protected $_uri;

    /**
     * @var string image file name
     */
    protected $_file;

    /**
     * @var Zend_View
     */
    public $view;

    /**
     * @param  string $file
     * @return Zend_View_Helper_ImageUri
     */
    public function imageUri($file = '')
    {
        $this->_file = $file;
        $this->_uri = $this->view->baseUrl().'/media/image'; 
        return $this;
    }

    /**
     * @param int $width
     * @return Zend_View_Helper_ImageUri
     */
    public function w($width)
    {
        if(is_numeric($width) && $width > 0)
        {
            $this->_uri.="/w".$width;
        }
        return $this;

    }

    /**
     * @param int $height
     * @return Zend_View_Helper_ImageUri
     */
    public function h($height)
    {
        if(is_numeric($height) && $height > 0)
        {
            $this->_uri.="/h".$height;
        }
        return $this;
    }

    /**
     * @param string $cropRate
     * @return Zend_View_Helper_ImageUri
     */
    public function r($cropRate)
    {
        if($cropRate && strpos($cropRate,'x') !== false)
        {
            $this->_uri.='/r'.$cropRate;
        }
        return $this;
    }

    /**
     * @param int $quality
     * @return Zend_View_Helper_ImageUri
     */
    public function q($quality)
    {
        if(is_numeric($quality) && $quality > 0)
        {
            $this->_uri.='/q'.$quality;
        }
        return $this;
    }

    /**
     * @return Zend_View_Helper_ImageUri
     */
    public function nochache()
    {
        $this->_uri.='/f1';
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->_uri.='/'.self::DEL.(($this->_file)?'/'.trim($this->_file, " \t\r\n\\/"):'');
        try
        {
            //Tools_Image::initLog(Zend_Registry::get(APPLICATION_REGISTRY_LOG));
            Tools_Image::storeUri($this->_uri);
        }
        catch(Exception $e)
        {
            return $e->getMessage().PHP_EOL.((defined ('DEBUG' && DEBUG))?$e->getTraceAsString():'');
        }
        return $this->_uri;
    }




    /**
     * @param  $view
     * @return void
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}
