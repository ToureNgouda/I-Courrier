<?php

/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

require_once('core/class/class_core_tools.php');
$Core_Tools = new core_tools;
$Core_Tools->load_lang();

$status = 0;

if (isset($_REQUEST['typeList'])) {
    $typeList = $_REQUEST['typeList'];
} else {
    $typeList = 'entity_id';
}

if (isset($_REQUEST['showStatus'])) {
    $showStatus = true;
} else {
    $showStatus = false;
}

//VISA WORKFLOW
if ($typeList=='VISA_CIRCUIT') {
    $visaWorkflow = \SrcCore\models\DatabaseModel::select([
        'select'   => ['u.firstname', 'u.lastname', 'l.process_date', 'l.process_comment', 'l.signatory', 'l.requested_signature'],
        'table'    => ['listinstance l, users u'],
        'where'    => ['l.res_id = ?', 'l.difflist_type = ?', 'u.user_id = l.item_id'],
        'data'     => [$_REQUEST['res_id'], 'VISA_CIRCUIT'],
        'order_by' => ['l.listinstance_id asc']
        ]);

    if (!empty($visaWorkflow)) {
        $return = '<table style="width:100%;margin-top: 5px;">';
        $return .= '<tr><td class="sstit visaWorkFlowBasket">'._ADMIN_USERS.'</td>';
        $return .= '<td class="sstit visaWorkFlowBasket">'._PROCESS_DATE.'</td></tr>';
        foreach ($visaWorkflow as $key => $value) {
            $signatory = '';
            if ($value['signatory'] || ($value['requested_signature'] && empty($value['process_date']))) {
                $signatory = ' ('._SIGNATORY.')';
            } else {
                $signatory = ' ('._VISA_USER_SEARCH_MIN.')';
            }
            $nb = $key+1;
            $return .= '<tr><td style="width:50%;padding: 8px 0px 7px 10px;">'. $nb . '. ' .$value['firstname']. ' ' . $value['lastname'] . $signatory.'</td>';
            $return .= '<td style="width:50%; text-align:center;">'.functions::format_date_db($value['process_date'], true, '', true) . '</td></tr>';
        }
        $return .= '</table>';
    } else {
        $return .= '<div style="font-style:italic;text-align:center;color:#ea0000;margin:10px;">'._DIFF_LIST.' '._IS_EMPTY.'</div>';
    }
} else {
    require_once('modules/entities/class/class_manage_listdiff.php');
    $diffListObj = new diffusion_list();
    $difflist = $diffListObj->get_listinstance($_REQUEST['res_id'], false, $_SESSION['collection_id_choice'], $typeList);
        
    # Include display of list
    $roles = $diffListObj->list_difflist_roles();

    ob_start();
    require_once 'modules/entities/difflist_display.php';
    $return .= str_replace(array("\r", "\n", "\t"), array("", "", ""), ob_get_contents());
    ob_end_clean();
}

echo "{status : " . $status . ", toShow : '" . addslashes($return) . "'}";
exit();
