<?php

/*
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 * 
 */

/*
 * @brief load_listmodel_avis_users
 * @author dev@maarch.org
 * @ingroup avis
 * 
 */
require_once 'modules/entities/class/class_manage_listdiff.php';
require_once "modules" . DIRECTORY_SEPARATOR . "avis" . DIRECTORY_SEPARATOR
        . "class" . DIRECTORY_SEPARATOR
        . "avis_controler.php";


$db = new Database();
$core = new core_tools();
$core->load_lang();
$diffList = new diffusion_list();

$objectType = $_REQUEST['objectType'];
$objectId = $_REQUEST['objectId'];
$origin = 'avis';

// Get listmodel_parameters
$contentListModel = $diffList->get_listmodel($objectType, $objectId);

if (!$contentListModel['avis']['users']) {
    $contentListModel['avis']['users'] = array();
}

$userList = $contentListModel['avis']['users'];

$userList = json_encode($userList);


echo "{\"status\" : 0, \"result\" :" . $userList . "}";
exit();
