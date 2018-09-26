<?php
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (!isset($_SESSION['config']['corepath'])) {
    header('location: ../../../');
}
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
require_once('modules/basket/class/class_modules_tools.php');
$core = new core_tools();
$core->test_user();
$core->load_lang();

$s_id = $_REQUEST['id'];
$_SESSION['doc_id'] = $s_id;


$_SESSION['collection_id_choice'] = $_SESSION['current_basket']['coll_id'];
$whereRequest = " res_id_master = $s_id and attachment_type <> 'simple_attachement' and attachment_type <> 'converted_pdf' and status <> 'SIGN' and status <> 'DEL'";
$basket_label = _RESPONSE_PROJECTS;
$view = 'res_view_attachments';

include_once('apps/' . $_SESSION['config']['app_id']
        . '/smartphone/list_result.php'
    );