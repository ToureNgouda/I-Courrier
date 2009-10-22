<?php
/*
*
*    Copyright 2008,2009 Maarch
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
*
*	@author  C�dric Ndoumba  <dev@maarch.org>
*/

$admin = new core_tools();
$admin->test_admin('manage_entities', 'entities');
$_SESSION['m_admin']= array();
/****************Management of the location bar  ************/
$init = false;
if($_REQUEST['reinit'] == "true")
{
	$init = true;
}
$level = "";
if($_REQUEST['level'] == 2 || $_REQUEST['level'] == 3 || $_REQUEST['level'] == 4 || $_REQUEST['level'] == 1)
{
	$level = $_REQUEST['level'];
}
$page_path = $_SESSION['config']['businessappurl'].'index.php?page=manage_entities&module=entities';
$page_label = _ENTITIES_LIST;
$page_id = "manage_entities";
$admin->manage_location_bar($page_path, $page_label, $page_id, $init, $level);
/***********************************************************/
require_once($_SESSION['pathtocoreclass']."class_request.php");
require_once($_SESSION['pathtocoreclass']."class_functions.php");
require_once($_SESSION['config']['businessapppath']."class".DIRECTORY_SEPARATOR."class_list_show.php");
require_once($_SESSION['pathtomodules'].'entities'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_manage_entities.php');
$ent = new entity();
$func = new functions();
$request = new request;



$what = '';
$where = '';

/*
$order = ' order by entity_type, entity_label';

if(isset($_REQUEST['order_field']) && !empty($_REQUEST['order_field']))
{
	$order = ' order by '.$_REQUEST['order_field'].' '.$_REQUEST['order'];

}*/

if(isset($_REQUEST['what']) && !empty($_REQUEST['what']))
{
	$what = $func->protect_string_db($_REQUEST['what']);
	if($_SESSION['config']['databasetype'] == 'POSTGRESQL')
	{
		$where = "(entity_label ilike '".strtolower($what)."%' or entity_label ilike '".strtoupper($what)."%') ";
	}
	else
	{
		$where = "(entity_label like '".strtolower($what)."%' or entity_label like '".strtoupper($what)."%') ";
	}
}

if($_SESSION['user']['UserId'] != 'superadmin')
{
	$my_tab_entities_id = $ent->get_all_entities_id_user($_SESSION['user']['entities']);
	if (count($my_tab_entities_id)>0)
	{
		if(isset($_REQUEST['what']) && !empty($_REQUEST['what']))
		{
			$where.= ' and ';
		}

		//we need all entities that are managed by connected user
		$where.= '('.$_SESSION['tablename']['ent_entities'].'.entity_id in ('.join(',', $my_tab_entities_id).'))';
	}
}

$list = new list_show();
$order = 'asc';
if(isset($_REQUEST['order']) && !empty($_REQUEST['order']))
{
	$order = trim($_REQUEST['order']);
}
$field = 'entity_label';
if(isset($_REQUEST['order_field']) && !empty($_REQUEST['order_field']))
{
	$field = trim($_REQUEST['order_field']);
}

$orderstr = $list->define_order($order, $field);

$select[$_SESSION['tablename']['ent_entities']] = array();
array_push($select[$_SESSION['tablename']['ent_entities']], "entity_id", "entity_label", "entity_type", "enabled");

$tab = $request->select($select, $where, $orderstr, $_SESSION['config']['databasetype']);
//$request->show();

for ($i=0;$i<count($tab);$i++)
{
	for ($j=0;$j<count($tab[$i]);$j++)
	{
		foreach(array_keys($tab[$i][$j]) as $value)
		{
			if($tab[$i][$j][$value]=="entity_id")
			{
				$tab[$i][$j]["entity_id"]=$tab[$i][$j]['value'];
				$tab[$i][$j]["label"]= _ID;
				$tab[$i][$j]["size"]="18";
				$tab[$i][$j]["label_align"]="left";
				$tab[$i][$j]["align"]="left";
				$tab[$i][$j]["order"]=$tab[$i][$j][$value];
				$tab[$i][$j]["valign"]="bottom";
				$tab[$i][$j]["show"]=true;
			}

			if($tab[$i][$j][$value]=="entity_label")
			{
				$tab[$i][$j]['value']=$request->show_string($tab[$i][$j]['value']);
				$tab[$i][$j]["entity_label"]=$tab[$i][$j]['value'];
				$tab[$i][$j]["label"]=_DESC;
				$tab[$i][$j]["size"]="25";
				$tab[$i][$j]["label_align"]="left";
				$tab[$i][$j]["align"]="left";
				$tab[$i][$j]["order"]=$tab[$i][$j][$value];
				$tab[$i][$j]["valign"]="bottom";
				$tab[$i][$j]["show"]=true;
			}

			if($tab[$i][$j][$value]=="entity_type")
			{
				$tab[$i][$j]['value']=$request->show_string($tab[$i][$j]['value']);
				$tab[$i][$j]["entity_type"]=$tab[$i][$j]['value'];
				$tab[$i][$j]["label"]=_TYPE;
				$tab[$i][$j]["size"]="15";
				$tab[$i][$j]["label_align"]="left";
				$tab[$i][$j]["align"]="left";
				$tab[$i][$j]["order"]=$tab[$i][$j][$value];
				$tab[$i][$j]["valign"]="bottom";
				$tab[$i][$j]["show"]=true;
			}

			if($tab[$i][$j][$value]=="enabled")
			{
				$tab[$i][$j]["enabled"]=$tab[$i][$j]['value'];
				$tab[$i][$j]["label"]=_STATUS;
				$tab[$i][$j]["size"]="10";
				$tab[$i][$j]["label_align"]="center";
				$tab[$i][$j]["align"]="center";
				$tab[$i][$j]["valign"]="bottom";
				$tab[$i][$j]["show"]=true;
			}

		}
	}
}

$page_name = "manage_entities";
$page_name_up = "entity_up";
$page_name_del = "entity_del";
$page_name_val = "entity_allow";
$page_name_ban = "entity_ban";
$page_name_add = "entity_add";
$label_add = _ENTITY_ADDITION;
$_SESSION['m_admin']['init'] = true;

$title = _ENTITIES_LIST." : ".$i." "._ENTITIES;
$autoCompletionArray = array();
$autoCompletionArray["list_script_url"] = $_SESSION['urltomodules'].'entities/entities_list_by_label.php';
$autoCompletionArray["number_to_begin"] = 1;

$list->admin_list($tab, $i, $title, 'entity_id','manage_entities','entities', 'entity_id', true, $page_name_up, $page_name_val, $page_name_ban, $page_name_del, $page_name_add, $label_add, false, false, _ALL_ENTITIES, _ENTITY, $_SESSION['urltomodules'].'entities/img/manage_entities_b.gif', true, true, false, true, "", true, $autoCompletionArray);
?>
