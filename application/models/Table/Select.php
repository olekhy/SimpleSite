<?php
/**
 * Created by PhpStorm.
 * User: al
 * Date: Oct 17, 2010
 * Time: 10:25:00 AM
 * @version $Id: Select.php 4448 2011-03-14 00:53:21Z khueoreeskyy@webfact.de $
 * To change this template use File | Settings | File Templates.
 *
 * @method pk() App_Model_Table_Select pk($id) return Select object with where clause for primary key id
 */

class App_Model_Table_Select extends Zend_Db_Table_Select
{

    /**
     * database table name
     *
     * @return string
     */
    public function getTableName()
    {
        static $tableName;
        if($tableName == null)
        {
            $tableName = $this->getTable()->info(Zend_Db_Table_Abstract::NAME);
        }
        return $tableName;
    }
    /**
     * @return mixed
     */
    public function fetchOne()
    {
        $this->getTable()->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);
        return $this->getTable()->getAdapter()->fetchOne($this);
    }
    /**
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function fetchRow()
    {
        return $this->getTable()->fetchRow($this);
    }

    /**
     * @param string $message
     * @return
     */
    public function fetchRowIfExists($message  = 'Content was called which not exists.')
    {
        return $this->getTable()->fetchRowIfExists($this, $message);
    }

    /**
     * @param  $limit
     * @param  $offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchAll($limit = null, $offset = null) // Not the best method name)
    {
        if ($limit) {
            $this->limit($limit, $offset);
        }

        return $this->getTable()->fetchAll($this);
    }

    /**
     * @return array
     */
    public function fetchArrayIdName()
    {
        $this->getTable()->getAdapter()->setFetchMode(Zend_Db::FETCH_ASSOC);
        $rs = array();
        foreach($this->getTable()->getAdapter()->fetchAll($this) as $k => $v)
        {
            $rs[$v['uid']] = $v['uname'];
        }
        
        return $rs;
    }
    /**
     * @return array
     */
    public function fetchCol()
    {
        $ad = $this->getTable()->getAdapter(); 
        //$ad->setFetchMode(Zend_Db::FETCH_ASSOC);
        return $ad->fetchCol($this);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param int $pageRange
     * @return Zend_Paginator
     */
    public function getPaginator($page = 1, $limit = 10, $pageRange = 7)
    {
        $adapter = new Zend_Paginator_Adapter_DbTableSelect($this);
        $paginate = new Zend_Paginator($adapter);
        $paginate->setItemCountPerPage($limit);
        $paginate->setCurrentPageNumber($page);
        $paginate->setPageRange($pageRange);

        return $paginate;
    }

    // Эти полезные методы тоже можно реализовать.
    /**
     * @param  $field
     * @return Zend_Db_Select
     */
    public function max($field)
    {
        return $this->columns(new Zend_Db_Expr('max('.$field.') as voting '));    
    }
    //public function min($field){};
    //public function sum($field){};
    //public function exists(){};

    public function random($limit = 5) // Pseudo Random
    {
        $count = $this->count();
        $offset = ($count > $limit) ? $count - $limit : 0;
        $this->limit($limit, mt_rand(0, $offset));

        return $this->fetchAll();
    }

    /**
     * @throws InvalidArgumentException
     * @param  string $direction
     * @return bool
     */
    protected function _checkDirectionStr($direction)
    {
        if(!in_array($direction, array(Zend_Db_Select::SQL_ASC, Zend_Db_Select::SQL_DESC)))
        {
            throw new InvalidArgumentException('Sort drirection can be '.
                                               Zend_Db_Select::SQL_ASC.
                                               ' or '.
                                               Zend_Db_Select::SQL_DESC);
        }
        
        return true;
    }

    /**
     * @return Zend_Db_Select
     */
    public function count()
    {
        /*
         * TODO FIX ME count
         */
        throw new Exception('BUGGED count() implementation');
        return '0';
        return $this->columns(new Zend_Db_Expr(
            'count('.$this->getTableName().'.'.$this->getTable()->getPKey().') as COUNT'
        ))->fetchRow()->COUNT;
    }

    /**           
     * Add where part of sql statement for select by primary key value
     * 
     * @throws InvalidArgumentException
     * @param int $id
     * @return Zend_Db_Select
     */
    public function pKey($id)
    {
        if(is_nan($id) || $id < 0)
        {
            throw new InvalidArgumentException('Primary Key value must be numeric');            
        }
        $tableName = $this->getTableName();
        $primKeyName = $this->getTable()->getPKey();
        return $this->where("{$tableName}.{$primKeyName}=?", $id);
    }
}
