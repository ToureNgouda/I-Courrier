<?php
/*
*   Copyright 2008-2015 Maarch
*
*   This file is part of Maarch Framework.
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
*   along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @brief   Action : proceed visa workflow
*
* Permet de récupérer l'état de visa en cours et de transmettre à la personne suivante devant viser
*
* @file
* @author Nicolas Couture <couture@docimsol.com>
* @date $date$
* @version $Revision$
* @ingroup apps
*/

/**
* $confirm  bool true
*/
$confirm = true;

/**
* $etapes  array Contains only one etap, the status modification
*/

// $error_visa_workflow = false;

// $db = new Database();
// $stmt = $db->query("SELECT listinstance_id from listinstance WHERE res_id= ? and coll_id = ? and difflist_type = ? and process_date ISNULL", array($_POST['values'], 'letterbox_coll', 'VISA_CIRCUIT'));

// if ($stmt->rowCount() < 2) {
//     $error_visa_workflow = true;
// }

$etapes = array('empty_error');
 
require_once "modules/visa/class/class_modules_tools.php";

function manage_empty_error($arr_id, $history, $id_action, $label_action, $status)
{
    $db = new Database();
    $result = '';

    if (!empty($_SESSION['stockCheckbox'])) {
        $arr_id = $_SESSION['stockCheckbox'];
    }

    for ($i=0; $i<count($arr_id);$i++) {
        $_SESSION['action_error'] = '';
        $coll_id = $_SESSION['current_basket']['coll_id'];
        $res_id = $arr_id[$i];
        require_once("core/class/class_security.php");
        $sec = new security();
        require_once("core/class/class_history.php");
        $history = new history();
        $table = $sec->retrieve_table_from_coll($coll_id);
        $circuit_visa = new visa();
        $sequence = $circuit_visa->getCurrentStep($res_id, $coll_id, 'VISA_CIRCUIT');
        $stepDetails = array();
        $stepDetails = $circuit_visa->getStepDetails($res_id, $coll_id, 'VISA_CIRCUIT', $sequence);
        $message = [];

        //enables to process the visa if i am not the item_id
        if ($stepDetails['item_id'] <> $_SESSION['user']['UserId']) {
            $stmt = $db->query("UPDATE listinstance SET process_date = CURRENT_TIMESTAMP "
                . " WHERE listinstance_id = ? AND item_mode = ? AND res_id = ? AND item_id = ? AND difflist_type = ?"
                , array($stepDetails['listinstance_id'], $stepDetails['item_mode'], $res_id, $stepDetails['item_id'], 'VISA_CIRCUIT'));

            $stmt = $db->query("SELECT firstname, lastname, user_id FROM users WHERE user_id IN (?)", array([$_SESSION['user']['UserId'], $stepDetails['item_id']]));
            foreach ($stmt as $value) {
                if($value['user_id'] == $_SESSION['user']['UserId']){
                    $user1 = $value['firstname'] . ' ' . $value['lastname'];
                } else {
                    $user2 = $value['firstname'] . ' ' . $value['lastname'];
                }
            }

            $message[] = " " ._VISA_BY . " " . $user1 . " " . _INSTEAD_OF . " " . $user2;
        } else {
            $stmt = $db->query("UPDATE listinstance SET process_date = CURRENT_TIMESTAMP "
                . " WHERE listinstance_id = ? AND item_mode = ? AND res_id = ? AND item_id = ? AND difflist_type = ?"
                , array($stepDetails['listinstance_id'], $stepDetails['item_mode'], $res_id, $_SESSION['user']['UserId'], 'VISA_CIRCUIT'));
            $message[] = "";
        }

        if ($circuit_visa->getCurrentStep($res_id, $coll_id, 'VISA_CIRCUIT') == $circuit_visa->nbVisa($res_id, $coll_id)) {
            $mailStatus = 'ESIG';
            $db->query("UPDATE res_letterbox SET status = ? WHERE res_id = ? ", [$mailStatus, $res_id]);
        }
        $result .= $arr_id[$i] . '#';
    }

    return array('result' => $result, 'history_msg' => $message);
}
