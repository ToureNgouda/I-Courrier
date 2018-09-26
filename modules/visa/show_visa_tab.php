<?php
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_request.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_security.php';
require_once "modules" . DIRECTORY_SEPARATOR . "visa" . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "class_modules_tools.php";

$res_id = $_REQUEST["resId"];
$coll_id = $_REQUEST["collId"];
$destination = $_REQUEST["destination"];

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

if(isset($_REQUEST['visaStep']) && $_REQUEST['visaStep'] == true){
    $visaStep = true;
}else{
    $visaStep = false;
}

$core_tools = new core_tools();
$core_tools->load_lang();
$core_tools->load_html();
$core_tools->load_header('', true, false);
$core_tools->load_js();

$modifVisaWorkflow = false;
if($from_detail == true){
    if ($core_tools->test_service('config_visa_workflow_in_detail', 'visa', false)) {
        $modifVisaWorkflow = true;
    }
}else{
    if ($core_tools->test_service('config_visa_workflow', 'visa', false)) {
        $modifVisaWorkflow = true;
    }
}
    
$frm_str .= '<div class="error" id="divError" name="divError"></div>';
$frm_str .= '<div style="text-align:center;">';
$visa = new visa();
$frm_str .= $visa->getList($res_id, $coll_id, $modifVisaWorkflow, 'VISA_CIRCUIT',$visaStep,$from_detail);

$frm_str .= '</div><br>';
/* Historique diffusion visa */
$frm_str .= '<br/>';
$frm_str .= '<br/>';
$frm_str .= '<span class="diff_list_visa_history" style="width: 90%; cursor: pointer;" onmouseover="this.style.cursor=\'pointer\';" onclick="new Effect.toggle(\'diff_list_visa_history_div\', \'blind\', {delay:0.2});whatIsTheDivStatus(\'diff_list_visa_history_div\', \'divStatus_diff_list_visa_history_div\');return false;">';
$frm_str .= '<span id="divStatus_diff_list_visa_history_div" style="color:#1C99C5;"><i class="fa fa-plus-square-o"></i></span>';
$frm_str .= '<b>&nbsp;<small>' . _DIFF_LIST_VISA_HISTORY . '</small></b>';
$frm_str .= '</span>';

$frm_str .= '<div id="diff_list_visa_history_div" style="display:none">';

$s_id = $res_id;
$return_mode = true;
$diffListType = 'VISA_CIRCUIT';
require_once('modules/entities/difflist_visa_history_display.php');

$frm_str .= '</div>';

//script
$curr_visa_wf = $visa->getWorkflow($res_id, $coll_id, 'VISA_CIRCUIT');
if (count($curr_visa_wf['visa']) == 0 && count($curr_visa_wf['sign']) == 0){
    $frm_str .= '<script>';
    $frm_str .= '$j("#modelList").val(\''.$destination.'\');$j("#modelList").change();';
    $frm_str .= '</script>';
}

echo $frm_str;