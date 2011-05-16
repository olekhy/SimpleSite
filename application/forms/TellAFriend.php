<?php

class App_Form_TellAFriend extends App_Abstract_Form
{
    const NAME = 'tell_a_friend';
    const EL_RECIPNAME = 'mrecipient';
    const EL_SENDERNAME = 'msender';
    const EL_EMAILRECIP = 'mrecipient_email';
    const EL_EMAILSENDER = 'msender_email';
    const EL_CAPTCHA = 'captcha';
    const EL_MESSAGE = 'mmsg';
    const EL_BACK = 'reset';
    const EL_SEND = 'send';

    protected $_hideElemets = array();
    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setName(self::NAME);
        static $filters;
        if($filters === null)
        {
            $filters = array(
                'strip_tags'=>new Zend_Filter_StripTags,
                'strip_nl'=>new Zend_Filter_StripNewlines,
                'trim'=>new Zend_Filter_StringTrim
            );
        }
        
        $this->setElementDecorators(array(
            //'ViewHelper',
            //'Label',
            //'Errors',
            //array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            //array('Label', array('tag' => 'td'),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        

        $this->addElement('text', self::EL_RECIPNAME, array(
            'label'=>$this->_('form.tell.recipient.name'),
            'allowEmpty'=>false,
            'required'=>true,
            'filters'=>$filters,
            'maxlength'=>255
        ));


        $emailValidator = new Zend_Validate_EmailAddress();
        $this->addElement('text', self::EL_EMAILRECIP, array(
            'label'=>$this->_('form.email.recipient'),
            'validators'=>array(array('validator' => $emailValidator, 'breakChainOnFailure'=>false,)),
            'allowEmpty'=>false,
            'requered'=>true,
            'filters'=>$filters,
            'maxlength'=>255
        ));

        $this->addElement('text', self::EL_SENDERNAME, array(
            'label'=>$this->_('form.tell.sender.name'),
            'allowEmpty'=>false,
            'required'=>true,
            'filters'=>$filters,
            'maxlength'=>255
        ));
        $this->addElement('text', self::EL_EMAILSENDER, array(
            'label'=>$this->_('form.email.sender'),
            'validators'=>array(array('validator' => $emailValidator, 'breakChainOnFailure'=>false,)),
            'allowEmpty'=>false,
            'requerd'=>true,
            'filters'=>$filters,
            'maxlength'=>255
        ));

        $this->addElement('textarea', self::EL_MESSAGE, array(
            'label'=>'',//$this->_('form.tell.a.friend.message'),
            'validators'=>array(array('vlidator'=>new Zend_Validate_StringLength(array('max'=>1000)))),
            'rows'=>5,
            'cols'=>40,
            'value'=>$this->_('form.tel.a.friend.message'),
        ));

        $this->addElement('captcha', self::EL_CAPTCHA, array(
            'label'=>$this->_('form.tell.a.friend.please.type.the.words.shown'),
            'captcha'=>array(
                'captcha'=>'Image',
                'wordLen'=>'5',
                'timeout'=>300,
                'font'=>APPLICATION_PATH.'/../data/fonts/consola.ttf',
                'imgDir'=>$this->getApplicationConfig()->images->uploadpath,
                'imgUrl'=>Zend_Controller_Front::getInstance()->getBaseUrl().'/'.$this->getApplicationConfig()->uploaddirname,
                'height'=>42,
                'width'=>129,
                'dotNoiseLevel'=>0,
                'lineNoiseLevel'=>0,
                'fontSize'=>18

            )
        ));
        
        if($this->isDebug())
        {
            $this->getLog()->debug("captcha path".$this->getApplicationConfig()->images->uploadpath);
            $this->getLog()->debug("captcha url".Zend_Controller_Front::getInstance()->getBaseUrl().$this->getApplicationConfig()->uploaddirname);
        }

        $this->addElement('button', self::EL_BACK, array(
            'type'=>'button',
            'class'=>'tell-close',
            'label'=>$this->_('form.tell.a.friend.back')
        ));

        $this->{self::EL_SENDERNAME}->removeDecorator('label');
        $this->{self::EL_EMAILSENDER}->removeDecorator('label');
        $this->{self::EL_RECIPNAME}->removeDecorator('label');
        $this->{self::EL_EMAILRECIP}->removeDecorator('label');
        $this->{self::EL_CAPTCHA}->removeDecorator('label');
        $this->{self::EL_MESSAGE}->removeDecorator('label');
        $this->{self::EL_SENDERNAME}->removeDecorator('errors');
        $this->{self::EL_EMAILSENDER}->removeDecorator('errors');
        $this->{self::EL_RECIPNAME}->removeDecorator('errors');
        $this->{self::EL_EMAILRECIP}->removeDecorator('errors');
        $this->{self::EL_CAPTCHA}->removeDecorator('errors');
        $this->{self::EL_MESSAGE}->removeDecorator('errors');
        
        $this->addElement('button', self::EL_SEND, array(
            'type'=>'submit',
            'label'=>$this->_('form.tell.a.friend.send')
        ));



        if(is_array($this->_hideElemets) && $this->_hideElemets)
        {
            foreach ($this->_hideElemets as $elToHide)
            {
                if($this->getElement($elToHide) instanceof Zend_Form_Element)
                {
                    $this->removeElement($elToHide);
                    $this->addElement(new Zend_Form_Element_Hidden($elToHide));
                    $this->getElement($elToHide)->setAttrib('disabled','disabled');
                }
            }
        }

        //$group = $this->addDisplayGroup(
        //    array_keys($this->getElements()),'tell_a_friend',array('legend'=>$this->_('form.legend.tell.a.friend')));
    }

}
