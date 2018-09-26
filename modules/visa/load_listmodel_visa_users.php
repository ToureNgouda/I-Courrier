<?php
/**
* File : load_listmodel_visa_users.php
*
* Script called by an ajax object to retrieve users in visa circuit
*
*
* @package  maarch
* @version 1
* @since 10/2005
* @license GPL v3
* @author  Alex ORLUC <dev@maarch.org>
*/
require_once 'modules/entities/class/class_manage_listdiff.php';
require_once "modules" . DIRECTORY_SEPARATOR . "visa" . DIRECTORY_SEPARATOR
    . "class" . DIRECTORY_SEPARATOR
    . "class_modules_tools.php";


$db = new Database();
$core = new core_tools();
$core->load_lang();
$diffList = new diffusion_list();

$objectType = $_REQUEST['objectType'];
$objectId = $_REQUEST['objectId'];
$origin = 'visa';

// Get listmodel_parameters
$contentListModel = $diffList->get_listmodel($objectType, $objectId);

if(!$contentListModel['visa']['users']){
    $contentListModel['visa']['users'] = array();
}

if(!$contentListModel['sign']['users']){
    $contentListModel['sign']['users'] = array();
}

$userList = array_merge($contentListModel['visa']['users'],$contentListModel['sign']['users']);

$userList = json_encode($userList);


echo "{\"status\" : 0, \"result\" :" . $userList . "}";
exit();
