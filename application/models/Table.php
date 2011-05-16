<?php

/**
 * 
 * 
 * @author al
 * @version     
 */

class App_Model_Table extends Zend_Db_Table_Abstract
{

    /**
     * @return string table name
     */
    public function getName()
    {
        return $this->info(self::NAME);
    }
   
    /**
     * @return string Primary field name
     */
    public function getPKeyName()
    {
        return (is_array($this->info(self::PRIMARY))) ? array_shift($this->info(self::PRIMARY)) : $this->info(self::PRIMARY);
    }

    /**
     * Generate world unique id identifier
     *
     * @param string $type is a table field name
     * @return string unique code | false if not succesfully generated
     */
    public function genCode()
    {
        static $n = 5;
        $table = "____reserved_code";
        $field = "uuid";

        for($i=0; $i<$n; $i++)
        {
            $select = new Zend_Db_Select($this->getAdapter());
            $subSel = clone $select;
            $stmt = $select
                    ->from($table, array())
                    ->columns(new Zend_Db_Expr(" SUBSTRING_INDEX(uuid(), '-', 1) AS ".$field ))
                    ->having($field.' NOT IN ('.
                        $subSel
                        ->from($table, array())
                        ->columns(array($field))
                        ->__toString() .')'
                )
            ;
            $rs = $this->getAdapter()->query($stmt->__toString())->fetchObject();

            if($rs->{$field})
            {
                $stmt = $this->getAdapter()->prepare('INSERT INTO `'.$table.'` (`'.$field.'`) VALUES( ? )');
                try
                {
                    if( false === $stmt->execute(array($rs->{$field})))
                    {
                        throw new RuntimeException('');
                    }
                    break;
                }
                catch (RuntimeException  $e)
                {
                    continue;
                }
            }
        }
        if(!$rs->{$field})
        {
            static $r = 0;
            $rs = $this->getAdapter()->query("SELECT SUBSTRING_INDEX(uuid(), '-', 1)")->fetchColumn();
            $stmt = $this->getAdapter()->prepare('INSERT INTO `'.$table.'` (`'.$field.'`) VALUES( ? )');
            try
            {
                $stmt->execute(array($rs));
                $r++;
                if(false === $stmt || ($r>=$n) || !$rs)
                {
                    throw new RuntimeException('Can not generate an unique ident code.');
                }
                return $this->genCode();

            }
            catch(RuntimeException $e)
            {
                if(($log = $this->getLog()) instanceof Zend_Log)
                {
                    $log->emerg($e);
                    return null;
                }
                else
                {
                    throw new RuntimeException($e->getMessage());
                }
            }
        }
        return $rs->{$field};
    }

    /**
     * @param bool $withFromPart
     * @return Zend_Db_Table_Select
     */
    public function select($withFromPart = self::SELECT_WITHOUT_FROM_PART)
    {
        $select = new App_Model_Table_Select($this);
        if ($withFromPart == self::SELECT_WITH_FROM_PART) {
            $select->from($this->info(self::NAME), Zend_Db_Table_Select::SQL_WILDCARD, $this->info(self::SCHEMA));
        }
        return $select;
    }

    /**
     * @param  $data
     * @return mixed
     */
    public function insert(array $data)
    {
        // add a timestamp
        $filedPrefix = $this->info(self::NAME);
        $tsField = $filedPrefix."created";
        if (!array_key_exists($tsField, $data) || empty($data[$tsField])) {
            $data[$tsField] = date('Y-m-d H:i:s');
        }
        return parent::insert($data);
    }

    /**
     * @param  $data
     * @param  $where
     * @return int
     */
    //public function update(array $data, $where)
    //{
        // add a timestamp
    //    if (empty($data['updated_on'])) {
    //        $data['updated_on'] = time();
    //    }
    //    return parent::update($data, $where);
    //}
    
}

