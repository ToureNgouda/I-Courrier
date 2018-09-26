<?php

require_once 'core'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_request.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_security.php';
require_once "modules" . DIRECTORY_SEPARATOR . "visa" . DIRECTORY_SEPARATOR
    . "class" . DIRECTORY_SEPARATOR
    . "class_modules_tools.php";

$res_id = $_REQUEST["resId"];
$coll_id = $_REQUEST["collId"];
$table = $_REQUEST["table"];

$security = new security();
$right = $security->test_right_doc($coll_id, $res_id);

if(!$right){
    exit(_NO_RIGHT_TXT);
}

$core_tools = new core_tools();
$core_tools->load_lang();
$core_tools->load_html();
$core_tools->load_header('', true, false);
$core_tools->load_js();
    
$print_folder = new visa();
$frm_str .= $print_folder->showPrintFolder($coll_id, $table, $res_id);

echo $frm_str;