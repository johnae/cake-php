<?php
/* SVN FILE: $Id$ */

/**
 * AdoDB layer for DBO.
 * 
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c) 2005, Cake Software Foundation, Inc. 
 *                     1785 E. Sahara Avenue, Suite 490-204
 *                     Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource 
 * @copyright    Copyright (c) 2005, Cake Software Foundation, Inc.
 * @link         http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package      cake
 * @subpackage   cake.cake.libs.model.dbo
 * @since        CakePHP v 0.2.9
 * @version      $Revision$
 * @modifiedby   $LastChangedBy$
 * @lastmodified $Date$
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Include AdoDB files.
 */
require_once(VENDORS.'adodb/adodb.inc.php');

/**
 * AdoDB DBO implementation.
 * 
 * Database abstraction implementation for the AdoDB library.
 *
 * @package    cake
 * @subpackage cake.cake.libs.model.dbo
 * @since      CakePHP v 0.2.9
 */
class DBO_AdoDB extends DBO 
{
   
/**
 * ADOConnection object with which we connect.
 *
 * @var ADOConnection The connection object.
 * @access private
 */
   var $_adodb = null;

/**
 * Connects to the database using options in the given configuration array.
 *
 * @param array $config Configuration array for connecting
 */
   function connect ($config) 
   {
      if ($this->config = $config)
      {
         if (isset($config['driver']))
         {
            $this->_adodb = NewADOConnection($config['driver']);

            $adodb =& $this->_adodb;
            $this->connected = $adodb->Connect($config['host'], $config['login'], $config['password'], $config['database']);
         }
      }

      if(!$this->connected){
        // die('Could not connect to DB.');
      }
   }

/**
 * Disconnects from database.
 *
 * @return boolean True if the database could be disconnected, else false
 */
   function disconnect () 
   {
      return $this->_adodb->close();
   }

/**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier
 */
   function execute ($sql) 
   {
      return $this->_adodb->execute($sql);
   }

/**
 * Returns a row from given resultset as an array .
 *
 * @return array The fetched row as an array
 */
   function fetchRow () 
   {
      return $this->_result->FetchRow();
   }

/**
 * Returns an array of tables in the database. If there are no tables, an error is raised and the application exits.
 *
 * @return array Array of tablenames in the database
 */
   function tablesList () 
   {
      $tables = $this->_adodb->MetaTables('TABLES');

      if (!sizeof($tables)>0) {
         trigger_error(ERROR_NO_TABLE_LIST, E_USER_NOTICE);
         exit;
      }
      return $tables;
   }

/**
 * Returns an array of the fields in given table name.
 *
 * @param string $tableName Name of database table to inspect
 * @return array Fields in table. Keys are name and type
 */
   function fields ($tableName)
   {
      $data = $this->_adodb->MetaColumns($tableName);
      $fields = false;

      foreach ($data as $item)
         $fields[] = array('name'=>$item->name, 'type'=>$item->type);

      return $fields;
   }

/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @return string Quoted and escaped
 *
 * @todo To be implemented.
 */
   function prepareValue ($data)      
   {
      return $this->_adodb->Quote($data);
   }

/**
 * Returns a formatted error message from previous database operation.
 *
 * @return string Error message
 */
   function lastError () 
   {
      return $this->_adodb->ErrorMsg();
   }

/**
 * Returns number of affected rows in previous database operation, or false if no previous operation exists.
 *
 * @return int Number of affected rows
 */
   function lastAffected ()
   {
      return $this->_adodb->Affected_Rows(); 
   }

/**
 * Returns number of rows in previous resultset, or false if no previous resultset exists.
 *
 * @return int Number of rows in resultset
 */
   function lastNumRows () 
   {
       return $this->_result? $this->_result->RecordCount(): false;
   }

/**
 * To-be-implemented. Returns the ID generated from the previous INSERT operation.
 *
 * @return int 
 *
 * @todo To be implemented.
 */
   function lastInsertId ()      { die('Please implement DBO::lastInsertId() first.'); }

/**
 * Returns a LIMIT statement in the correct format for the particular database.
 *
 * @param int $limit Limit of results returned
 * @param int $offset Offset from which to start results
 * @return string SQL limit/offset statement
 * @todo Please change output string to whatever select your database accepts. adodb doesn't allow us to get the correct limit string out of it.
 */
   function selectLimit ($limit, $offset=null)
   {
      return " LIMIT {$limit}".($offset? "{$offset}": null);
      // please change to whatever select your database accepts
      // adodb doesn't allow us to get the correct limit string out of it
   }

}

?>