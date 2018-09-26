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
require_once('core/core_tables.php');
require_once('apps/maarch_entreprise/apps_tables.php');
require_once('modules/notes/notes_tables.php');
require_once('modules/notes/lang/fr.php');
$core = new core_tools();
$core->load_lang();
$db = new Database();
$db->query(
		"DELETE FROM " .APPS_CONTACTS_V2. " WHERE contact_id= ? ",array($_REQUEST['contact_id']));
$db->query(
		"DELETE FROM " .APPS_CONTACTS_ADDRESSES. " WHERE contact_id= ?", array($_REQUEST['contact_id']));

//$db->show();

header('Location:' . $_SESSION['config']['businessappurl']. 'smartphone/index.php?page=welcome');
?>