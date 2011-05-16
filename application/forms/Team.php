<?php

class App_Form_Team extends App_Abstract_Form
{
    const NAME = 'simple_team';
    const EL_NAME = 'tname';
    const EL_DESC = 'tdesc';
    const EL_URIKEY = 'turikey';
    const EL_OWNER = 'uid';
    const EL_STATUS = 'tstatus';

    const EL_SEND = 'send';
    protected $_hideElemets = array();

    protected $_owners = array();

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setName(self::NAME);
        $this->setMethod('post');

        $this->addElement('text', self::EL_NAME, array(
            'required'=>true,
            'allowempty'=>false,
            'filters'=>array('StringTrim','StripTags','StripNewlines'),
            'validators'=>array(array('StringLength', false, array(4, 42))),
            'maxlength'=>42,
            'label'=>$this->_('form.create.team.name').':*'
        ));

        $this->addElement('text', self::EL_DESC, array(
            'required'=>false,
            'allowempty'=>true,
            'filters'=>array('StringTrim','StripTags','StripNewlines'),
            'validators'=>array(array('StringLength', false, array(4, 42))),
            'maxlength'=>42,
            'label'=>$this->_('form.create.team.description').':'
        ));

        $this->addElement('select', self::EL_STATUS, array(
            'required'=>true,
            'allowempty'=>false,
            'multiOptions'=>array(
                ''=>$this->_('form.please.choose'),
                'active' => $this->_('admin.team.stautus.active'),
                'inactive' => $this->_('admin.team.status.inactive')),
            'filters'=>array('StringTrim','StripTags','StripNewlines'),
            'validators'=>array(array('InArray', false, array(
                array(
                    'active',
                    'inactive')))),
            'label'=>$this->_('admin.team.status').':*',
            'errorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.team.status'))
        ));

        $this->addElement('select', self::EL_OWNER, array(
            'required'=>true,
            'allowempty'=>false,
            'filters'=>array('StringTrim','StripTags','StripNewlines'),
            'multiOptions'=>array(''=>$this->_('form.please.choose')) + $this->_owners,
            'validators'=>array(array('InArray', false, array(array_keys($this->_owners)))),
            'label'=>$this->_('admin.team.owner').':*',
            'errorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.team.owner'))
        ));

        $rv = new Zend_Validate_Regex('/^[a-z0-9\_\-\.]+$/i');
        $rv->setMessage('form.error.please.choose.team.uri.key.must.be.onliy.alphanum');

        $this->addElement('text', self::EL_URIKEY, array(
            'required'=>true,
            'allowempty'=>false,
            'filters'=>array('StringTrim','StripTags','StripNewlines'),
            'validators'=>array(
                array('StringLength', false, array(3, 42)),
                array($rv)
            ),
            'maxlength'=>42,
            'label'=>$this->_('form.create.team.uri.key').':*'
        ));

        $this->addElement('button', self::EL_SEND, array(
            'label'=>$this->_('form.create.team.submit'),
            'type'=>'submit'
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

        $group = $this->addDisplayGroup(array_keys($this->getElements()),'gr_'.self::NAME,array('legend'=>$this->_('admin.create.new.team.form')));
    }

    /**
     * @param  $owner
     * @return void
     */
    public function setOwners(array $owners)
    {
        $this->_owners = $owners;
    }

    /**
     * @param  $options
     * @return void
     *
    public function setValidateExistsTurikey($options)
    {
        $this->{self::EL_URIKEY}->addValidator(
            new Zend_Validate_Db_NoRecordExists($options)
        );
    }

    **
     * @param  $options
     * @return void
     *
    public function setExistsName($options)
    {
        $this->{self::EL_NAME}->addValidator(
            new Zend_Validate_Db_NoRecordExists($options)
        );
    }
    */
}
