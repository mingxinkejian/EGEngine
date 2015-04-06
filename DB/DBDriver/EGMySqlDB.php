<?php

namespace DB\DBDriver;

use DB\EGADB;
class EGMySqlDB extends EGADB{
	/* (non-PHPdoc)
     * @see \DB\EGIDB::lazyConnection()
     */
    protected function lazyConnection()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::nowConnection()
     */
    protected function nowConnection()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::connection()
     */
    public function connection()
    {
        // TODO Auto-generated method stub
        if ($this->_config['connType']=='lazy'){
            $this->lazyConnection();
        }else{
            $this->connection();
        }
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::selectDB()
     */
    public function selectDB($dbName)
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::freeResult()
     */
    public function freeResult()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::close()
     */
    public function close()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::getDBError()
     */
    public function getDBError()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::getLastSql()
     */
    public function getLastSql()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::getLastId()
     */
    public function getLastId()
    {
        // TODO Auto-generated method stub
        
    }

	
	
}
