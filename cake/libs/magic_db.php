<?php 
/* SVN FILE: $Id: magic_db.php 5444 2007-07-19 13:38:26Z the_undefined $ */
/**
 * MagicDb parser and file analyzer
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link			http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs
 * @since			CakePHP(tm) v 1.2.0
 * @version			$Revision: 5444 $
 * @modifiedby		$LastChangedBy: the_undefined $
 * @lastmodified	$Date: 2007-07-19 15:38:26 +0200 (Do, 19 Jul 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
if (!class_exists('Object')) {
	 uses ('object');
}

if (!class_exists('File')) {
	 uses ('file');
}

/**
 * A class to parse and use the MagicDb for file type analysis
 *
 * @package		cake.tests
 * @subpackage	cake.tests.cases.libs
 */
class MagicDb extends Object {
/**
 * Holds the parsed MagicDb for this class instance
 *
 * @var array
 **/
	var $db = array();

/**
 * Reads a MagicDb from various formats
 *
 * @var $magicDb mixed Can be an array containing the db, a magic db as a string, or a filename pointing to a magic db in .db or magic.db.php format
 * @return mixed
 * @author Felix
 **/
	function read($magicDb = null) {
		if (!is_string($magicDb) && !is_array($magicDb)) {
			return false;
		}
		if (is_array($magicDb) || strpos($magicDb, '# FILE_ID DB') === 0) {
			$data = $magicDb;
		} else {
			$File =& new File($magicDb);
			if (!$File->exists()) {
				return false;
			}
			if ($File->ext() == 'php') {
				include($File->pwd());
				$data = $magicDb;
			} else {
				// @TODO: Needs test coverage
				$data = $File->read();
			}
		}
		
		$magicDb = $this->toArray($data);
		if (!$this->validates($magicDb)) {
			return false;
		}
		return !!($this->db = $magicDb);
	}

/**
 * Parses a MagicDb $data string into an array or returns the current MagicDb instance as an array
 *
 * @param string $data
 * @return array
 * @access public
 */
	function toArray($data = null) {
		if (is_array($data)) {
			return $data;
		}
		if (empty($data)) {
			return $this->db;
		}
		
		if (strpos($data, '# FILE_ID DB') !== 0) {
			return array();
		}

		$lines = explode("\r\n", $data);
		$db = array();
		
		$validHeader = count($lines > 3)
					&& preg_match('/^# Date:([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $lines[1], $date)
					&& preg_match('/^# Source:(.+)$/', $lines[2], $source)
					&& strlen($lines[3]) == 0;
		if (!$validHeader) {
			return $db;
		}
		
		$db = array('header' => array('Date' => $date[1], 'Source' => $source[1]), 'database' => array());
		$lines = array_splice($lines, 3);

		$format = array();
		while (!empty($lines)) {
			$line = array_shift($lines);
			if (strpos($line, '#') === 0 || empty($line)) {
				continue;
			}

			$columns = explode("\t", $line);
			if (in_array($columns[0]{0}, array('&', '>'))) {
				$format[] = $columns;
			} else {
				$db['database'][] = $format;
				$format = array();
			}
		}
	}

/**
 * Returns true if the MagicDb instance or the passed $magicDb is valid
 *
 * @param mixed $magicDb
 * @return boolean
 * @access public
 */
	function validates($magicDb = null) {
		if ($magicDb !== null && !is_array($magicDb)) {
			$magicDb = $this->toArray($magicDb);
		}
		
		return isset($magicDb['header'], $magicDb['database']) && is_array($magicDb['header']) && is_array($magicDb['database']);
	}

/**
 * Analyzes a given $file using the currently loaded MagicDb information based on the desired $options
 *
 * @param string $file
 * @param array $options
 * @return mixed
 * @access public
 */
	function analyze($file, $options = array()) {
		
	}
}

?>