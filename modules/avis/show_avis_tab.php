<?php
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_request.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_security.php';
require_once "modules" . DIRECTORY_SEPARATOR . "avis" . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "avis_controler.php";

$res_id = $_REQUEST["resId"];
$coll_id = $_REQUEST["collId"];

$security = new security();
$right = $security->test_right_doc($coll_id, $res_id);

if(!$right){
    exit(_NO_RIGHT_TXT);
}

if(isset($_REQUEST['fromDetail']) && $_REQUEST['fromDetail'] == true){
    $from_detail = true;
}else{
    $from_detail = false;
}

$core_tools = new core_tools();
$core_tools->load_lang();
$core_tools->load_html();
$core_tools->load_header('', true, false);
$core_tools->load_js();

$modifAvisWorkflow = false;
if($from_detail == true){
    if ($core_tools->test_service('config_avis_workflow_in_detail', 'avis', false)) {
        $modifAvisWorkflow = true;
    }
}else{
    if ($core_tools->test_service('config_avis_workflow', 'avis', false)) {
        $modifAvisWorkflow = true;
    }
}

$frm_str .= '<div class="error" id="divError" name="divError"></div>';
$frm_str .= '<div style="text-align:center;">';
$avis = new avis_controler();
$frm_str .= $avis->getList($res_id, $coll_id, $modifAvisWorkflow, 'AVIS_CIRCUIT');

$frm_str .= '</div>';
$frm_str .= '<span class="diff_list_avis_history" style="width: 90%; cursor: pointer;" onmouseover="this.style.cursor=\'pointer\';" onclick="new Effect.toggle(\'diff_list_avis_history_div\', \'blind\', {delay:0.2});whatIsTheDivStatus(\'diff_list_avis_history_div\', \'divStatus_diff_list_avis_history_div\');return false;">';
$frm_str .= '<span id="divStatus_diff_list_avis_history_div" style="color:#1C99C5;"><i class="fa fa-plus-square-o"></i></span>';
$frm_str .= '<b>&nbsp;<small>'._DIFF_LIST_AVIS_HISTORY.'</small></b>';
$frm_str .= '</span>';
$frm_str .= '<div id="diff_list_avis_history_div" style="display:none">';

$s_id = $res_id;
$return_mode = true;
$diffListType = 'AVIS_CIRCUIT';
require_once('modules/avis/difflist_avis_history_display.php');
                                    
$frm_str .= '</div>';
echo $frm_str;