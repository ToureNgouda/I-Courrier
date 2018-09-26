<?php
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_request.php';
require_once 'core' . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class_security.php';
require 'modules/templates/class/templates_controler.php';


$res_id = $_REQUEST["resId"];
$coll_id = $_REQUEST["collId"];

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

$templatesControler = new templates_controler();
$templates = array();
$templates = $templatesControler->getAllTemplatesForProcess($data['destination']['value']);
$db = new Database;
$stmt = $db->query("SELECT res_id FROM " . $_SESSION['tablename']['attach_res_attachments']
        . " WHERE (status = 'A_TRA' or status = 'TRA') and attachment_type <> 'converted_pdf' and attachment_type <> 'print_folder' and res_id_master = ? and coll_id = ?", array($res_id, $coll_id));
//$req->show();
$nb_attach = 0;
if ($stmt->rowCount() > 0) {
    $nb_attach = $stmt->rowCount();
}
$frm_str .= '<div class="ref-unit">';
$frm_str .= '<center>';
if ($core_tools->is_module_loaded('templates')) {

    $frm_str .= '<input type="button" name="attach" id="attach" class="button" value="'
            . _CREATE_PJ
            . '" onclick="showAttachmentsForm(\'' . $_SESSION['config']['businessappurl']
            . 'index.php?display=true&module=attachments&page=attachments_content\')" />';
}
$frm_str .= '</center><iframe name="list_attach" id="list_attach" src="'
        . $_SESSION['config']['businessappurl']
        . 'index.php?display=true&module=attachments&page=frame_list_attachments&load&attach_type_exclude=converted_pdf,print_folder" '
        . 'frameborder="0" width="100%" height="550px"></iframe>';
$frm_str .= '</div>';

echo $frm_str;