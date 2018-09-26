<?php

/*
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 * 
 */

/*
 * @brief updateAvisWF
 * @author dev@maarch.org
 * @ingroup avis
 * 
 */
require_once "modules" . DIRECTORY_SEPARATOR . "avis" . DIRECTORY_SEPARATOR
        . "class" . DIRECTORY_SEPARATOR
        . "avis_controler.php";


$res_id = $_REQUEST['resId'];
$coll_id = 'letterbox_coll';

$userList = json_decode($_REQUEST['userList']);

$avis = new avis_controler();
$_SESSION['avis_wf']['diff_list']['avis']['users'] = array();

$i = 0;
if ($userList) {
    foreach ($userList as $user) {

        array_push(
                $_SESSION['avis_wf']['diff_list']['avis']['users'], array(
            'user_id' => $user->userId,
            'process_comment' => $user->userConsigne,
            'process_date' => $user->userAvisDate,
            'viewed' => 0,
            'visible' => 'Y',
            'difflist_type' => 'AVIS_CIRCUIT'
                )
        );
    }
}

$avis->saveWorkflow($res_id, $coll_id, $_SESSION['avis_wf']['diff_list'], 'AVIS_CIRCUIT');

//LOAD TOOLBAR BADGE
$toolbarBagde_script = $_SESSION['config']['businessappurl'] . 'index.php?display=true&module=avis&page=load_toolbar_avis&origin=parent&resId=' . $res_id . '&collId=' . $coll_id;
$js = 'loadToolbarBadge(\'avis_tab\',\'' . $toolbarBagde_script . '\');';


echo "{\"status\" : 0, \"exec_js\" : \"" . $js . "\"}";
exit();
?>