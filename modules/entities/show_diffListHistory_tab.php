<?php
$s_id = $_REQUEST["resId"];
$coll_id = $_REQUEST["collId"];


require_once 'core'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_request.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_security.php';

$security = new security();
$right = $security->test_right_doc($coll_id, $s_id);

if(!$right){
    exit(_NO_RIGHT_TXT);
}

$core_tools = new core_tools();
$core_tools->load_lang();
$core_tools->load_html();
$core_tools->load_header('', true, false);
$core_tools->load_js();
    
$difflist = $_SESSION['process']['diff_list'];

$frm_str .= '<div id="diff_list_history_div">';

$return_mode = true;
$diffListType = 'entity_id';
require_once('modules/entities/difflist_history_display.php');


echo $frm_str;