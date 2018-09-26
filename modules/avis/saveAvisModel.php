<?php

/*
 * Copyright Maarch since 2008 under licence GPLv3.
 * See LICENCE.txt file at the root folder for more details.
 * This file is part of Maarch software.
 * 
 */

/*
 * @brief saveAvisModel
 * @author dev@maarch.org
 * @ingroup avis
 * 
 */
require_once "modules" . DIRECTORY_SEPARATOR . "avis" . DIRECTORY_SEPARATOR
        . "class" . DIRECTORY_SEPARATOR
        . "avis_controler.php";


$title = $_REQUEST['title'];
$id_list = 'AVIS_CIRCUIT_' . strtoupper(base_convert(date('U'), 10, 36));


$userList = json_decode($_REQUEST['userList']);

$avis = new avis_controler();
$_SESSION['avis_wf']['diff_list']['avis']['users'] = array();
$_SESSION['avis_wf']['diff_list']['sign']['users'] = array();

$i = 0;
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
$avis->saveModelWorkflow($id_list, $_SESSION['avis_wf']['diff_list'], 'AVIS_CIRCUIT', $title);

echo "{\"status\" : 0}";

exit();
?>