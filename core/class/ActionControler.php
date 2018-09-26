<?php

/*
*    Copyright 2008-2015 Maarch
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
* @brief  Contains the controler of the Action Object (create, save, modify, etc...)
*
*
* @file
* @author Claire Figueras <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup core
*/

// Loads the required class
try {
	require_once('core/class/class_db_pdo.php');
	require_once('core/class/Action.php');
	require_once('core/core_tables.php');
 // require_once('core/class/ObjectControlerIF.php');
    require_once('core/class/ObjectControlerAbstract.php');
    require_once('core/class/class_history.php');
} catch (Exception $e) {
	functions::xecho($e->getMessage()) . ' // ';
}

/**
* @brief  Controler of the Action Object
*
*<ul>
*  <li>Get an action object from an id</li>
*  <li>Save in the database an action</li>
*  <li>Manage the operation on the action related tables in the database (insert, select, update, delete)</li>
*</ul>
* @ingroup core
*/
class ActionControler
{
	/**
	* Database object used to connnect to the database
    */
	private static $db;

	/**
	* Actions table
    */
	public static $actions_table ;
	
	/**
	* Actions_groupbaskets_table table
    */
	public static $actions_groupbaskets_table ;

	/**
	* Opens a database connexion and values the tables variables
	*/
	public static function connect()
	{
		$db = new Database();
		
		self::$actions_table = $_SESSION['tablename']['actions'];
		self::$actions_groupbaskets_table = $_SESSION['tablename']['bask_actions_groupbaskets'];

		self::$db=$db;
	}

	/**
	* Returns an Action array of Object based on all action
	*
	* @return Action array of objects with properties from the database or null
	*/
	public function getAllActions()
	{
		self::connect();
		$query = "select * from ".self::$actions_table;

		$stmt = self::$db->query($query);

		if($stmt->rowCount() > 0)
		{
			$actions_list = array();
			while($queryResult=$stmt->fetchObject()){
				$action = new Action();
				foreach($queryResult as $key => $value){
					$action->{$key}=$value;
				}
				array_push($actions_list, $action);
			}
			return $actions_list;
		}
		else
		{
			return null;
		}
	}

    /**
	* Returns an Categories array of categories linked to an action
	*
	* @return categories array 
	*/
	public static function getAllCategoriesLinkedToAction($actionId)
	{
		self::connect();
		$query = "select category_id from actions_categories where action_id = ?";

		$stmt = self::$db->query($query, array($actionId));

		if ($stmt->rowCount() > 0) {
			$categories_list = array();
			while($queryResult=$stmt->fetchObject()){
				array_push($categories_list, $queryResult->category_id);
			}
			return $categories_list;
		} else {
			return null;
		}
	}

}
