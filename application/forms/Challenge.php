<?php

class App_Form_Challenge extends App_Abstract_Form
{
    const NAME = 'challenge';

    const EL_NAME = 'cname';
    const EL_BEGIN = 'cbegin';
    const EL_END = 'cend';
    const EL_DESC = 'cdesc';
    const EL_SUBMIT = 'submit';



    const EL_BEGIN_H= 'sh';
    const EL_BEGIN_Y = 'sy';
    const EL_BEGIN_M = 'sm';
    const EL_BEGIN_D = 'sd';


    const EL_END_Y= 'ey';
    const EL_END_M= 'em';
    const EL_END_D= 'ed';
    const EL_END_H= 'eh';

    protected $_hideElemets = array();


    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setName(self::NAME);
        $this->setMethod('post');

        $this->addElement('hidden', self::EL_BEGIN);
        $this->addElement('hidden', self::EL_END);

        static $d; if($d==null){$d = date('d');}
        static $m; if($m==null){$m = date('m');}
        static $y; if($y==null){$y = date('Y');}

        $this->addElement('text', self::EL_NAME, array(
            'required'=>true,
            'allowempty'=>false,
            'filters'=>array('StringTrim', 'StripTags'),
            'maxlength'=>255,
            'label'=>$this->_('form.create.challenge.name').':*',
            'errorMessages'=>array($this->_('form.error.please.enter.challenge.name'))
        ));

        $this->addElement('select', self::EL_BEGIN_H, array(
            'MultiOptions'=>array(''=>$this->_('hour')) + App_Form_Helpers_DateTimeValues::getHours(':00'),
            'AllowEmpty'=>false,
            'Required'=>true,
            'label'=>$this->_('form.create.challenge.begin.date').':*',
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge.begin.hour'))
        ));
        //$this->{self::EL_BEGIN_H}->removeDecorator('Label');
        
        $this->addElement('select', self::EL_BEGIN_D, array(
            'MultiOptions'=>array(''=>$this->_('day')) + App_Form_Helpers_DateTimeValues::getDays(),
            'AllowEmpty'=>false,
            'Required'=>true,
            'label'=>$this->_('form.create.challenge.begin.day').':*',
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge.begin.day'))
        ));

        $this->addElement('select', self::EL_BEGIN_M, array(
            'Value'=>$m,
            'MultiOptions'=>array(''=>$this->_ ('month')) + App_Form_Helpers_DateTimeValues::getMonth(),
            'AllowEmpty'=>false,
            'Required'=>true,
            'label'=>$this->_('form.create.challenge.begin.month').':*',
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge.begin.month'))
        ));
        $this->{self::EL_BEGIN_M}->removeDecorator('Label');
        
        $this->addElement('select', self::EL_BEGIN_Y, array(
            'Value'=>$y,
            'MultiOptions'=>array(''=>$this->_('year')) + App_Form_Helpers_DateTimeValues::getYears($y, $y+1),
            'AllowEmpty'=>false,
            'Required'=>true,
            'label'=>$this->_('form.create.challenge.begin.year').':*',
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge.begin.year'))
        ));
        $this->{self::EL_BEGIN_Y}->removeDecorator('Label');



        
        $this->addElement('select', self::EL_END_H, array(
            'MultiOptions'=>array(''=>$this->_('hour')) + App_Form_Helpers_DateTimeValues::getHours(':00'),
            'AllowEmpty'=>false,
            'Required'=>true,
            'label'=>$this->_('form.create.challenge.end.date').':*',
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge.end.hour'))
        ));

        $this->addElement('select', self::EL_END_D, array(
            'MultiOptions'=>array(''=>$this->_('day')) + App_Form_Helpers_DateTimeValues::getDays(),
            'AllowEmpty'=>false,
            'Required'=>true,
            'label'=>$this->_('form.create.challenge.end.day').':*',
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge.end.day'))
        ));
        $this->{self::EL_END_D}->removeDecorator('Label');

        $this->addElement('select', self::EL_END_M, array(
            'Value'=>$m,
            'MultiOptions'=>array(''=>$this->_ ('month')) + App_Form_Helpers_DateTimeValues::getMonth(),
            'AllowEmpty'=>false,
            'Required'=>true,
            'label'=>$this->_('form.create.challenge.end.month').':*',
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge.end.month'))
        ));
        $this->{self::EL_END_M}->removeDecorator('Label');

        $this->addElement('select', self::EL_END_Y, array(
            'Value'=>$y,
            'MultiOptions'=>array(''=>$this->_('year')) + App_Form_Helpers_DateTimeValues::getYears($y, $y+1),
            'AllowEmpty'=>false,
            'Required'=>true,
            'label'=>$this->_('form.create.challenge.end.year').':*',
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge.end.year'))
        ));
        $this->{self::EL_END_Y}->removeDecorator('Label');
        
        $this->addElement('textarea', self::EL_DESC, array(
            'cols'=>40,
            'Label'=>$this->_('form.create.challenge.description'),
            'rows'=>4,
            'maxlength'=>1000,
            'filters'=>array('StringTrim', 'StripTags'),
            'validators'=>array(array('StringLength', false, array(1, 255))),
            'allowempty'=>true
        ));

        $this->addElement('submit', self::EL_SUBMIT, array(
            'label'=>$this->_('form.button.submit.challenge.create')
        ));

        if(is_array($this->_hideElemets))
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

        //$group = $this->addDisplayGroup(array_keys($el),'gr_'.self::NAME,array('legend'=>$this->_('admin.create.new.team.form')));
    }


    /**
     * @param  $data
     * @return bool
     */
    public function isValid($data)
    {
        if(false === parent::isValid($data))
        {
            return false;
        }
        $return = true;
        $beginDataDate[] = $this->{self::EL_BEGIN_Y}->getValue();
        $beginDataDate[] = $this->{self::EL_BEGIN_M}->getValue();
        $beginDataDate[] = $this->{self::EL_BEGIN_D}->getValue();
        //$beginDataDate[] = $this->{self::EL_BEGIN_H}->getValue();
        $beginDataDate = join('-', $beginDataDate).' '.$this->{self::EL_BEGIN_H}->getValue().':00:00';

        $beginTs = mktime(
            $this->{self::EL_BEGIN_H}->getValue(),
            '00',
            '00',
            $this->{self::EL_BEGIN_M}->getValue(),
            $this->{self::EL_BEGIN_D}->getValue(),
            $this->{self::EL_BEGIN_Y}->getValue()
        );
        $begin = date('Y-m-d G:i:s', $beginTs);

        if($begin != $beginDataDate)
        {
            $this->addError($this->_('form.error.upload.begin.date.is.invalid'));
            $return = false;
        }

        $endDataDate[] = $this->{self::EL_END_Y}->getValue();
        $endDataDate[] = $this->{self::EL_END_M}->getValue();
        $endDataDate[] = $this->{self::EL_END_D}->getValue();

        $endDataDate = join('-', $endDataDate) . ' '.$this->{self::EL_END_H}->getValue().':00:00';
        $endTs = mktime(
            $this->{self::EL_END_H}->getValue(),
            '00',
            '00',
            $this->{self::EL_END_M}->getValue(),
            $this->{self::EL_END_D}->getValue(),
            $this->{self::EL_END_Y}->getValue()
        );
        $end = date('Y-m-d G:i:s', $endTs);
        if($end != $endDataDate)
        {
            $this->addError($this->_('form.error.upload.end.date.is.invalid'));
            $return = false;
        }
        
        if($return === true)
        {
            if($endTs-$beginTs<0)
            {
                $this->setErrors(array($this->_('form.error.challenge.begin.date.is.early.as.end.date')));
                $return = false;
            }
        }
        $this->{self::EL_END}->setValue($endDataDate);
        $this->{self::EL_BEGIN}->setValue($beginDataDate);

        return $return;
    }

    /**
     * @param  $defaults
     * @return Zend_Form
     */
    public function setDefaults(array $defaults)
    {
        if(isset($defaults[self::EL_BEGIN]))
        {
            $date = explode("-", $defaults[self::EL_BEGIN]);
            if(count($date) == 3)
            {
                $defaults[self::EL_BEGIN_H] = $date[3];
                $defaults[self::EL_BEGIN_D] = $date[2];
                $defaults[self::EL_BEGIN_M] = $date[1];
                $defaults[self::EL_BEGIN_Y] = $date[0];
            }
        }
        if(isset($defaults[self::EL_END]))
        {
            $date = explode("-", $defaults[self::EL_END]);
            if(count($date) == 3)
            {
                $defaults[self::EL_END_H] = $date[3];
                $defaults[self::EL_END_D] = $date[2];
                $defaults[self::EL_END_M] = $date[1];
                $defaults[self::EL_END_Y] = $date[0];
            }
        }
        return parent::setDefaults($defaults);
    }


}

