<?php

/**
 * Privacy Policy Link helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class App_View_Helper_PrivacyPolicyLink
{

	/**
	 * @var Zend_View_Interface
	 */
	public $view;

	/**
	 *
	 */
	public function privacyPolicyLink($urlPath, $linkText = 'Privacy policy', $renderAs = 'html', $onEventType=null, $onEventAction=null)
	{
		$linkText = $this->view->translate($linkText);
		if('html' == $renderAs)
		{
			return '<a href="'.$this->view->baseUrl($urlPath).'" title="'.$linkText.'">'.$linkText.'</a>'.PHP_EOL;
		}
		else
		{
			return $linkText . ' ' . $this->view->baseUrl($urlPath);
		}
	}

	/**
	 * Sets the view field
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}

