<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.

* @brief   admin_listmodel_save
* @author  dev <dev@maarch.org>
* @ingroup entities
*/

//AJAX save listmodel from admin_listmodel
require_once "modules/entities/class/class_manage_listdiff.php";
$difflist = new diffusion_list();

switch($_REQUEST['mode']) {
case 'up':
    $difflist->save_listmodel(
        $_SESSION['m_admin']['entity']['listmodel'], 
        $objectType = $_REQUEST['objectType'],
        $objectId = $_REQUEST['objectId'],
        $title = $_REQUEST['title'],
        $description = $_REQUEST['description']
    );
    $_SESSION['info'] = _ADMIN_LISTMODEL.' '._UPDATED;
    break;
case 'add':
    $difflist->save_listmodel(
        $_SESSION['m_admin']['entity']['listmodel'], 
        $objectType = $_REQUEST['objectType'],
        $objectId = $_REQUEST['objectId'],
        $title = $_REQUEST['title'],
        $description = $_REQUEST['description']
    );
    $_SESSION['info'] = _ADMIN_LISTMODEL.' '._ADDED;
    break;
    
case 'del':
    $difflist->delete_listmodel(
        $objectType = $_REQUEST['objectType'],
        $objectId = $_REQUEST['objectId']
    );
    $_SESSION['info'] = _ADMIN_LISTMODEL.' '._DELETED;
    break;
}