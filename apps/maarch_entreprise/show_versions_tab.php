<?php
require_once 'core'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class_request.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_security.php';

$res_id = $_REQUEST["resId"];
$coll_id = $_REQUEST["collId"];
$objectTable = $_REQUEST["objectTable"];

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

//test service add new version
$addNewVersion = false;
if ($core_tools->test_service('add_new_version', 'apps', false)) {
    $addNewVersion = true;
}
$viewVersions = false;
if ($core_tools->test_service('add_new_version', 'apps', false)) {
    $viewVersions = true;
}
 
$frm_str .= '<div class="error" id="divError" name="divError"></div>';
$frm_str .= '<div style="text-align:center;">';
$frm_str .= '<a href="';
$frm_str .= $_SESSION['config']['businessappurl'];
$frm_str .= 'index.php?display=true&dir=indexing_searching&page=view_resource_controler&original&id=';
$frm_str .= $res_id;
$frm_str .= '" target="_blank">';
$frm_str .= '<i class="fa fa-download fa-2x" title="' . _VIEW_ORIGINAL . '"></i>&nbsp;';
$frm_str .= _VIEW_ORIGINAL . ' | ';
$frm_str .= '</a>';
if ($addNewVersion) {
    $_SESSION['cm']['objectTable'] = $objectTable;
    $frm_str .= '<div id="createVersion" style="display: inline;"></div>';
}
$frm_str .= '<div id="loadVersions"></div>';
$frm_str .= '<script language="javascript">';
$frm_str .= 'showDiv("loadVersions", "nbVersions", "createVersion", "';
$frm_str .= $_SESSION['config']['businessappurl'];
$frm_str .= 'index.php?display=false&module=content_management&page=list_versions")';
$frm_str .= '</script>';
$frm_str .= '</div><br>';

echo $frm_str;