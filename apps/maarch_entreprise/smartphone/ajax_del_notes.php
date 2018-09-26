<?php
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (!isset($_SESSION['config']['corepath'])) {
    header('location: ../../../');
}
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
require_once('core/class/class_db_pdo.php');
require_once('core/class/class_security.php');
require_once('core/class/class_history.php');
require_once('modules/notes/notes_tables.php');
require_once('core/core_tables.php');
require_once('apps/maarch_entreprise/apps_tables.php');

$core = new core_tools();
$core->load_lang();

if (isset($_REQUEST['id'])) {
    $db = new Database();
    $sec = new security();
    $table = $sec->retrieve_table_from_coll($_REQUEST['collId']);
    $date = $db->current_datetime();
    $query = "DELETE FROM " . NOTES_TABLE . " WHERE id = ?";
    $db->query($query,array($_REQUEST['id']));
	
    $return['status'] = 1;
	
	echo json_encode($return);
} else {
    $return['status'] = 1;
	$return['msg'] = '';
	
	echo json_encode($return);
}