<?php

class App_Form_Image extends App_Abstract_Form
{
    const NAME = 'upload_image';
    const EL_IMAGE = 'iimage';
    const EL_CHALL = 'challenge';
   
    protected $_hideElemets = array();

    protected $_destination;
    protected $_maxFiles;
    protected $_maxFileSize;
    protected $_imageWidthMin;
    protected $_imageWidthMax;
    protected $_imageHeightMin;
    protected $_imageHeightMax;

    protected $_chall;
    
    protected $_newDestination;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setName(self::NAME);
        $this->setMethod('post');

        $this->addElement('file', self::EL_IMAGE, array(
            'required'=>false,
            'allowEmpty'=>false,
            'Destination'=>$this->_destination,
            'MaxFileSize'=>$this->_maxFileSize,
            'MultiFile'=>$this->_maxFiles,
            'Validators'=>array(
                array('validator'=>'IsImage','breakChainOnFailure'=>false),
            )
        ));
        
        $this->{self::EL_IMAGE}->addValidator('ImageSize', false, array(
                        'minwidth' => $this->_imageWidthMin,
                        'maxwidth' => $this->_imageWidthMax,
                        'minheight' =>$this->_imageHeightMin,
                        'maxheight' =>$this->_imageHeightMax
                      ));


        $this->addElement('select', self::EL_CHALL, array(
            'required'=>true,
            'allowEmpty'=>false,
            'label'=>$this->_('form.create.image.select.challenge').':*',
            'multiOptions'=>array(''=>$this->_('form.please.choose')) + $this->_chall,
            'ErrorMessages'=>array(
                Zend_Validate_InArray::NOT_IN_ARRAY => $this->_('form.error.please.choose.challenge'))
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
        $filter = new Zend_Filter_File_Rename($this->getDestination());
        foreach($this->{App_Form_Image::EL_IMAGE}->getFileInfo() as $k => $fv)
        {
            if(!$fv['name']=='')
            {

                $filter->addFile(
                    array('source' => $fv['tmp_name'],
                          'target' => $this->getNewDestination().'/'.uniqid().strrchr($fv['name'],'.'),
                          'overwrite' => false )
                );
            }
        }
        $this->{App_Form_Image::EL_IMAGE}->addFilter($filter);
        if(count($this->{self::EL_IMAGE}->getValue()) < 1)
        {
            $this->addError($this->_('form.error.upload.image.was.not.selected'));
            $return = false;
        }
        return $return;
    }

    /**
     * @param string $destination
     * @return App_Form_Image
     */
    public function setDestination($destination)
    {
        $this->_destination = DIRECTORY_SEPARATOR.trim($destination, " \n\r\t\\/");
        return $this;
    }
    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->_destination;
    }
    /**
     * @param  $fileSize
     * @return App_Form_Image
     */
    public function setMaxFileSize($fileSize)
    {
        if (!is_numeric($fileSize))
        {
            $type = substr(strtoupper($fileSize), -1);
            if($type == 'B'){ $type = substr($fileSize, -2, 1);}

            $this->_maxFileSize = (integer) substr($fileSize, 0, -1);

            switch ($type)
            {
                case 'K' :
                    $this->_maxFileSize *= 1024;
                    break;

                case 'M' :
                    $this->_maxFileSize *= 1024 * 1024;
                    break;

                case 'G' :
                    $this->_maxFileSize *= 1024 * 1024 * 1024;
                    break;

                default :
                    break;
            }
        }
        else $this->_maxFileSize = (integer) $fileSize;
        return $this;
    }
    /**
     * @param int $quantity
     * @return void
     */
    public function setMaxFiles($quantity)
    {
        $this->_maxFiles = 1;
        if(is_numeric($quantity) && $quantity > 0)
        {
            $this->_maxFiles = $quantity;
        }
        return $this;
    }
    /**
     * @param  $val
     * @return void
     */
    public function setAllowedHeight($val)
    {
        $this->_imageHeightMax = (integer) $val;
        $this->_imageHeightMin = (integer) $val;
        return $this;
    }
    /**
     * @param  $val
     * @return void
     */
    public function setAllowedWidth($val)
    {
        $this->_imageWidthMax = (integer) $val;
        $this->_imageWidthMin = (integer) $val;
        return $this;
    }
    /**
     * @param  $val
     * @return void
     */
    public function setAllowedMinHeight($val)
    {
        $this->_imageHeightMin = (integer) $val;
        return $this;
    }
    /**
     * @param  $val
     * @return void
     */
    public function setAllowedMinWidth($val)
    {
        $this->_imageWidthMin = (integer) $val;
        return $this;
    }
/**
     * @param  $val
     * @return void
     */
    public function setAllowedMaxHeight($val)
    {
        $this->_imageHeightMax = (integer) $val;
        return $this;
    }
    /**
     * @param  $val
     * @return void
     */
    public function setAllowedMaxWidth($val)
    {
        $this->_imageWidthMax = (integer) $val;
        return $this;
    }

    /**
     *
     *
     * @return string path
     */
    private function getNewDestination( )
    {
        return $this->_newDestination;
    }

    /**
     *
     *
     * @param  string $newDestination
     * @return App_Form_Image
     */
    public function setNewDestination($newDestination)
    {
        $this->_newDestination = $newDestination;
        return $this;
    }

    /**
    * @param array $data
    * @return App_Form_Image
    */
    public function setChallenges(array $data = array())
    {                      
        $this->_chall = $data;
        return $this;
    }
}

