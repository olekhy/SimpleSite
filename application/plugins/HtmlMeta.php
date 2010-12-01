<?php
class App_Plugin_HtmlMeta extends Zend_Controller_Plugin_Abstract
{
	/**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $frontController = Zend_Controller_Front::getInstance();
        $bs = $frontController->getParam('bootstrap');
        $cfg = $bs->getApplication()->getOptions();

        $title = isset($cfg['title'])?$cfg['title']:'';
        $js = $cfg['uri']['js'];
        $css = $cfg['uri']['css'];
        $img = $cfg['uri']['images'];
        $swf = $cfg['uri']['swf'];

        $bs->view->headTitle($title);
        $bs->view->headMeta()->setHttpEquiv('Content-Type', 'text/html; charset=' . $bs->view->getEncoding());

        $bs->view->headScript()->appendFile($bs->view->baseUrl($js.'/jquery/jquery.js'));
        $bs->view->headScript()->appendFile($bs->view->baseUrl($js.'/jquery/mousewheel.js'));
        $bs->view->headScript()->appendFile($bs->view->baseUrl($js.'/jquery/jScrollPane.js'));
        $bs->view->headScript()->appendFile($bs->view->baseUrl($js.'/swfobject.js'));

        if(file_exists(getcwd().$css.'/common.css'))
        {
            $bs->view->headLink()->appendStylesheet($bs->view->baseUrl($css.'/common.css'));
        }
        else
        {
            $bs->view->headLink()->appendStylesheet($bs->view->baseUrl($css.'/reset.css'));
            $bs->view->headLink()->appendStylesheet($bs->view->baseUrl($css.'/text.css'));
            $bs->view->headLink()->appendStylesheet($bs->view->baseUrl($css.'/links.css'));
            $bs->view->headLink()->appendStylesheet($bs->view->baseUrl($css.'/style.css'));
        }

        $locale = $bs->getResource('Locale');
        $localeCSSFile = null;
        if($locale){
            $localeCSSFile =  getcwd().$css."/{$locale->getLanguage()}.css";
        }

        if(file_exists($localeCSSFile)) {
            $bs->view->headLink()->appendStylesheet($bs->view->baseUrl($css."/{$locale->getLanguage()}.css"));
        }
        else
        {
            if(defined('DEBUG') && DEBUG)
            {
                $bs->getResource('Log')->log('Specified CSS file '.$localeCSSFile.' for given locale not exists and can not be included.', Zend_Log::NOTICE);
            }
        }
        $bs->view->headLink()->appendStylesheet($bs->view->baseUrl($css.'/ie.css'), 'screen', 'IE');

        ((defined('IMAGE_URL') && $bs->view->IMG = IMAGE_URL)) or ($bs->view->IMG = (($img)?$img:'/img'));
        ((defined('JS_URL') && $bs->view->JS = JS_URL)) or ($bs->view->JS = (($js)?$js:'/js'));
        ((defined('CSS_URL') && $bs->view->CSS = CSS_URL)) or (($bs->view->CSS = (($css)?$css:'/css')));
        ((defined('SWF_URL') && $bs->view->SWF = SWF_URL)) or (($bs->view->SWF = (($swf)?$swf:'/swf')));

    }
}

