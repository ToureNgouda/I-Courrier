<?php
/**
* File : autocomplete_folders.php
*
* Autocompletion list on market or project
*
* @package  maarch
* @version 1
* @since 10/2005
* @license GPL v3
* @author  Claire Figueras  <dev@maarch.org>
*/
session_name('PeopleBox');
session_start();
require_once($_SESSION['pathtocoreclass']."class_functions.php");
require_once($_SESSION['pathtocoreclass']."class_db.php");
require_once($_SESSION['pathtocoreclass']."class_request.php");

$req = new request();
$req->connect();
if($_REQUEST['mode']<> 'market' && $_REQUEST['mode'] <> 'project')
{
	exit();
}

$mode = $_REQUEST['mode'];
$table = $_SESSION['tablename']['fold_folders'];
$where = '';
$select = array();
$select[$table]= array( 'folder_name', 'subject', 'folders_system_id');
if($mode == 'market')
{
	$where = " folder_level = 2 and ";

}
else
{
	$where = " folder_level = 1 and ";
}
if($_SESSION['config']['databasetype'] == "POSTGRESQL")
{
	$where .= " (folder_name ilike '%".$req->protect_string_db($_REQUEST['Input'])."%' or subject ilike '%".$req->protect_string_db($_REQUEST['Input'])."%' ) and status <> 'DEL'";
}
else
{
	$where .= " (folder_name like '%".$req->protect_string_db($_REQUEST['Input'])."%' or subject like '%".$req->protect_string_db($_REQUEST['Input'])."%' ) and status <> 'DEL'";
}
$other = 'order by subject, folder_name';

$res = $req->select($select, $where, $other, $_SESSION['config']['databasetype'], 11,false,"","","", false);

echo "<ul>\n";
for($i=0; $i< min(count($res), 10)  ;$i++)
{
	echo "<li>".$req->show_string($res[$i][0]['value']).', '.$req->show_string($res[$i][1]['value']).' ('.$res[$i][2]['value'].")</li>\n";
}
if(count($res) == 11)
{
		echo "<li>...</li>\n";
}
echo "</ul>";