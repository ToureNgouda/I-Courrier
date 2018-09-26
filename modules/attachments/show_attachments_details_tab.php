<?php
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_request.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_security.php';


$res_id = $_REQUEST["resId"];
$coll_id = $_REQUEST["collId"];
$from_detail = $_REQUEST["fromDetail"];
$extraParam ='';

$security = new security();
$right = $security->test_right_doc($coll_id, $res_id);

if(!$right){
    exit(_NO_RIGHT_TXT);
}

if(isset($_REQUEST['attach_type_exclude'])){
    $extraParam = '&attach_type_exclude='.$_REQUEST['attach_type_exclude'];
}else if(isset($_REQUEST['attach_type'])){
    $extraParam = '&attach_type='.$_REQUEST['attach_type'];
}

$core_tools = new core_tools();
$core_tools->load_lang();
$core_tools->load_html();
$core_tools->load_header('', true, false);
$core_tools->load_js();


$frm_str .= '<div class="ref-unit">';
$frm_str .= '<center>';
if ($core_tools->is_module_loaded('templates') && ($core_tools->test_service('edit_attachments_from_detail', 'attachments', false))) {

    $frm_str .= '<input type="button" name="attach" id="attach" class="button" value="'. _CREATE_PJ.'"
        onclick="showAttachmentsForm(\''. $_SESSION['config']['businessappurl']
        . 'index.php?display=true&module=attachments&page=attachments_content&fromDetail=create\',\'98%\',\'auto\')" />';
}
$frm_str .= '</center><iframe name="list_attach" id="list_attach" src="'.$_SESSION['config']['businessappurl'] . 'index.php?display=true&module=attachments&page=frame_list_attachments&view_only=true&load&fromDetail='.$from_detail.$extraParam.'" '
        . 'frameborder="0" width="100%" height="550px"></iframe>';
$frm_str .= '</div>';

echo $frm_str;