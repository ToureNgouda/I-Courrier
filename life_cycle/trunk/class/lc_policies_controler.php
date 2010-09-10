<?php

/*
*    Copyright 2008,2009,2010 Maarch
*
*  This file is part of Maarch Framework.
*
*   Maarch Framework is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Maarch Framework is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*    along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @brief  Contains the life_cycle Object (herits of the BaseObject class)
* 
* 
* @file
* @author Luc KEULEYAN - BULL
* @date $date$
* @version $Revision$
* @ingroup life_cycle
*/


try {
	require_once("modules/life_cycle/class/ObjectControlerIF.php");
	require_once("modules/life_cycle/class/ClassifiedObjectControlerAbstract.php");
	require_once("modules/life_cycle/class/lc_policies.php");
} catch (Exception $e){
	echo $e->getMessage().' // ';
}

define ("_DEBUG", false);
define ("_ADVANCED_DEBUG",false);

/**
 * Class for controling lc_policies objects from database
 * data, and vice-versa.
 * @author boulio
 *
 */
class lc_policies_controler extends ClassifiedObjectControler implements ObjectControlerIF {
	
	/**
	 * Save given object in database: 
	 * - make an update if object already exists,
	 * - make an insert if new object.
	 * Return updated object.
	 * @param lc_policies $lc_policies
	 * @return boolean
	 */
	public function save($lc_policies){
		if(self::policiesExists($lc_policies->lc_policies_id)){
			// Update existing lc_policies
			return self::update($lc_policies);
		} else {
			// Insert new lc_policies
			return self::insert($lc_policies);
		}
	}

///////////////////////////////////////////////////////   INSERT BLOCK	
	/**
	 * Add given lc_policies to database.
	 * @param lc_policies $lc_policies
	 */
	private function insert($lc_policies){
		
		// Inserting object
		$result = self::advanced_insert($lc_policies);
		return $result;
	}

///////////////////////////////////////////////////////   UPDATE BLOCK
	/**
	 * Update given lc_policies informations in database.
	 * @param lc_policies $lc_policies
	 */
	private function update($lc_policies){
		// Updating automatised values of given object
		
		// Update given lc_policies in database
		$result = self::advanced_update($lc_policies);
	}

///////////////////////////////////////////////    GET BLOCK
	
	/**
	 * Get lc_policies with given id.
	 * Can return null if no corresponding object.
	 * @param $id Id of lc_policies to get
	 * @return lc_policies 
	 */
	public function get($id) {
		return self::advanced_get($id,_LC_POLICIES_TABLE_NAME);
	}

///////////////////////////////////////////////////// DELETE BLOCK
	/**
	 * Delete given lc_policies from database.
	 * @param lc_policies $lc_policies
	 */
	public function delete($lc_policies){
		// Deletion of given lc_policies
		$result = self::advanced_delete($lc_policies);
		return $result;
	}

///////////////////////////////////////////////////// DISABLE BLOCK
	/**
	 * Disable given lc_policies from database.
	 * @param lc_policies $lc_policies
	 */
	public function disable($lc_policies){
		// Disable of given lc_policies
		$result = self::advanced_disable($lc_policies);
		return $result;
	}

///////////////////////////////////////////////////// ENABLE BLOCK
	/**
	 * Disable given lc_policies from database.
	 * @param lc_policies $lc_policies
	 */
	public function enable($lc_policies){
		// Disable of given lc_policies
		$result = self::advanced_enable($lc_policies);
		return $result;
	}

//////////////////////////////////////////////   OTHER PRIVATE BLOCK
	public function policiesExists($lc_policies_id){
		if(!isset($lc_policies_id) || empty($lc_policies_id))
			return false;
		self::$db=new dbquery();
		self::$db->connect();
		
		//LKE = BULL ===== SPEC FONC : ==== Cycles de vie : lc_policies (ID1)
		// Ajout du contr�le pour v�rifier l'existence de la combinaison "lc_policies_id"	
		$query = "select lc_policies_id from "._LC_POLICIES_TABLE_NAME." where lc_policies_id = '".$lc_policies_id."'";
					
		try{
			if($_ENV['DEBUG']){echo $query.' // ';}
			self::$db->query($query);
		} catch (Exception $e){
			echo _UNKNOWN._DOCSERVER." ".$lc_policies_id.' // ';
		}
		
		if(self::$db->nb_result() > 0){
			self::$db->disconnect();
			return true;
		}
		self::$db->disconnect();
		return false;
	}
	
	public function getAllId($can_be_disabled = false){
		self::$db=new dbquery();
		self::$db->connect();
		$query = "select lc_policies_id from "._LC_POLICIES_TABLE_NAME." ";
		if(!$can_be_disabled)
			$query .= " where enabled = 'Y'";
		try{
			if($_ENV['DEBUG'])
				echo $query.' // ';
			self::$db->query($query);
		} catch (Exception $e){
			echo _NO_DOCSERVER_LOCATION.' // ';
		}
		if(self::$db->nb_result() > 0){
			$result = array();
			$cptId = 0;
			while($queryResult = self::$db->fetch_object()){
				$result[$cptId] = $queryResult->lc_policies_id;
				$cptId++;
			}
			self::$db->disconnect();
			return $result;
		} else {
			self::$db->disconnect();
			return null;
		}
	}
}

?>
